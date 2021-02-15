<?php

declare(strict_types=1);

namespace SixtyEightPublishers\DoctrinePersistence\Exception;

final class RuntimeException extends \RuntimeException implements ExceptionInterface
{
	/**
	 * @return \SixtyEightPublishers\DoctrinePersistence\Exception\RuntimeException
	 */
	public static function transactionAlreadyExecuted(): self
	{
		return new static('Transaction was already executed.');
	}

	/**
	 * @param string $name
	 *
	 * @return \SixtyEightPublishers\DoctrinePersistence\Exception\RuntimeException
	 */
	public static function reservedArgumentNameUsage(string $name): self
	{
		return new static(sprintf(
			'Usage of an argument\'s name "%s" is not allowed because it\'s reserved name.',
			$name
		));
	}
}
