<?php

declare(strict_types=1);

namespace SixtyEightPublishers\DoctrinePersistence\Transaction;

use Nette;

final class ImmutableTransaction implements ITransaction
{
	use Nette\SmartObject;

	/** @var \SixtyEightPublishers\DoctrinePersistence\Transaction\Transaction  */
	private $transaction;

	/** @var array  */
	private $arguments;

	/**
	 * @param \SixtyEightPublishers\DoctrinePersistence\Transaction\Transaction $transaction
	 * @param array                                                             $arguments
	 */
	public function __construct(Transaction $transaction, array $arguments)
	{
		$this->transaction = $transaction;
		$this->arguments = $arguments;
	}

	/**************** interface \SixtyEightPublishers\DoctrinePersistence\Transaction\ITransaction ****************/

	/**
	 * {@inheritdoc}
	 */
	public function then(callable $callback): ITransaction
	{
		$this->transaction->then($callback);

		return $this;
	}
	/**
	 * {@inheritdoc}
	 */
	public function done(callable $callback): ITransaction
	{
		$this->transaction->done($callback);

		return $this;
	}

	/**
	 * {@inheritdoc}
	 */
	public function catch(string $exceptionClass, callable $callback): ITransaction
	{
		$this->transaction->catch($exceptionClass, $callback);

		return $this;
	}

	/**
	 * {@inheritdoc}
	 */
	public function error(callable $callback): ITransaction
	{
		$this->transaction->error($callback);

		return $this;
	}

	/**
	 * {@inheritdoc}
	 */
	public function finally(callable $callback): ITransaction
	{
		$this->transaction->finally($callback);

		return $this;
	}

	/**
	 * {@inheritdoc}
	 */
	public function run()
	{
		return $this->transaction->run(...$this->arguments);
	}

	/**
	 * {@inheritdoc}
	 */
	public function __invoke()
	{
		return $this->run();
	}
}
