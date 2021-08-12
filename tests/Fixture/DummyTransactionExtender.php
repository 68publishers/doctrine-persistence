<?php

declare(strict_types=1);

namespace SixtyEightPublishers\DoctrinePersistence\Tests\Fixture;

use SixtyEightPublishers\DoctrinePersistence\TransactionFactoryInterface;

final class DummyTransactionExtender
{
	/**
	 * @param \SixtyEightPublishers\DoctrinePersistence\TransactionFactoryInterface $transactionFactory
	 *
	 * @return void
	 */
	public function __invoke(TransactionFactoryInterface $transactionFactory): void
	{
	}
}
