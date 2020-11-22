<?php

declare(strict_types=1);

namespace SixtyEightPublishers\DoctrinePersistence;

interface TransactionFactoryInterface
{
	/**
	 * @param callable $callback
	 * @param iterable $arguments
	 *
	 * @return \SixtyEightPublishers\DoctrinePersistence\TransactionInterface
	 */
	public function create(callable $callback, iterable $arguments = []): TransactionInterface;
}
