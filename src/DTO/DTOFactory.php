<?php

declare(strict_types=1);

namespace SixtyEightPublishers\DoctrinePersistence\DTO;

use Nette;
use SixtyEightPublishers;

final class DTOFactory
{
	use Nette\StaticClass;

	/** @var NULL|\SixtyEightPublishers\DoctrinePersistence\Reader\IReader */
	private static $reader;

	/**
	 * @param string $dtoClass
	 *
	 * @return \SixtyEightPublishers\DoctrinePersistence\DTO\AbstractDTO|mixed
	 */
	public static function create(string $dtoClass): SixtyEightPublishers\DoctrinePersistence\DTO\AbstractDTO
	{
		if (FALSE === is_subclass_of($dtoClass, SixtyEightPublishers\DoctrinePersistence\DTO\AbstractDTO::class, TRUE)) {
			throw new SixtyEightPublishers\DoctrinePersistence\Exception\InvalidArgumentException(sprintf(
				'Class %s is not inheritor of %s',
				$dtoClass,
				SixtyEightPublishers\DoctrinePersistence\DTO\AbstractDTO::class
			));
		}

		return new $dtoClass(self::getReader()->getClassProperties($dtoClass));
	}

	/**
	 * @internal
	 *
	 * @param \SixtyEightPublishers\DoctrinePersistence\Reader\IReader $reader
	 *
	 * @return void
	 */
	public static function setReader(SixtyEightPublishers\DoctrinePersistence\Reader\IReader $reader): void
	{
		self::$reader = $reader;
	}

	/**
	 * @return \SixtyEightPublishers\DoctrinePersistence\Reader\IReader
	 */
	private static function getReader(): SixtyEightPublishers\DoctrinePersistence\Reader\IReader
	{
		if (NULL === self::$reader) {
			throw new SixtyEightPublishers\DoctrinePersistence\Exception\RuntimeException(sprintf(
				'Reader is not set, please use method %s::setReader().',
				self::class
			));
		}

		return self::$reader;
	}
}
