<?php

declare(strict_types=1);

namespace SixtyEightPublishers\DoctrinePersistence;

use Doctrine\ORM\EntityManagerInterface;

final class TransactionFactory implements TransactionFactoryInterface
{
	private EntityManagerInterface $em;

	private FinallyCallbackQueueInvoker $finallyCallbackQueueInvoker;

	private TransactionTrackerInterface $transactionTracker;

	private array $transactionsExtenders = [];

	/**
	 * @param \Doctrine\ORM\EntityManagerInterface                                  $em
	 * @param \SixtyEightPublishers\DoctrinePersistence\FinallyCallbackQueueInvoker $finallyCallbackQueueInvoker
	 * @param \SixtyEightPublishers\DoctrinePersistence\TransactionTrackerInterface $transactionTracker
	 */
	public function __construct(EntityManagerInterface $em, FinallyCallbackQueueInvoker $finallyCallbackQueueInvoker, TransactionTrackerInterface $transactionTracker)
	{
		$this->em = $em;
		$this->finallyCallbackQueueInvoker = $finallyCallbackQueueInvoker;
		$this->transactionTracker = $transactionTracker;
	}

	/**
	 * @param callable $callback
	 * @param int      $priority
	 *
	 * @return void
	 */
	public function addTransactionExtender(callable $callback, int $priority = 0): void
	{
		$this->transactionsExtenders[$priority][] = $callback;
	}

	/**
	 * {@inheritDoc}
	 */
	public function create(callable $callback, iterable $arguments = []): TransactionInterface
	{
		$transaction = new Transaction($this->em, $this->finallyCallbackQueueInvoker, $this->transactionTracker, $callback, $arguments);

		$groupedExtenders = $this->transactionsExtenders;

		krsort($groupedExtenders);

		foreach ($groupedExtenders as $extenders) {
			foreach ($extenders as $extender) {
				$extender($transaction);
			}
		}

		return $transaction;
	}
}
