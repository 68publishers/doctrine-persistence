<?php

declare(strict_types=1);

namespace SixtyEightPublishers\DoctrinePersistence\Exception\Persistence;

use Throwable;

final class DuplicatedValueException extends AbstractInvalidValueException
{
	/**
	 * @param string $entityClassName
	 * @param string $columnName
	 * @param $value
	 * @param int             $code
	 * @param \Throwable|NULL $previous
	 */
	public function __construct(string $entityClassName, string $columnName, $value, int $code = 0, Throwable $previous = NULL)
	{
		parent::__construct(sprintf(
			'Duplicated value %s for column %s::$%s',
			json_encode($value, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE),
			$entityClassName,
			$columnName
		), $entityClassName, $columnName, $value, $code, $previous);
	}
}
