<?php

declare(strict_types=1);

namespace SixtyEightPublishers\DoctrinePersistence;

use ArrayIterator;
use SixtyEightPublishers\DoctrinePersistence\Badge\BadgeBag;
use SixtyEightPublishers\DoctrinePersistence\Badge\BadgeBagInterface;
use SixtyEightPublishers\DoctrinePersistence\Exception\RuntimeException;
use SixtyEightPublishers\DoctrinePersistence\Exception\InvalidArgumentException;

final class TransactionTracker implements TransactionTrackerInterface
{
	/** @var \SixtyEightPublishers\DoctrinePersistence\TransactionInterface[] */
	private array $transactions = [];

	private ?BadgeBagInterface $currentBadgeBag = NULL;

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
	public function track(TransactionInterface $transaction): BadgeBagInterface
	{
		$hash = $transaction->getId();

		if (isset($this->transactions[$hash])) {
			throw new InvalidArgumentException(sprintf(
				'Transaction with hash %s is already tracked.',
				$hash
			));
		}

		$transaction->finally(function () use ($hash): void {
			unset($this->transactions[$hash]);

			if (empty($this->transactions)) {
				$this->currentBadgeBag = NULL;
			}
		});

		if (empty($this->transactions) || NULL === $this->currentBadgeBag) {
			$this->currentBadgeBag = new BadgeBag();
		}

		$this->transactions[$hash] = $transaction;

		return $this->currentBadgeBag;
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

	/**
	 * {@inheritDoc}
	 */
	public function getTransaction(string $id): TransactionInterface
	{
		if (!isset($this->transactions[$id])) {
			throw new InvalidArgumentException(sprintf(
				'Transaction with ID %s is not tracked.',
				$id
			));
		}

		return $this->transactions[$id];
	}
}
