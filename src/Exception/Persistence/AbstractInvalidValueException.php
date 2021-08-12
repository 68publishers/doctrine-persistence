<?php

declare(strict_types=1);

namespace SixtyEightPublishers\DoctrinePersistence\Exception\Persistence;

use Throwable;

abstract class AbstractInvalidValueException extends PersistenceException
{
	protected string $entityClassName;

	protected string $columnName;

	/** @var mixed  */
	protected $value;

	/**
	 * @param string $message
	 * @param string $entityClassName
	 * @param string $columnName
	 * @param $value
	 * @param int             $code
	 * @param \Throwable|NULL $previous
	 */
	public function __construct(string $message, string $entityClassName, string $columnName, $value, int $code = 0, Throwable $previous = NULL)
	{
		parent::__construct($message, $code, $previous);

		$this->entityClassName = $entityClassName;
		$this->columnName = $columnName;
		$this->value = $value;
	}

	/**
	 * @return string
	 */
	public function getEntityClassName(): string
	{
		return $this->entityClassName;
	}

	/**
	 * @return string
	 */
	public function getColumnName(): string
	{
		return $this->columnName;
	}

	/**
	 * @return mixed
	 */
	public function getValue()
	{
		return $this->value;
	}

	/**
	 * @param string $entityClassName
	 * @param string $columnName
	 *
	 * @return bool
	 */
	public function is(string $entityClassName, string $columnName): bool
	{
		return (is_a($entityClassName, $this->getEntityClassName(), TRUE) || is_subclass_of($entityClassName, $this->getEntityClassName(), TRUE)) && $columnName === $this->getColumnName();
	}
}
