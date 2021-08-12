<?php

declare(strict_types=1);

namespace SixtyEightPublishers\DoctrinePersistence\Exception\Persistence;

use Throwable;

final class EntityNotFoundException extends PersistenceException
{
	private string $entityClassName;

	/** @var mixed  */
	private $identifier;

	/**
	 * @param string $entityClassName
	 * @param $identifier
	 * @param int             $code
	 * @param \Throwable|NULL $previous
	 */
	public function __construct(string $entityClassName, $identifier, int $code = 0, Throwable $previous = NULL)
	{
		parent::__construct(sprintf(
			'Entity %s with identifier %s not found.',
			$entityClassName,
			(string) $identifier
		), $code, $previous);

		$this->entityClassName = $entityClassName;
		$this->identifier = $identifier;
	}

	/**
	 * @return string
	 */
	public function getEntityClassName(): string
	{
		return $this->entityClassName;
	}

	/**
	 * @return mixed
	 */
	public function getIdentifier()
	{
		return $this->identifier;
	}
}
