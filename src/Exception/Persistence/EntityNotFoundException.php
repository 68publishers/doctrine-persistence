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
	 * @param string          $entityClassName
	 * @param                 $identifier
	 * @param int             $code
	 * @param \Throwable|NULL $previous
	 */
	public function __construct(string $entityClassName, $identifier, int $code = 0, ?Throwable $previous = NULL)
	{
		parent::__construct(sprintf(
			'Entity %s with identifier %s not found.',
			$entityClassName,
			is_array($identifier) ? json_encode($identifier, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE | JSON_PARTIAL_OUTPUT_ON_ERROR) : (string) $identifier
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
