<?php

declare(strict_types=1);

namespace SixtyEightPublishers\DoctrinePersistence\Transaction;

interface ITransaction
{
	/**
	 * Adds new callback into transaction.
	 * First parameter in EntityManagerInterface.
	 * Second is value returned from first (initial) callback.
	 * Another parameters are passed by calling ITransaction::run(...args).
	 *
	 * @param callable $callback
	 *
	 * @return \SixtyEightPublishers\DoctrinePersistence\Transaction\ITransaction
	 */
	public function then(callable $callback): ITransaction;

	/**
	 * This callback is called when current transaction successfully ends, but everything may not be committed if current transaction is wrapped into another transaction.
	 * The only parameter is value returned from first (initial) callback.
	 *
	 * @param callable $callback
	 *
	 * @return \SixtyEightPublishers\DoctrinePersistence\Transaction\ITransaction
	 */
	public function done(callable $callback): ITransaction;

	/**
	 * This callback is invoked when specified Exception is thrown.
	 * The only parameter is caught exception.
	 *
	 * @param string   $exceptionClass
	 * @param callable $callback
	 *
	 * @return \SixtyEightPublishers\DoctrinePersistence\Transaction\ITransaction
	 */
	public function catch(string $exceptionClass, callable $callback): ITransaction;

	/**
	 * This callback is invoked when some Exception is caught.
	 * The only parameter is exception PersistenceException, original exception is accessible via $e->getPrevious().
	 *
	 * @param callable $callback
	 *
	 * @return \SixtyEightPublishers\DoctrinePersistence\Transaction\ITransaction
	 */
	public function error(callable $callback): ITransaction;

	/**
	 * This callback is invoked when everything is committed including nested/parent transactions.
	 * No parameters here.
	 *
	 * @param callable $callback
	 *
	 * @return \SixtyEightPublishers\DoctrinePersistence\Transaction\ITransaction
	 */
	public function finally(callable $callback): ITransaction;

	/**
	 * Runs transaction, each transaction instance can be invoked only once.
	 *
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
