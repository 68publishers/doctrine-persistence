<?php

declare(strict_types=1);

namespace SixtyEightPublishers\DoctrinePersistence\Context;

use Doctrine\ORM\EntityManagerInterface;
use SixtyEightPublishers\DoctrinePersistence\Badge\BadgeInterface;
use SixtyEightPublishers\DoctrinePersistence\Argument\ArgumentBagInterface;

trait CommonContextProxyTrait
{
	protected CommonContextInterface $commonContext;

	/**
	 * @return string
	 */
	public function getTransactionId(): string
	{
		return $this->commonContext->getTransactionId();
	}

	/**
	 * @return \SixtyEightPublishers\DoctrinePersistence\Argument\ArgumentBagInterface
	 */
	public function getArgumentBag(): ArgumentBagInterface
	{
		return $this->commonContext->getArgumentBag();
	}

	/**
	 * @return \Doctrine\ORM\EntityManagerInterface
	 */
	public function getEntityManager(): EntityManagerInterface
	{
		return $this->commonContext->getEntityManager();
	}


	/**
	 * @param \SixtyEightPublishers\DoctrinePersistence\Badge\BadgeInterface ...$badges
	 *
	 * @return void
	 */
	public function addBadges(BadgeInterface ...$badges): void
	{
		$this->commonContext->addBadges(...$badges);
	}

	/**
	 * @param string $badgeClassName
	 *
	 * @return \SixtyEightPublishers\DoctrinePersistence\Badge\BadgeInterface|NULL
	 */
	public function getLastBadge(string $badgeClassName): ?BadgeInterface
	{
		return $this->commonContext->getLastBadge($badgeClassName);
	}

	/**
	 * @param string|null $badgeClassName
	 *
	 * @return \SixtyEightPublishers\DoctrinePersistence\Badge\BadgeInterface[]
	 */
	public function getAllBadges(?string $badgeClassName = NULL): array
	{
		return $this->commonContext->getAllBadges($badgeClassName);
	}
}
