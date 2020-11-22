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

	/**
	 * @param \Doctrine\ORM\EntityManagerInterface                                  $em
	 * @param \SixtyEightPublishers\DoctrinePersistence\FinallyCallbackQueueInvoker $finallyCallbackQueueInvoker
	 */
	public function __construct(EntityManagerInterface $em, FinallyCallbackQueueInvoker $finallyCallbackQueueInvoker)
	{
		$this->em = $em;
		$this->finallyCallbackQueueInvoker = $finallyCallbackQueueInvoker;
	}

	/**
	 * {@inheritDoc}
	 */
	public function create(callable $callback, iterable $arguments = []): TransactionInterface
	{
		return new Transaction($this->em, $this->finallyCallbackQueueInvoker, $callback, $arguments);
	}
}
