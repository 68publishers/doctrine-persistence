<?php

declare(strict_types=1);

namespace SixtyEightPublishers\DoctrinePersistence\Exception\Persistence;

use Exception;
use SixtyEightPublishers\DoctrinePersistence\Exception\ExceptionInterface;

class PersistenceException extends Exception implements ExceptionInterface
{
	private array $context = [];

	/**
	 * @return array
	 */
	public function getContext(): array
	{
		return $this->context;
	}

	/**
	 * @param array $context
	 *
	 * @return $this
	 */
	public function setContext(array $context): self
	{
		$this->context = $context;

		return $this;
	}
}
