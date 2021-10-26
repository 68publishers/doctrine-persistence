<?php

declare(strict_types=1);

namespace SixtyEightPublishers\DoctrinePersistence;

use Throwable;
use Doctrine\ORM\Events;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\PostFlushEventArgs;
use SixtyEightPublishers\DoctrinePersistence\Argument\ArgumentBag;
use SixtyEightPublishers\DoctrinePersistence\Context\ErrorContext;
use SixtyEightPublishers\DoctrinePersistence\Context\CommonContext;
use SixtyEightPublishers\DoctrinePersistence\Context\FinallyContext;
use SixtyEightPublishers\DoctrinePersistence\Context\SuccessContext;
use SixtyEightPublishers\DoctrinePersistence\Helper\CallbackInvoker;
use SixtyEightPublishers\DoctrinePersistence\Helper\TransactionHelper;
use SixtyEightPublishers\DoctrinePersistence\Context\TransactionContext;
use SixtyEightPublishers\DoctrinePersistence\Exception\RuntimeException;
use SixtyEightPublishers\DoctrinePersistence\Argument\ArgumentBagInterface;
use SixtyEightPublishers\DoctrinePersistence\Context\ErrorContextInterface;
use SixtyEightPublishers\DoctrinePersistence\Context\CommonContextInterface;
use SixtyEightPublishers\DoctrinePersistence\Context\TransactionContextInterface;
use SixtyEightPublishers\DoctrinePersistence\Exception\TransactionUncatchableExceptionInterface;

final class Transaction implements TransactionInterface
{
	private EntityManagerInterface $em;

	private FinallyCallbackQueueInvoker $finallyCallbackQueueInvoker;

	private TransactionTrackerInterface $transactionTracker;

	private iterable $arguments;

	/** @var callable[]  */
	private array $callbacks;

	/** @var callable[]  */
	private array $successCallbacks = [];

	/** @var callable[]  */
	private array $errorCallbacks = [];

	/** @var callable[]  */
	private array $finallyCallbacks = [];

	private bool $executed = FALSE;

