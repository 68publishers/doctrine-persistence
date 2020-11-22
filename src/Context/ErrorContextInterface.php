<?php

declare(strict_types=1);

namespace SixtyEightPublishers\DoctrinePersistence\Context;

use Throwable;

interface ErrorContextInterface
{
	/**
	 * @return \Throwable
	 */
	public function getError(): Throwable;

	/**
	 * Next callbacks will be skipped
	 *
	 * @return void
	 */
	public function stopPropagation(): void;

	/**
	 * Default behaviour will be prevented => an exception will not be thrown
	 *
	 * @return void
	 */
	public function preventDefault(): void;

	/**
	 * @return bool
	 */
	public function isPropagationStopped(): bool;

	/**
	 * @return bool
	 */
	public function isDefaultBehaviourPrevented(): bool;
}
