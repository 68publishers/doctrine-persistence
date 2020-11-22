<?php

declare(strict_types=1);

namespace SixtyEightPublishers\DoctrinePersistence\Exception;

use RuntimeException;
use ReflectionException;
use ReflectionParameter;

final class CallbackInvocationException extends RuntimeException implements TransactionUncatchableExceptionInterface
{
	/**
	 * @param \ReflectionParameter $parameter
	 *
	 * @return \SixtyEightPublishers\DoctrinePersistence\Exception\CallbackInvocationException
	 */
	public static function missingValueForParameter(ReflectionParameter $parameter): self
	{
		return new static(sprintf(
			'Missing value for required parameter: %s',
			$parameter
		));
	}

	/**
	 * @param \ReflectionException $previous
	 *
	 * @return \SixtyEightPublishers\DoctrinePersistence\Exception\CallbackInvocationException
	 */
	public static function fromReflectionException(ReflectionException $previous): self
	{
		return new static('Unexpected reflection exception', 0, $previous);
	}
}
