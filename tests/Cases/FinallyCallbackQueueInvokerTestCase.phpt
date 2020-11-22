<?php

declare(strict_types=1);

namespace SixtyEightPublishers\DoctrinePersistence\Tests\Cases;

use Mockery;
use Tester\Assert;
use Tester\TestCase;
use SixtyEightPublishers\DoctrinePersistence\FinallyCallbackQueueInvoker;
use SixtyEightPublishers\DoctrinePersistence\Context\FinallyContextInterface;
use SixtyEightPublishers\DoctrinePersistence\Exception\TransactionMustBeCommittedException;

require __DIR__ . '/../bootstrap.php';

class FinallyCallbackQueueInvokerTestCase extends TestCase
{
	/**
	 * @return void
	 */
	protected function tearDown(): void
	{
		parent::tearDown();

		Mockery::close();
	}

	/**
	 * @return void
	 */
	public function testCallbacksInvocation(): void
	{
		$everythingCommitted = FALSE;
		$firstCallbackCounter = $secondCallbackCounter = $thirdCallbackCounter = 0;
		$context = Mockery::mock(FinallyContextInterface::class);

		$firstCallback = static function (FinallyContextInterface $context) use (&$firstCallbackCounter) {
			$context->needsEverythingCommitted();
			$firstCallbackCounter++;
		};

		$secondCallback = static function () use (&$secondCallbackCounter) {
			$secondCallbackCounter++;
		};

		$thirdCallback = static function (FinallyContextInterface $context) use (&$thirdCallbackCounter) {
			$context->needsEverythingCommitted();
			$thirdCallbackCounter++;
		};

		$context->shouldReceive('needsEverythingCommitted')->andReturnUsing(static function () use (&$everythingCommitted) {
			if (!$everythingCommitted) {
				throw new TransactionMustBeCommittedException();
			}
		});

		$context->shouldReceive('isEverythingCommitted')->andReturnUsing(static function () use (&$everythingCommitted) {
			return $everythingCommitted;
		});

		$finallyCallbackQueueInvoker = new FinallyCallbackQueueInvoker();

		$finallyCallbackQueueInvoker->enqueue($firstCallback, $context);
		$finallyCallbackQueueInvoker->enqueue($secondCallback, $context);
		$finallyCallbackQueueInvoker->enqueue($thirdCallback, $context);

		# First invocation - only the second callback should be invoked
		$finallyCallbackQueueInvoker->invoke();

		Assert::same(0, $firstCallbackCounter);
		Assert::same(1, $secondCallbackCounter);
		Assert::same(0, $thirdCallbackCounter);

		# Second invocation - nothing should be invoked
		$finallyCallbackQueueInvoker->invoke();

		Assert::same(0, $firstCallbackCounter);
		Assert::same(1, $secondCallbackCounter);
		Assert::same(0, $thirdCallbackCounter);

		# Simulate successful transaction
		$everythingCommitted = TRUE;

		# Third invocation - the first and third callbacks should by invoked
		$finallyCallbackQueueInvoker->invoke();

		Assert::same(1, $firstCallbackCounter);
		Assert::same(1, $secondCallbackCounter);
		Assert::same(1, $thirdCallbackCounter);

		# Fourth invocation - nothing should be invoked
		$finallyCallbackQueueInvoker->invoke();

		Assert::same(1, $firstCallbackCounter);
		Assert::same(1, $secondCallbackCounter);
		Assert::same(1, $thirdCallbackCounter);
	}
}

(new FinallyCallbackQueueInvokerTestCase())->run();
