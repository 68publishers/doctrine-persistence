<?php

declare(strict_types=1);

namespace SixtyEightPublishers\DoctrinePersistence\Argument;

use ArrayIterator;
use SixtyEightPublishers\DoctrinePersistence\Exception\InvalidArgumentException;

class ArgumentBag implements ArgumentBagInterface
{
	/** @var array  */
	private $arguments = [];

	/**
	 * @param iterable $values
	 */
	public function __construct(iterable $values = [])
	{
		foreach ($values as $k => $v) {
			$this->arguments[(string) $k] = $v;
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function withArguments(iterable $arguments, bool $merge = TRUE): ArgumentBagInterface
	{
		if (!$merge) {
			return new static($arguments);
		}

		$newArguments = $this->arguments;

		foreach ($arguments as $name => $argument) {
			$newArguments[$name] = $argument;
		}

		return new static($newArguments);
	}

	/**
	 * {@inheritDoc}
	 */
	public function add(string $name, $value): void
	{
		if ($this->has($name)) {
			throw new InvalidArgumentException(sprintf(
				'Argument with a key "%s" is already defined.',
				$name
			));
		}

		$this->arguments[$name] = $value;
	}

	/**
	 * {@inheritDoc}
	 */
	public function get(string $name)
	{
		if (!$this->has($name)) {
			throw new InvalidArgumentException(sprintf(
				'Missing argument with name "%s".',
				$name
			));
		}

		return $this->arguments[$name];
	}

	/**
	 * {@inheritDoc}
	 */
	public function has(string $name): bool
	{
		return array_key_exists($name, $this->arguments);
	}

	/**
	 * {@inheritDoc}
	 */
	public function getIterator(): ArrayIterator
	{
		return new ArrayIterator($this->arguments);
	}

	/**
	 * {@inheritDoc}
	 */
	public function count(): int
	{
		return count($this->arguments);
	}
}
