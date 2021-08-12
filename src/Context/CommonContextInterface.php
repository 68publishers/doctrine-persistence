<?php

declare(strict_types=1);

namespace SixtyEightPublishers\DoctrinePersistence\Context;

use Doctrine\ORM\EntityManagerInterface;
use SixtyEightPublishers\DoctrinePersistence\Badge\BadgeInterface;
use SixtyEightPublishers\DoctrinePersistence\Argument\ArgumentBagInterface;

interface CommonContextInterface
{
	/**
	 * @return \SixtyEightPublishers\DoctrinePersistence\Argument\ArgumentBagInterface
	 */
	public function getArgumentBag(): ArgumentBagInterface;

	/**
	 * @return \Doctrine\ORM\EntityManagerInterface
	 */
	public function getEntityManager(): EntityManagerInterface;

	/**
	 * @param \SixtyEightPublishers\DoctrinePersistence\Badge\BadgeInterface ...$badges
	 *
	 * @return void
	 */
	public function addBadges(BadgeInterface ...$badges): void;

	/**
	 * @param string $badgeClassName
	 *
	 * @return \SixtyEightPublishers\DoctrinePersistence\Badge\BadgeInterface|NULL
	 */
	public function getLastBadge(string $badgeClassName): ?BadgeInterface;

	/**
	 * @param string|null $badgeClassName
	 *
	 * @return \SixtyEightPublishers\DoctrinePersistence\Badge\BadgeInterface[]
	 */
	public function getAllBadges(?string $badgeClassName = NULL): array;
}
