<?php

declare(strict_types=1);

namespace SixtyEightPublishers\DoctrinePersistence\Exception;

class PersistenceException extends \Exception implements IException
{
	/** @var bool  */
	private $alreadyProcessed = FALSE;

	/**
	 * @return bool
	 */
	public function isAlreadyProcessed(): bool
	{
		return $this->alreadyProcessed;
	}

	/**
	 * @param bool $alreadyProcessed
	 *
	 * @return void
	 */
	public function setAlreadyProcessed(bool $alreadyProcessed): void
	{
		$this->alreadyProcessed = $alreadyProcessed;
	}
}
