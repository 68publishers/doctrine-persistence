<?php

declare(strict_types=1);

namespace SixtyEightPublishers\DoctrinePersistence\Tests\Cases;

use Mockery;
use Tester\Assert;
use Tester\TestCase;
use Doctrine\ORM\EntityManagerInterface;
use SixtyEightPublishers\DoctrinePersistence\TransactionFactory;
use SixtyEightPublishers\DoctrinePersistence\TransactionTracker;
use SixtyEightPublishers\DoctrinePersistence\TransactionInterface;
use SixtyEightPublishers\DoctrinePersistence\FinallyCallbackQueueInvoker;

require __DIR__ . '/../bootstrap.php';

class TransactionFactoryTestCase extends TestCase
{
	protected function tearDown(): void
	{
		parent::tearDown();

		Mockery::close();
	}

	public function testTransactionShouldBeCreated(): void
	{
		$transactionFactory = $this->createTransactionFactory();

		Assert::type(TransactionInterface::class, $transactionFactory->create(static function () {
		}, []));
	}

	public function testTransactionExtendersShouldByInvoker(): void
	{
		$transactionFactory = $this->createTransactionFactory();
		$extendersInvocations = [];

		$transactionFactory->addTransactionExtender(function (TransactionInterface $transaction) use (&$extendersInvocations) {
			$extendersInvocations[] = 'first-0';
		}, 0);

		$transactionFactory->addTransactionExtender(function (TransactionInterface $transaction) use (&$extendersInvocations) {
			$extendersInvocations[] = 'second-10';
		}, 10);

		Assert::type(TransactionInterface::class, $transactionFactory->create(static function () {
		}, []));
		Assert::same(['second-10', 'first-0'], $extendersInvocations);
	}

	private function createTransactionFactory(): TransactionFactory
	{
		$em = Mockery::mock(EntityManagerInterface::class);
		$finallyCallbackQueueInvoker = Mockery::mock(FinallyCallbackQueueInvoker::class);
		$transactionTracker = new TransactionTracker();

		return new TransactionFactory($em, $finallyCallbackQueueInvoker, $transactionTracker);
	}
}

(new TransactionFactoryTestCase())->run();
