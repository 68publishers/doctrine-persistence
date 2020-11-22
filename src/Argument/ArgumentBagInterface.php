<?php

declare(strict_types=1);

namespace SixtyEightPublishers\DoctrinePersistence\Argument;

use Countable;
use IteratorAggregate;

interface ArgumentBagInterface extends IteratorAggregate, Countable
{
	/**
	 * @param iterable $arguments
	 * @param bool     $merge
	 *
	 * @return \SixtyEightPublishers\DoctrinePersistence\Argument\ArgumentBagInterface
	 */
	public function withArguments(iterable $arguments, bool $merge = TRUE): ArgumentBagInterface;

	/**
	 * @param string $name
	 * @param mixed  $value
	 *
	 * @return void
	 */
	public function add(string $name, $value): void;

	/**
	 * @param string $name
	 *
	 * @return mixed
	 * @throws \SixtyEightPublishers\DoctrinePersistence\Exception\InvalidArgumentException
	 */
	public function get(string $name);

	/**
	 * @param string $name
	 *
	 * @return bool
	 */
	public function has(string $name): bool;
}
