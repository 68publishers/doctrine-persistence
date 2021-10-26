<?php

declare(strict_types=1);

namespace SixtyEightPublishers\DoctrinePersistence\Context;

use Doctrine\ORM\EntityManagerInterface;
use SixtyEightPublishers\DoctrinePersistence\Badge\BadgeInterface;
use SixtyEightPublishers\DoctrinePersistence\Badge\BadgeBagInterface;
use SixtyEightPublishers\DoctrinePersistence\Argument\ArgumentBagInterface;

final class CommonContext implements CommonContextInterface
{
	private string $transactionId;

	private EntityManagerInterface $em;

	private ArgumentBagInterface $argumentBag;

	private BadgeBagInterface $badgeBag;

	/**
	 * @param string                                                                  $transactionId
	 * @param \Doctrine\ORM\EntityManagerInterface                                    $em
	 * @param \SixtyEightPublishers\DoctrinePersistence\Argument\ArgumentBagInterface $argumentBag
	 * @param \SixtyEightPublishers\DoctrinePersistence\Badge\BadgeBagInterface       $badgeBag
	 */
	public function __construct(string $transactionId, EntityManagerInterface $em, ArgumentBagInterface $argumentBag, BadgeBagInterface $badgeBag)
	{
		$this->transactionId = $transactionId;
		$this->em = $em;
		$this->argumentBag = $argumentBag;
		$this->badgeBag = $badgeBag;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getTransactionId(): string
	{
		return $this->transactionId;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getArgumentBag(): ArgumentBagInterface
	{
		return $this->argumentBag;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getEntityManager(): EntityManagerInterface
	{
		return $this->em;
	}

	/**
	 * {@inheritDoc}
	 */
	public function addBadges(BadgeInterface ...$badges): void
	{
		$this->badgeBag->add(...$badges);
	}

	/**
	 * {@inheritDoc}
	 */
	public function getLastBadge(string $badgeClassName): ?BadgeInterface
	{
		return $this->badgeBag->last($badgeClassName);
	}

	/**
	 * {@inheritDoc}
	 */
	public function getAllBadges(?string $badgeClassName = NULL): array
	{
		return $this->badgeBag->all($badgeClassName);
	}
}
