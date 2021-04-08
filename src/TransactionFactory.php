<?php

declare(strict_types=1);

namespace SixtyEightPublishers\DoctrinePersistence;

use Doctrine\ORM\EntityManagerInterface;

final class TransactionFactory implements TransactionFactoryInterface
{
	/** @var \Doctrine\ORM\EntityManagerInterface  */
	private $em;

	/** @var \SixtyEightPublishers\DoctrinePersistence\FinallyCallbackQueueInvoker  */
	private $finallyCallbackQueueInvoker;

	/** @var \SixtyEightPublishers\DoctrinePersistence\TransactionTrackerInterface  */
	private $transactionTracker;

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
	 * {@inheritDoc}
	 */
	public function create(callable $callback, iterable $arguments = []): TransactionInterface
	{
		return new Transaction($this->em, $this->finallyCallbackQueueInvoker, $this->transactionTracker, $callback, $arguments);
	}
}
