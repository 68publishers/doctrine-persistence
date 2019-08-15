<?php

declare(strict_types=1);

namespace SixtyEightPublishers\DoctrinePersistence\Identifier;

use Nette;
use Ramsey;
use SixtyEightPublishers;

final class UuidStringIdentifier implements IIdentifier
{
	use Nette\SmartObject;

	/** @var string  */
	private $uuid;

	/**
	 * @param string $uuid
	 */
	public function __construct(string $uuid)
	{
		$this->uuid = $uuid;
	}

	/**************** interface \SixtyEightPublishers\DoctrinePersistence\Identifier ****************/

	/**
	 * {@inheritdoc}
	 */
	public function getValue(): Ramsey\Uuid\UuidInterface
	{
		try {
			return Ramsey\Uuid\Uuid::fromString($this->uuid);
		} catch (Ramsey\Uuid\Exception\InvalidUuidStringException $e) {
			throw new SixtyEightPublishers\DoctrinePersistence\Exception\MalformedIdentifierException(sprintf(
				'String "%s" is not valid UUID.',
				$this->uuid
			), 0, $e);
		}
	}
}
