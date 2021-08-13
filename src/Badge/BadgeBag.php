<?php

declare(strict_types=1);

namespace SixtyEightPublishers\DoctrinePersistence\Badge;

class BadgeBag implements BadgeBagInterface
{
	private array $badges = [];

	/**
	 * {@inheritDoc}
	 */
	public function with(BadgeInterface ...$badges): BadgeBagInterface
	{
		$replica = clone $this;

		foreach ($badges as $badge) {
			$replica->badges[get_class($badge)][] = $badge;
		}

		return $replica;
	}

	/**
	 * {@inheritDoc}
	 */
	public function add(BadgeInterface ...$badges): BadgeBagInterface
	{
		foreach ($badges as $badge) {
			$this->badges[get_class($badge)][] = $badge;
		}

		return $this;
	}

	/**
	 * {@inheritDoc}
	 */
	public function last(string $badgeClassName): ?BadgeInterface
	{
		return isset($this->badges[$badgeClassName]) ? end($this->badges[$badgeClassName]) : NULL;
	}

	/**
	 * {@inheritDoc}
	 */
	public function all(?string $badgeClassName = NULL): array
	{
		if (NULL !== $badgeClassName) {
			return $this->badges[$badgeClassName] ?? [];
		}

		return array_merge([], ...array_values($this->badges));
	}
}
