<?php

declare(strict_types=1);

namespace SixtyEightPublishers\DoctrinePersistence\Helper;

use Throwable;
use ReflectionType;
use Nette\StaticClass;
use ReflectionException;
use ReflectionNamedType;
use ReflectionParameter;
use ReflectionUnionType;
use Nette\Utils\Callback;
use ReflectionIntersectionType;
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

			$error = $context->getError();

			if (self::acceptsError($parameters[1]->getType(), $error)) {
				$callback($context, $error);
			}
		} catch (ReflectionException $e) {
			throw CallbackInvocationException::fromReflectionException($e);
		}
	}

	/**
	 * Untyped => any error, union => any of the types, intersection => all of the types.
	 */
	private static function acceptsError(?ReflectionType $type, Throwable $error): bool
	{
		if ($type instanceof ReflectionNamedType) {
			$name = $type->getName();

			return $type->isBuiltin() ? in_array($name, ['mixed', 'object'], TRUE) : $error instanceof $name;
		}

		if ($type instanceof ReflectionUnionType) {
			foreach ($type->getTypes() as $innerType) {
				if (self::acceptsError($innerType, $error)) {
					return TRUE;
				}
			}

			return FALSE;
		}

		if ($type instanceof ReflectionIntersectionType) {
			foreach ($type->getTypes() as $innerType) {
				if (!self::acceptsError($innerType, $error)) {
					return FALSE;
				}
			}
		}

		return TRUE;
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
