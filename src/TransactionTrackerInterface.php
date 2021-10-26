<?php

declare(strict_types=1);

namespace SixtyEightPublishers\DoctrinePersistence;

use Countable;
use IteratorAggregate;
use SixtyEightPublishers\DoctrinePersistence\Badge\BadgeBagInterface;

interface TransactionTrackerInterface extends IteratorAggregate, Countable
{
	/**
	 * @internal
	 *
	 * @param \SixtyEightPublishers\DoctrinePersistence\TransactionInterface $transaction
	 *
	 * @return \SixtyEightPublishers\DoctrinePersistence\Badge\BadgeBagInterface
	 */
	public function track(TransactionInterface $transaction): BadgeBagInterface;

	/**
	 * @return bool
	 */
	public function hasActiveTransaction(): bool;

	/**
	 * @return \SixtyEightPublishers\DoctrinePersistence\TransactionInterface
	 */
	public function getCurrentTransaction(): TransactionInterface;

	/**
	 * @param string $id
	 *
	 * @return \SixtyEightPublishers\DoctrinePersistence\TransactionInterface
	 * @throws \SixtyEightPublishers\DoctrinePersistence\Exception\InvalidArgumentException
	 */
	public function getTransaction(string $id): TransactionInterface;
}
