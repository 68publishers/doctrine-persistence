<?php

declare(strict_types=1);

namespace SixtyEightPublishers\DoctrinePersistence\Reader;

interface IReader
{
	/**
	 * @param string $className
	 *
	 * @return array<string, \SixtyEightPublishers\DoctrinePersistence\Reader\Property>
	 */
	public function getClassProperties(string $className): array;
}
