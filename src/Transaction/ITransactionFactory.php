<?php

declare(strict_types=1);

namespace SixtyEightPublishers\DoctrinePersistence\Transaction;

interface ITransactionFactory
{
	/**
	 * First parameter is EntityManagerInterface, another parameters are passed by calling ITransaction::run(...args).
	 *
	 * @param callable $callback
	 *
	 * @return \SixtyEightPublishers\DoctrinePersistence\Transaction\Transaction
	 */
	public function create(callable $callback): Transaction;
}
