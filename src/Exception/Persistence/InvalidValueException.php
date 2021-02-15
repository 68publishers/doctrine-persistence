<?php

declare(strict_types=1);

namespace SixtyEightPublishers\DoctrinePersistence\Exception\Persistence;

use Throwable;

final class InvalidValueException extends AbstractInvalidValueException
{
	/** @var mixed|object|NULL */
	private $entity;

	/**
	 * @param string          $entityClassName
	 * @param string          $columnName
	 * @param mixed           $value
	 * @param object|NULL     $entity
	 * @param int             $code
	 * @param \Throwable|NULL $previous
	 */
	public function __construct(string $entityClassName, string $columnName, $value, ?object $entity = NULL, int $code = 0, Throwable $previous = NULL)
	{
		parent::__construct(sprintf(
			'Invalid value %s for column %s::$%s',
			json_encode($value, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE),
			$entityClassName,
			$columnName
		), $entityClassName, $columnName, $value, $code, $previous);

		$this->entity = $entity;
	}

	/**
	 * @return mixed|NULL|object
	 */
	public function getEntity()
	{
		return $this->entity;
	}
}
