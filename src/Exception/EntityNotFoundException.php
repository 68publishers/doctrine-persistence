<?php

declare(strict_types=1);

namespace SixtyEightPublishers\DoctrinePersistence\Exception;

final class EntityNotFoundException extends RuntimeException
{
	/**
	 * @param string $identifier
	 * @param string $entityClassName
	 *
	 * @return \SixtyEightPublishers\DoctrinePersistence\Exception\EntityNotFoundException
	 */
	public static function error(string $identifier, string $entityClassName): self
	{
		return new static(sprintf(
			'Entity %s with identifier %s not found',
			$entityClassName,
			$identifier
		));
	}
}
