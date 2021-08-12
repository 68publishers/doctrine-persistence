<?php

declare(strict_types=1);

namespace SixtyEightPublishers\DoctrinePersistence\Context;

use Throwable;

interface ErrorContextInterface extends CommonContextInterface
{
	/**
	 * @return \Throwable
	 */
	public function getError(): Throwable;

	/**
	 * Next callbacks will be skipped
	 *
	 * @return \SixtyEightPublishers\DoctrinePersistence\Context\ErrorContextInterface
	 */
	public function stopPropagation(): self;

	/**
	 * Default behaviour will be prevented => an exception will not be thrown
	 *
	 * @return \SixtyEightPublishers\DoctrinePersistence\Context\ErrorContextInterface
	 */
	public function preventDefault(): self;

	/**
	 * @return bool
	 */
	public function isPropagationStopped(): bool;

	/**
	 * @return bool
	 */
	public function isDefaultBehaviourPrevented(): bool;
}
