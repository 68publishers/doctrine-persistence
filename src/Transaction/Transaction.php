<?php

declare(strict_types=1);

namespace SixtyEightPublishers\DoctrinePersistence\Transaction;

use Nette;
use Doctrine;
use SixtyEightPublishers;

/**
 * @method void onFinally($result)
 * @method void onDone($result)
 * @method void onError(\Throwable $e)
 */
final class Transaction implements ITransaction
{
	use Nette\SmartObject;

	/** @var array  */
	private static $transactionCallbacks = [];

	/** @var \Doctrine\ORM\EntityManagerInterface  */
	private $em;

	/** @var bool  */
	private $executed = FALSE;

	/** @var NULL|mixed */
	private $result;

	/** @var callable[] */
	private $callbacks = [];

	/** @var callable[] */
	private $catchCallbacks = [];

	/**
	 * Called after transaction's rollback. The Exception is thrown anyway after this callbacks.
	 *
	 * @var callable[]|NULL
	 */
	public $onError;

	/**
	 * Called at the end of transaction. Data may not be committed at this point if the transaction is nested.
	 *
	 * @var callable[]|NULL
	 */
	public $onDone;

	/**
	 * Called after successful data saving if transaction's nesting level is 0 => Everything is committed at this point.
	 *
	 * @var callable[]|NULL
	 */
	public $onFinally;

	/**
	 * @param callable                             $callback
	 * @param \Doctrine\ORM\EntityManagerInterface $em
	 */
	public function __construct(callable $callback, Doctrine\ORM\EntityManagerInterface $em)
	{
		$this->callbacks[] = $callback;
		$this->em = $em;
	}

	/**
	 * @param mixed ...$args
	 *
	 * @return \SixtyEightPublishers\DoctrinePersistence\Transaction\ImmutableTransaction
	 */
	public function immutable(...$args): ImmutableTransaction
	{
		return new ImmutableTransaction($this, $args);
	}

	/**
	 * @return bool|mixed
	 * @throws \Throwable
	 */
	public function __invoke()
	{
		return $this->run(...func_get_args());
	}

	/**
	 * @return void
	 */
	private function dispatchLastPostFlushEvent(): void
	{
		# save FINALLY callback
		self::$transactionCallbacks[] = function () {
			$this->onFinally($this->result);
		};

		if (FALSE === Helper::isEverythingCommitted($this->em)) {
			return;
		}

		$evm = $this->em->getEventManager();
		if ($evm->hasListeners(Doctrine\ORM\Events::postFlush)) {
			$evm->dispatchEvent(
				Doctrine\ORM\Events::postFlush,
				new Doctrine\ORM\Event\PostFlushEventArgs($this->em)
			);
		}

		while (0 < count(self::$transactionCallbacks)) {
			$callback = array_shift(self::$transactionCallbacks);

			$callback();
		}
	}

	/**
	 * @param \Throwable $e
	 *
	 * @return bool
	 */
	private function dispatchCatchCallbacks(\Throwable $e): bool
	{
		$processed = FALSE;

		foreach ($this->catchCallbacks as $className => $callbacks) {
			if (!$e instanceof $className) {
				continue;
			}

			foreach ($callbacks as $callback) {
				$callback($e);
			}

			$processed = TRUE;
		}

		return $processed;
	}

	/**************** interface \SixtyEightPublishers\DoctrinePersistence\Transaction\ITransaction ****************/

	/**
	 * {@inheritdoc}
	 */
	public function then(callable $callback): ITransaction
	{
		$this->callbacks[] = $callback;

		return $this;
	}

	/**
	 * {@inheritdoc}
	 */
	public function catch(string $exceptionClass, callable $callback): ITransaction
	{
		$this->catchCallbacks[$exceptionClass][] = $callback;

		return $this;
	}

	/**
	 * {@inheritdoc}
	 */
	public function error(callable $callback): ITransaction
	{
		$this->onError[] = $callback;

		return $this;
	}

	/**
	 * {@inheritdoc}
	 */
	public function done(callable $callback): ITransaction
	{
		$this->onDone[] = $callback;

		return $this;
	}

	/**
	 * {@inheritdoc}
	 */
	public function finally(callable $callback): ITransaction
	{
		$this->onFinally[] = $callback;

		return $this;
	}

	/**
	 * Pass args into this method
	 *
	 * {@inheritdoc}
	 */
	public function run()
	{
		$args = func_get_args();

		if (TRUE === $this->executed) {
			throw new SixtyEightPublishers\DoctrinePersistence\Exception\RuntimeException(sprintf(
				'Transaction was already executed.'
			));
		}

		$connection = $this->em->getConnection();
		$this->executed = TRUE;

		array_unshift($args, $this->em);
		$connection->beginTransaction();

		try {
			$callback = array_shift($this->callbacks);
			$this->result = $callback(...$args);

			array_splice($args, 1, 0, [ $this->result ]);

			while (0 < count($this->callbacks)) {
				$callback = array_shift($this->callbacks);

				$callback(...$args);
			}

			$this->em->flush();
			$connection->commit();
		} catch (\Throwable $e) {
			$this->em->close();
			$connection->rollBack();

			self::$transactionCallbacks = [];
			$exceptionAlreadyProcessed = $this->dispatchCatchCallbacks($e);

			if (!$e instanceof SixtyEightPublishers\DoctrinePersistence\Exception\PersistenceException) {
				$e = new SixtyEightPublishers\DoctrinePersistence\Exception\PersistenceException('Persistence failed.', $e->getCode(), $e);
			}

			$e->setAlreadyProcessed($exceptionAlreadyProcessed);
			$this->onError($e);

			throw $e;
		}

		$this->onDone($this->result);
		$this->dispatchLastPostFlushEvent();

		return $this->result;
	}
}
