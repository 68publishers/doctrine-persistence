<?php

declare(strict_types=1);

namespace SixtyEightPublishers\DoctrinePersistence\Badge;

interface BadgeBagInterface
{
	/**
	 * @param \SixtyEightPublishers\DoctrinePersistence\Badge\BadgeInterface ...$badges
	 *
	 * @return \SixtyEightPublishers\DoctrinePersistence\Badge\BadgeBagInterface
	 */
	public function with(BadgeInterface ...$badges): self;

	/**
	 * @param \SixtyEightPublishers\DoctrinePersistence\Badge\BadgeInterface ...$badges
	 *
	 * @return \SixtyEightPublishers\DoctrinePersistence\Badge\BadgeBagInterface
	 */
	public function add(BadgeInterface ...$badges): self;

	/**
	 * @param string $badgeClassName
	 *
	 * @return \SixtyEightPublishers\DoctrinePersistence\Badge\BadgeInterface|null
	 */
	public function last(string $badgeClassName): ?BadgeInterface;

	/**
	 * @param string|NULL $badgeClassName
	 *
	 * @return \SixtyEightPublishers\DoctrinePersistence\Badge\BadgeInterface[]
	 */
	public function all(?string $badgeClassName = NULL): array;
}
