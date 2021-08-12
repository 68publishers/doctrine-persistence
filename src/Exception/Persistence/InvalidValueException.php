<?php

declare(strict_types=1);

namespace SixtyEightPublishers\DoctrinePersistence\Exception\Persistence;

use Throwable;

final class InvalidValueException extends AbstractInvalidValueException
{
	private ?string $reason;

	/** @var mixed|object|NULL */
	private $entity;

	/**
	 * @param string          $entityClassName
	 * @param string          $columnName
	 * @param mixed           $value
	 * @param string|NULL     $reason
	 * @param int             $code
	 * @param \Throwable|NULL $previous
	 */
	public function __construct(string $entityClassName, string $columnName, $value, ?string $reason = NULL, int $code = 0, Throwable $previous = NULL)
	{
		parent::__construct(sprintf(
			'Invalid value %s for column %s::$%s.%s',
			json_encode($value, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE),
			$entityClassName,
			$columnName,
			empty($reason) ? '' : ' ' . $reason
		), $entityClassName, $columnName, $value, $code, $previous);

		$this->reason = $reason;
	}

	/**
	 * @param string          $entityClassName
	 * @param string          $columnName
	 * @param mixed           $value
	 * @param string|NULL     $reason
	 * @param int             $code
	 * @param \Throwable|NULL $previous
	 *
	 * @return static
	 */
	public static function create(string $entityClassName, string $columnName, $value, ?string $reason = NULL, int $code = 0, Throwable $previous = NULL): self
	{
		return new self($entityClassName, $columnName, $value, $reason, $code, $previous);
	}

	/**
	 * @return NULL|string
	 */
	public function getReason(): ?string
	{
		return $this->reason;
	}

	/**
	 * @return mixed|NULL|object
	 */
	public function getEntity()
	{
		return $this->entity;
	}

	/**
	 * @param $entity
	 *
	 * @return \SixtyEightPublishers\DoctrinePersistence\Exception\Persistence\InvalidValueException
	 */
	public function setEntity($entity): self
	{
		$this->entity = $entity;

		return $this;
	}
}
