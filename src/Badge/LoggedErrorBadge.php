<?php

declare(strict_types=1);

namespace SixtyEightPublishers\DoctrinePersistence\Badge;

use Throwable;

final class LoggedErrorBadge implements BadgeInterface
{
	private Throwable $error;

	/**
	 * @param \Throwable $error
	 */
	public function __construct(Throwable $error)
	{
		$this->error = $error;
	}

	/**
	 * @return \Throwable
	 */
	public function getError(): Throwable
	{
		return $this->error;
	}
}
