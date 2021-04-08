<?php

declare(strict_types=1);

namespace SixtyEightPublishers\DoctrinePersistence;

use ArrayIterator;
use SixtyEightPublishers\DoctrinePersistence\Exception\RuntimeException;
use SixtyEightPublishers\DoctrinePersistence\Exception\InvalidArgumentException;

final class TransactionTracker implements TransactionTrackerInterface
{
	/** @var \SixtyEightPublishers\DoctrinePersistence\TransactionInterface[] */
	private $transactions = [];

	/**
	 * {@inheritDoc}
	 */
	public function getIterator(): ArrayIterator
	{
		return new ArrayIterator($this->transactions);
	}

	/**
	 * {@inheritDoc}
	 */
	public function count(): int
	{
		return count($this->transactions);
	}

	/**
	 * {@inheritDoc}
	 *
	 * @throws \SixtyEightPublishers\DoctrinePersistence\Exception\InvalidArgumentException
	 */
	public function track(TransactionInterface $transaction): void
	{
		$hash = spl_object_hash($transaction);

		if (isset($this->transactions[$hash])) {
			throw new InvalidArgumentException(sprintf(
				'Transaction with hash %s is already tracked.',
				$hash
			));
		}

		$transaction->finally(function () use ($hash): void {
			unset($this->transactions[$hash]);
		});

		$this->transactions[$hash] = $transaction;
	}

	/**
	 * {@inheritDoc}
	 */
	public function hasActiveTransaction(): bool
	{
		return !empty($this->transactions);
	}

	/**
	 * {@inheritDoc}
	 *
	 * @throws \SixtyEightPublishers\DoctrinePersistence\Exception\RuntimeException
	 */
	public function getCurrentTransaction(): TransactionInterface
	{
		if (empty($this->transactions)) {
			throw RuntimeException::noActiveTransaction();
		}

		return end($this->transactions);
	}
}
