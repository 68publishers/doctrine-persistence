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
		}

		if (!isset($value)) {
			if (!isset($e) || !$e instanceof SixtyEightPublishers\DoctrinePersistence\Exception\MalformedIdentifierException) {
				$e = new SixtyEightPublishers\DoctrinePersistence\Exception\MalformedIdentifierException(
					'Entity identifier is malformed and can\'t be parsed.',
					0,
					$e ?? NULL
				);
			}

			throw $e;
		}

		return $value;
	}
}