	/**
	 * @param \Doctrine\ORM\EntityManagerInterface                                  $em
	 * @param \SixtyEightPublishers\DoctrinePersistence\FinallyCallbackQueueInvoker $finallyCallbackQueueInvoker
	 * @param \SixtyEightPublishers\DoctrinePersistence\TransactionTrackerInterface $transactionTracker
	 * @param callable                                                              $callback
	 * @param iterable                                                              $arguments
	 */
	public function __construct(EntityManagerInterface $em, FinallyCallbackQueueInvoker $finallyCallbackQueueInvoker, TransactionTrackerInterface $transactionTracker, callable $callback, iterable $arguments = [])
	{
		$this->em = $em;
		$this->finallyCallbackQueueInvoker = $finallyCallbackQueueInvoker;
		$this->transactionTracker = $transactionTracker;
		$this->callbacks = [$callback];
		$this->arguments = $arguments;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getId(): string
	{
		return spl_object_hash($this);
	}

	/**
	 * {@inheritDoc}
	 */
	public function withArguments(iterable $arguments): TransactionInterface
	{
		$callbacks = $this->callbacks;
		$firstCallback = array_shift($callbacks);

		$transaction = new static($this->em, $this->finallyCallbackQueueInvoker, $this->transactionTracker, $firstCallback, $arguments);

		foreach ($callbacks as $callback) {
			$transaction->then($callback);
		}

		foreach ($this->successCallbacks as $successCallback) {
			$transaction->success($successCallback);
		}

		foreach ($this->errorCallbacks as $errorCallback) {
			$transaction->error($errorCallback);
		}

		foreach ($this->finallyCallbacks as $finallyCallback) {
			$transaction->finally($finallyCallback);
		}

		return $transaction;
	}

	/**
	 * {@inheritDoc}
	 */
	public function then(callable $callback): TransactionInterface
	{
		$this->callbacks[] = $callback;

		return $this;
	}

	/**
	 * {@inheritDoc}
	 */
	public function success(callable $callback): TransactionInterface
	{
		$this->successCallbacks[] = $callback;

		return $this;
	}

	/**
	 * {@inheritDoc}
	 */
	public function error(callable $callback): TransactionInterface
	{
		$this->errorCallbacks[] = $callback;

		return $this;
	}

	/**
	 * {@inheritDoc}
	 */
	public function finally(callable $callback): TransactionInterface
	{
		$this->finallyCallbacks[] = $callback;

		return $this;
	}

	/**
	 * {@inheritDoc}
	 *
	 * @throws \Throwable
	 */
	public function run()
	{
		if (TRUE === $this->executed) {
			throw RuntimeException::transactionAlreadyExecuted();
		}

		$badgeBag = $this->transactionTracker->track($this);
		$namedArgumentBag = $this->arguments instanceof ArgumentBagInterface ? $this->arguments : new ArgumentBag($this->arguments);
		$commonContext = new CommonContext($this->getId(), $this->em, $namedArgumentBag, $badgeBag);

		$typeHintedArgumentBag = new ArgumentBag([
			EntityManagerInterface::class => $this->em,
			EntityManager::class => $this->em,
		]);

		if ($namedArgumentBag->has(self::ARGUMENT_NAME_RESULT)) {
			throw RuntimeException::reservedArgumentNameUsage(self::ARGUMENT_NAME_RESULT);
		}

		$this->executed = TRUE;
		$result = NULL;

		$this->em->getConnection()->beginTransaction();

		try {
			$result = $this->processTransaction($namedArgumentBag, $typeHintedArgumentBag, $commonContext);
		} catch (TransactionUncatchableExceptionInterface $e) {
			throw $e;
		} catch (Throwable $e) {
			$result = NULL;
			$errorContext = $this->processError($commonContext, $e);

			if (!$errorContext->isDefaultBehaviourPrevented()) {
				throw $errorContext->getError();
			}
		} finally {
			$this->processFinally($result, $commonContext, $errorContext ?? NULL);
		}

		return $result;
	}

	/**
	 * {@inheritDoc}
	 *
	 * @throws \Throwable
	 */
	public function __invoke()
	{
		return $this->run();
	}

	/**
	 * @param \SixtyEightPublishers\DoctrinePersistence\Argument\ArgumentBagInterface  $namedArgumentBag
	 * @param \SixtyEightPublishers\DoctrinePersistence\Argument\ArgumentBagInterface  $typeHintedArgumentBag
	 * @param \SixtyEightPublishers\DoctrinePersistence\Context\CommonContextInterface $commonContext
	 *
	 * @return mixed
	 * @throws \Doctrine\DBAL\ConnectionException
	 */
	private function processTransaction(ArgumentBagInterface $namedArgumentBag, ArgumentBagInterface $typeHintedArgumentBag, CommonContextInterface $commonContext)
	{
		$result = NULL;

		foreach ($this->callbacks as $callback) {
			$transactionContext = new TransactionContext($commonContext, $result);

			$namedArgumentBag = $namedArgumentBag->withArguments([
				self::ARGUMENT_NAME_RESULT => $result,
			], TRUE);

			$typeHintedArgumentBag = $typeHintedArgumentBag->withArguments([
				TransactionContextInterface::class => $transactionContext,
				TransactionContext::class => $transactionContext,
			], TRUE);

			$result = CallbackInvoker::invokeTransactionCallback($callback, $namedArgumentBag, $typeHintedArgumentBag);
		}

		$this->em->flush();
		$this->em->getConnection()->commit();

		foreach ($this->successCallbacks as $successCallback) {
			$successCallback(new SuccessContext($commonContext, $result), $result);
		}

		return $result;
	}

	/**
	 * @param \SixtyEightPublishers\DoctrinePersistence\Context\CommonContextInterface $commonContext
	 * @param \Throwable                                                               $e
	 *
	 * @return \SixtyEightPublishers\DoctrinePersistence\Context\ErrorContextInterface
	 * @throws \Doctrine\DBAL\ConnectionException
	 * @throws \Throwable
	 */
	private function processError(CommonContextInterface $commonContext, Throwable $e): ErrorContextInterface
	{
		$connection = $this->em->getConnection();

		if ($connection->isTransactionActive()) {
			$this->em->close();
			$connection->rollBack();
		}

		$errorContext = new ErrorContext($commonContext, $e);

		try {
			foreach ($this->errorCallbacks as $errorCallback) {
				if ($errorContext->isPropagationStopped()) {
					break;
				}

				CallbackInvoker::tryInvokeErrorCallback($errorCallback, $errorContext);
			}
		} catch (Throwable $e) {
			$errorContext = new ErrorContext($commonContext, $e);
		}

		return $errorContext;
	}

	/**
	 * @param mixed                                                                        $result
	 * @param \SixtyEightPublishers\DoctrinePersistence\Context\CommonContextInterface     $commonContext
	 * @param \SixtyEightPublishers\DoctrinePersistence\Context\ErrorContextInterface|null $errorContext
	 *
	 * @return void
	 */
	public function processFinally($result, CommonContextInterface $commonContext, ?ErrorContextInterface $errorContext): void
	{
		$evm = $this->em->getEventManager();

		if (TransactionHelper::isEverythingCommitted($this->em) && $evm->hasListeners(Events::postFlush)) {
			$evm->dispatchEvent(Events::postFlush, new PostFlushEventArgs($this->em));
		}

		$lastError = NULL !== $errorContext ? $errorContext->getError() : NULL;
		$finallyContext = new FinallyContext($commonContext, $result, $lastError);

		foreach ($this->finallyCallbacks as $finallyCallback) {
			$this->finallyCallbackQueueInvoker->enqueue($finallyCallback, $finallyContext);
		}

		$this->finallyCallbackQueueInvoker->invoke($lastError);
	}
}
