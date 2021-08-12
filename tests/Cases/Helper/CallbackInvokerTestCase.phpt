<?php

declare(strict_types=1);

namespace SixtyEightPublishers\DoctrinePersistence\Tests\Cases\Helper;

use Mockery;
use Exception;
use Throwable;
use ArrayObject;
use Tester\Assert;
use LogicException;
use Tester\TestCase;
use RuntimeException;
use Doctrine\ORM\EntityManagerInterface;
use SixtyEightPublishers\DoctrinePersistence\Argument\ArgumentBag;
use SixtyEightPublishers\DoctrinePersistence\Helper\CallbackInvoker;
use SixtyEightPublishers\DoctrinePersistence\Context\ErrorContextInterface;
use SixtyEightPublishers\DoctrinePersistence\Exception\CallbackInvocationException;

require __DIR__ . '/../../bootstrap.php';

class CallbackInvokerTestCase extends TestCase
{
	protected function tearDown(): void
	{
		parent::tearDown();

		Mockery::close();
	}

	public function testShouldInvokeTransactionCallbackWithNamedArgumentsOnly(): void
	{
		[$namedArgumentBag, $typeHintedArgumentBag] = $this->createArgumentBags();

		$called = FALSE;
		CallbackInvoker::invokeTransactionCallback(static function (string $name, int $age, ArrayObject $creditCards, bool $agreement) use (&$called) {
			$called = TRUE;

			Assert::same('John Doe', $name);
			Assert::same(22, $age);
			Assert::type(ArrayObject::class, $creditCards);
			Assert::true($agreement);
		}, $namedArgumentBag, $typeHintedArgumentBag);

		Assert::true($called);
	}

	public function testShouldInvokeTransactionCallbackWithTypeHintedArgumentsOnly(): void
	{
		[$namedArgumentBag, $typeHintedArgumentBag] = $this->createArgumentBags();

		$called = FALSE;
		CallbackInvoker::invokeTransactionCallback(static function (EntityManagerInterface $em) use (&$called) {
			$called = TRUE;

			Assert::type(EntityManagerInterface::class, $em);
		}, $namedArgumentBag, $typeHintedArgumentBag);

		Assert::true($called);
	}

	public function testShouldInvokeTransactionCallbackWithCombinedArguments(): void
	{
		[$namedArgumentBag, $typeHintedArgumentBag] = $this->createArgumentBags();

		# combined with different positions
		$called = FALSE;
		CallbackInvoker::invokeTransactionCallback(static function (int $age, EntityManagerInterface $entityManager, string $name, ArrayObject $creditCards) use (&$called) {
			$called = TRUE;

			Assert::same(22, $age);
			Assert::type(EntityManagerInterface::class, $entityManager);
			Assert::same('John Doe', $name);
			Assert::type(ArrayObject::class, $creditCards);
		}, $namedArgumentBag, $typeHintedArgumentBag);

		Assert::true($called);
	}

	public function testShouldInvokeTransactionCallbackWithMissingButNullableArguments(): void
	{
		[$namedArgumentBag, $typeHintedArgumentBag] = $this->createArgumentBags();

		$called = FALSE;
		CallbackInvoker::invokeTransactionCallback(static function (?string $city, ?string $name, $whatever, ?Throwable $error, ?int $weight = NULL, ?ArrayObject $creditCards = NULL) use (&$called) {
			$called = TRUE;

			Assert::null($city);
			Assert::same('John Doe', $name);
			Assert::null($whatever); // no type => NULL can be passed
			Assert::null($weight);
			Assert::type(ArrayObject::class, $creditCards);
			Assert::null($error);
		}, $namedArgumentBag, $typeHintedArgumentBag);

		Assert::true($called);
	}

	public function testShouldInvokeTransactionCallbackWithMissingArgumentsWithDefaultValues(): void
	{
		[$namedArgumentBag, $typeHintedArgumentBag] = $this->createArgumentBags();

		$called = FALSE;
		CallbackInvoker::invokeTransactionCallback(static function (string $name, bool $agreement = FALSE, string $city = 'Prague') use (&$called) {
			$called = TRUE;

			Assert::same('John Doe', $name);
			Assert::true($agreement);
			Assert::same('Prague', $city);
		}, $namedArgumentBag, $typeHintedArgumentBag);

		Assert::true($called);
	}

