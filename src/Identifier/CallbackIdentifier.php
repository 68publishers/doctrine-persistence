<?php

declare(strict_types=1);

namespace SixtyEightPublishers\DoctrinePersistence\Identifier;

use Nette;
use SixtyEightPublishers;

final class CallbackIdentifier implements IIdentifier
{
	use Nette\SmartObject;

	/** @var callable  */
	private $cb;

	/**
	 * @param callable $cb
	 */
	public function __construct(callable $cb)
	{
		$this->cb = $cb;
	}

	/**
	 * @param \Throwable|NULL $previous
	 *
	 * @return \SixtyEightPublishers\DoctrinePersistence\Exception\MalformedIdentifierException
	 */
	private function createMalformedIdentifierException(?\Throwable $previous = NULL): SixtyEightPublishers\DoctrinePersistence\Exception\MalformedIdentifierException
	{
		if ($previous instanceof SixtyEightPublishers\DoctrinePersistence\Exception\MalformedIdentifierException) {
			return $previous;
		}

		return new SixtyEightPublishers\DoctrinePersistence\Exception\MalformedIdentifierException(
			'Entity identifier is malformed and can\'t be parsed.',
			0,
			$previous
		);
	}

	/**************** interface \SixtyEightPublishers\DoctrinePersistence\Identifier ****************/

	/**
	 * {@inheritdoc}
	 */
	public function getValue()
	{
		try {
			$cb = $this->cb;
			$value = $cb();
		} catch (\Throwable $e) {
			throw $this->createMalformedIdentifierException($e);
		}

		if (NULL ===  $value) {
			throw $this->createMalformedIdentifierException();
		}

		return $value;
	}
}
