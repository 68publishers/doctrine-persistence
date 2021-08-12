<?php

declare(strict_types=1);

namespace SixtyEightPublishers\DoctrinePersistence\Context;

interface SuccessContextInterface extends CommonContextInterface
{
	/**
	 * @return mixed
	 */
	public function getResult();

	/**
	 * @return bool
	 */
	public function isEverythingCommitted(): bool;
}
