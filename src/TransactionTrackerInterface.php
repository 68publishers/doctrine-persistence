<?php

declare(strict_types=1);

namespace SixtyEightPublishers\DoctrinePersistence;

use Countable;
use IteratorAggregate;

interface TransactionTrackerInterface extends IteratorAggregate, Countable
{
	/**
	 * @internal
	 *
	 * @param \SixtyEightPublishers\DoctrinePersistence\TransactionInterface $transaction
	 */
	public function track(TransactionInterface $transaction): void;

	/**
	 * @return bool
	 */
	public function hasActiveTransaction(): bool;

	/**
	 * @return \SixtyEightPublishers\DoctrinePersistence\TransactionInterface
	 */
	public function getCurrentTransaction(): TransactionInterface;
}
