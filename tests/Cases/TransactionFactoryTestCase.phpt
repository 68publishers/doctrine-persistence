<?php

declare(strict_types=1);

namespace SixtyEightPublishers\DoctrinePersistence\Tests\Cases;

use Mockery;
use Tester\Assert;
use Tester\TestCase;
use Doctrine\ORM\EntityManagerInterface;
use SixtyEightPublishers\DoctrinePersistence\TransactionFactory;
use SixtyEightPublishers\DoctrinePersistence\TransactionInterface;
use SixtyEightPublishers\DoctrinePersistence\FinallyCallbackQueueInvoker;

require __DIR__ . '/../bootstrap.php';

class TransactionFactoryTestCase extends TestCase
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
	public function testTransactionShouldBeCreated(): void
	{
		$em = Mockery::mock(EntityManagerInterface::class);
		$finallyCallbackQueueInvoker = Mockery::mock(FinallyCallbackQueueInvoker::class);

		$transactionFactory = new TransactionFactory($em, $finallyCallbackQueueInvoker);

		Assert::type(TransactionInterface::class, $transactionFactory->create(static function () {
		}, []));
	}
}

(new TransactionFactoryTestCase())->run();
