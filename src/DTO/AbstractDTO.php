<?php

declare(strict_types=1);

namespace SixtyEightPublishers\DoctrinePersistence\DTO;

use Nette;
use SixtyEightPublishers;

abstract class AbstractDTO
{
	/** @var \SixtyEightPublishers\DoctrinePersistence\Reader\Property[]  */
	private $properties;

	/** @var array  */
	private $values = [];

	/**
	 * @param \SixtyEightPublishers\DoctrinePersistence\Reader\Property[] $properties
	 */
	final public function __construct(array $properties)
	{
		$this->properties = $properties;
	}

	/**
	 * Fill properties from entity
	 *
	 * @param object $entity
	 */
	abstract public function fill($entity): void;

	/**
	 * @param string $name
	 *
	 * @return bool
	 */
	public function __isset(string $name): bool
	{
		$this->checkProperty($name);

		return NULL !== ($this->values[$name] ?? NULL);
	}

	/**
	 * @param mixed $name
	 * @param mixed $value
	 */
	public function __set(string $name, $value): void
	{
		$this->checkProperty($name);

		$property = $this->properties[$name];

		if (NULL !== $property->validator) {
			Nette\Utils\Validators::assert($value, $property->validator, 'property ' . static::class . '::$' . $name);
		}

		$this->values[$name] = $value;
	}

	/**
	 * @param string $name
	 *
	 * @return mixed
	 */
	public function __get(string $name)
	{
		$this->checkProperty($name);

		$value = $this->values[$name] ?? NULL;

		if (NULL === $value && FALSE === $this->properties[$name]->nullable) {
			throw new SixtyEightPublishers\DoctrinePersistence\Exception\RuntimeException(sprintf(
				'Property %s::$%s can\'t be NULL.',
				__CLASS__,
				$name
			));
		}

		return $value;
	}

	/**
	 * @param string $name
	 *
	 * @return void
	 */
	private function checkProperty(string $name): void
	{
		if (!isset($this->properties[$name])) {
			throw new SixtyEightPublishers\DoctrinePersistence\Exception\InvalidArgumentException(sprintf(
				'Property %s::$%s is not defined',
				__CLASS__,
				$name
			));
		}
	}
}