	public function testShouldThrowExceptionWhenCallbacksContainsMissingRequiredArguments(): void
	{
		Assert::exception(
			function () {
				CallbackInvoker::invokeTransactionCallback(static function (ArrayObject $undefinedArgument) {
				}, ...$this->createArgumentBags());
			},
			CallbackInvocationException::class,
			'#^' . preg_quote('Missing value for required parameter:', '#') . '.*#'
		);

		Assert::exception(
			function () {
				CallbackInvoker::invokeTransactionCallback(static function (float $whatever) {
				}, ...$this->createArgumentBags());
			},
			CallbackInvocationException::class,
			'#^' . preg_quote('Missing value for required parameter:', '#') . '.*#'
		);
	}

	public function testShouldInvokeErrorCallbackWithoutSpecificException(): void
	{
		$error = new RuntimeException();
		$context = $this->createErrorContext($error);

		$called = FALSE;
		CallbackInvoker::tryInvokeErrorCallback(static function (ErrorContextInterface $errorContext) use (&$called, $context) {
			$called = TRUE;

			Assert::same($context, $errorContext);
		}, $context);

		Assert::true($called);
	}

	public function testShouldInvokeErrorCallbackGenericException(): void
	{
		$error = new RuntimeException();
		$context = $this->createErrorContext($error);

		$called = FALSE;
		CallbackInvoker::tryInvokeErrorCallback(static function (ErrorContextInterface $errorContext, Throwable $e) use (&$called, $context, $error) {
			$called = TRUE;

			Assert::same($context, $errorContext);
			Assert::same($error, $e);
		}, $context);

		Assert::true($called);

		$called = FALSE;
		CallbackInvoker::tryInvokeErrorCallback(static function (ErrorContextInterface $errorContext, Exception $e) use (&$called, $context, $error) {
			$called = TRUE;

			Assert::same($context, $errorContext);
			Assert::same($error, $e);
		}, $context);

		Assert::true($called);
	}

	public function testShouldInvokeErrorCallbackWithSpecificException(): void
	{
		# 1 - RuntimeException
		$error = new RuntimeException();
		$context = $this->createErrorContext($error);

		$called = FALSE;
		CallbackInvoker::tryInvokeErrorCallback(static function (ErrorContextInterface $errorContext, RuntimeException $e) use (&$called, $context, $error) {
			$called = TRUE;

			Assert::same($context, $errorContext);
			Assert::same($error, $e);
		}, $context);

		Assert::true($called);

		# 2 - LoginException
		$error = new LogicException();
		$context = $this->createErrorContext($error);

		$called = FALSE;
		CallbackInvoker::tryInvokeErrorCallback(static function (ErrorContextInterface $errorContext, LogicException $e) use (&$called, $context, $error) {
			$called = TRUE;

			Assert::same($context, $errorContext);
			Assert::same($error, $e);
		}, $context);

		Assert::true($called);
	}

	public function testShouldNotInvokeErrorCallbackWithSpecificException(): void
	{
		# First
		$error = new RuntimeException();
		$context = $this->createErrorContext($error);

		$called = FALSE;
		CallbackInvoker::tryInvokeErrorCallback(static function (ErrorContextInterface $errorContext, LogicException $e) use (&$called) {
			$called = TRUE;
		}, $context);

		Assert::false($called);
	}

	private function createErrorContext(Throwable $error): ErrorContextInterface
	{
		$context = Mockery::mock(ErrorContextInterface::class);

		$context->shouldReceive('getError')->andReturn($error);

		return $context;
	}

	private function createArgumentBags(): array
	{
		$namedArgumentBag = new ArgumentBag([
			'name' => 'John Doe',
			'age' => 22,
			'agreement' => TRUE,
			'creditCards' => new ArrayObject([]),
		]);

		$typeHintedArgumentBag = new ArgumentBag([
			EntityManagerInterface::class => Mockery::mock(EntityManagerInterface::class),
		]);

		return [
			$namedArgumentBag,
			$typeHintedArgumentBag,
		];
	}
}

(new CallbackInvokerTestCase())->run();
