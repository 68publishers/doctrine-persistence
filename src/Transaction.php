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
use SixtyEightPublishers\DoctrinePersistence\Context\FinallyContext;
use SixtyEightPublishers\DoctrinePersistence\Helper\CallbackInvoker;
use SixtyEightPublishers\DoctrinePersistence\Helper\TransactionHelper;
use SixtyEightPublishers\DoctrinePersistence\Exception\RuntimeException;
use SixtyEightPublishers\DoctrinePersistence\Argument\ArgumentBagInterface;
use SixtyEightPublishers\DoctrinePersistence\Context\ErrorContextInterface;
use SixtyEightPublishers\DoctrinePersistence\Exception\TransactionUncatchableExceptionInterface;

final class Transaction implements TransactionInterface
{
	/** @var \Doctrine\ORM\EntityManagerInterface  */
	private $em;

	/** @var \SixtyEightPublishers\DoctrinePersistence\FinallyCallbackQueueInvoker  */
	private $finallyCallbackQueueInvoker;

	/** @var \SixtyEightPublishers\DoctrinePersistence\TransactionTrackerInterface  */
	private $transactionTracker;

	/** @var iterable  */
	private $arguments;

	/** @var callable[]  */
	private $callbacks;

	/** @var callable[]  */
	private $successCallbacks = [];

	/** @var callable[]  */
	private $errorCallbacks = [];

	/** @var callable[]  */
	private $finallyCallbacks = [];

	/** @var bool  */
	private $executed = FALSE;

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

		$namedArgumentBag = $this->arguments instanceof ArgumentBagInterface ? $this->arguments : new ArgumentBag($this->arguments);
		$typeHintedArgumentBag = new ArgumentBag([
			EntityManagerInterface::class => $this->em,
			EntityManager::class => $this->em,
		]);

		if ($namedArgumentBag->has(self::ARGUMENT_NAME_RESULT)) {
			throw RuntimeException::reservedArgumentNameUsage(self::ARGUMENT_NAME_RESULT);
		}

		$this->executed = TRUE;
		$result = NULL;

		$this->transactionTracker->track($this);
		$this->em->getConnection()->beginTransaction();

		try {
			$result = $this->processTransaction($namedArgumentBag, $typeHintedArgumentBag);
		} catch (TransactionUncatchableExceptionInterface $e) {
			throw $e;
		} catch (Throwable $e) {
			$result = NULL;
			$errorContext = $this->processError($e);
		} finally {
			$this->processFinally($result, $errorContext ?? NULL);
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
	 * @param \SixtyEightPublishers\DoctrinePersistence\Argument\ArgumentBagInterface $namedArgumentBag
	 * @param \SixtyEightPublishers\DoctrinePersistence\Argument\ArgumentBagInterface $typeHintedArgumentBag
	 *
	 * @return mixed
	 * @throws \Doctrine\DBAL\ConnectionException
	 */
	private function processTransaction(ArgumentBagInterface $namedArgumentBag, ArgumentBagInterface $typeHintedArgumentBag)
	{
		$result = NULL;

		foreach ($this->callbacks as $callback) {
			$namedArgumentBag = $namedArgumentBag->withArguments([
				self::ARGUMENT_NAME_RESULT => $result,
			], TRUE);

			$result = CallbackInvoker::invokeTransactionCallback($callback, $namedArgumentBag, $typeHintedArgumentBag);
		}

		$this->em->flush();
		$this->em->getConnection()->commit();

		foreach ($this->successCallbacks as $successCallback) {
			$successCallback($result);
		}

		return $result;
	}

	/**
	 * @param \Throwable $e
	 *
	 * @return \SixtyEightPublishers\DoctrinePersistence\Context\ErrorContextInterface
	 * @throws \Doctrine\DBAL\ConnectionException
	 * @throws \Throwable
	 */
	private function processError(Throwable $e): ErrorContextInterface
	{
		$connection = $this->em->getConnection();

		if ($connection->isTransactionActive()) {
			$this->em->close();
			$connection->rollBack();
		}

		$errorContext = new ErrorContext($e);

		try {
			foreach ($this->errorCallbacks as $errorCallback) {
				if ($errorContext->isPropagationStopped()) {
					break;
				}

				CallbackInvoker::tryInvokeErrorCallback($errorCallback, $errorContext);
			}
		} catch (Throwable $e) {
			$errorContext = new ErrorContext($e);
		}

		if (!$errorContext->isDefaultBehaviourPrevented()) {
			throw $errorContext->getError();
		}

		return $errorContext;
	}

	/**
	 * @param mixed                                                                        $result
	 * @param \SixtyEightPublishers\DoctrinePersistence\Context\ErrorContextInterface|NULL $errorContext
	 *
	 * @return void
	 */
	public function processFinally($result, ?ErrorContextInterface $errorContext): void
	{
		$evm = $this->em->getEventManager();

		if (TransactionHelper::isEverythingCommitted($this->em) && $evm->hasListeners(Events::postFlush)) {
			$evm->dispatchEvent(Events::postFlush, new PostFlushEventArgs($this->em));
		}

		$lastError = NULL !== $errorContext ? $errorContext->getError() : NULL;
		$finallyContext = new FinallyContext($this->em->getConnection(), $result, $lastError);

		foreach ($this->finallyCallbacks as $finallyCallback) {
			$this->finallyCallbackQueueInvoker->enqueue($finallyCallback, $finallyContext);
		}

		$this->finallyCallbackQueueInvoker->invoke($lastError);
	}
}
