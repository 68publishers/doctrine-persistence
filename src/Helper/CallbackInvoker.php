<?php

declare(strict_types=1);

namespace SixtyEightPublishers\DoctrinePersistence\Helper;

use Nette\StaticClass;
use ReflectionException;
use ReflectionNamedType;
use ReflectionParameter;
use Nette\Utils\Callback;
use SixtyEightPublishers\DoctrinePersistence\Argument\ArgumentBagInterface;
use SixtyEightPublishers\DoctrinePersistence\Context\ErrorContextInterface;
use SixtyEightPublishers\DoctrinePersistence\Exception\CallbackInvocationException;

final class CallbackInvoker
{
	use StaticClass;

	/**
	 * @param callable                                                                $callback
	 * @param \SixtyEightPublishers\DoctrinePersistence\Argument\ArgumentBagInterface $namedArguments
	 * @param \SixtyEightPublishers\DoctrinePersistence\Argument\ArgumentBagInterface $typeHintedArguments
	 *
	 * @return mixed
	 */
	public static function invokeTransactionCallback(callable $callback, ArgumentBagInterface $namedArguments, ArgumentBagInterface $typeHintedArguments)
	{
		try {
			$arguments = [];

			foreach (Callback::toReflection($callback)->getParameters() as $parameter) {
				$arguments[] = self::getValue($parameter, $namedArguments, $typeHintedArguments);
			}
		} catch (ReflectionException $e) {
			throw CallbackInvocationException::fromReflectionException($e);
		}

		return $callback(...$arguments);
	}

	/**
	 * @param callable                                                                $callback
	 * @param \SixtyEightPublishers\DoctrinePersistence\Context\ErrorContextInterface $context
	 *
	 * @return void
	 */
	public static function tryInvokeErrorCallback(callable $callback, ErrorContextInterface $context): void
	{
		try {
			$parameters = Callback::toReflection($callback)->getParameters();

			if (!isset($parameters[1])) {
				$callback($context);

				return;
			}

			$exceptionType = $parameters[1]->getType();
			$exceptionType = $exceptionType instanceof ReflectionNamedType ? $exceptionType->getName() : NULL;

			if ($context->getError() instanceof $exceptionType) {
				$callback($context, $context->getError());
			}
		} catch (ReflectionException $e) {
			throw CallbackInvocationException::fromReflectionException($e);
		}
	}

	/**
	 * @param \ReflectionParameter                                                    $parameter
	 * @param \SixtyEightPublishers\DoctrinePersistence\Argument\ArgumentBagInterface $namedArguments
	 * @param \SixtyEightPublishers\DoctrinePersistence\Argument\ArgumentBagInterface $typeHintedArguments
	 *
	 * @return mixed
	 * @throws \ReflectionException
	 * @throws \SixtyEightPublishers\DoctrinePersistence\Exception\CallbackInvocationException
	 */
	private static function getValue(ReflectionParameter $parameter, ArgumentBagInterface $namedArguments, ArgumentBagInterface $typeHintedArguments)
	{
		$type = $parameter->getType();

		if ($type instanceof ReflectionNamedType && !$type->isBuiltin() && $typeHintedArguments->has($type->getName())) {
			return $typeHintedArguments->get($type->getName());
		}

		if ($namedArguments->has($parameter->getName())) {
			return $namedArguments->get($parameter->getName());
		}

		if ($parameter->isDefaultValueAvailable()) {
			return $parameter->getDefaultValue();
		}

		if ($parameter->allowsNull()) {
			return NULL;
		}

		throw CallbackInvocationException::missingValueForParameter($parameter);
	}
}
