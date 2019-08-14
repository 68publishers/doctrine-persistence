<?php

declare(strict_types=1);

namespace SixtyEightPublishers\DoctrinePersistence\Exception;

final class DuplicatedValueException extends PersistenceException
{
	/** @var string  */
	private $entityClassName;

	/** @var string  */
	private $columnName;

	/** @var mixed  */
	private $value;

	/**
	 * @param string $entityClassName
	 * @param string $columnName
	 * @param $value
	 * @param int             $code
	 * @param \Throwable|null $previous
	 */
	public function __construct(string $entityClassName, string $columnName, $value, int $code = 0, \Throwable $previous = NULL)
	{
		parent::__construct(sprintf(
			'Duplicated value %s for column %s::$%s',
			(string) $value,
			$entityClassName,
			$columnName
		), $code, $previous);

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
		return $entityClassName === $this->getEntityClassName() && $columnName === $this->getColumnName();
	}
}
