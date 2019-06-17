<?php

declare(strict_types=1);

namespace SixtyEightPublishers\DoctrinePersistence\Transaction;

interface ITransaction
{
	/**
	 * @param callable $callback
	 *
	 * @return \SixtyEightPublishers\DoctrinePersistence\Transaction\ITransaction
	 */
	public function then(callable $callback): ITransaction;

	/**
	 * @param callable $callback
	 *
	 * @return \SixtyEightPublishers\DoctrinePersistence\Transaction\ITransaction
	 */
	public function done(callable $callback): ITransaction;

	/**
	 * @param callable $callback
	 *
	 * @return \SixtyEightPublishers\DoctrinePersistence\Transaction\ITransaction
	 */
	public function error(callable $callback): ITransaction;

	/**
	 * @param callable $callback
	 *
	 * @return \SixtyEightPublishers\DoctrinePersistence\Transaction\ITransaction
	 */
	public function finally(callable $callback): ITransaction;

	/**
	 * @return mixed
	 */
	public function run();

	/**
	 * Calls ::run() internally
	 *
	 * @return mixed
	 */
	public function __invoke();
}
