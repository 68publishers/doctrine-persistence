<?php

declare(strict_types=1);

namespace SixtyEightPublishers\DoctrinePersistence\Tests\Cases\Helper;

use Mockery;
use Tester\Assert;
use Tester\TestCase;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use SixtyEightPublishers\DoctrinePersistence\Helper\TransactionHelper;

require __DIR__ . '/../../bootstrap.php';

class TransactionHelperTestCase extends TestCase
{
	/** @var \Doctrine\DBAL\Connection|NULL */
	private $connection;

	protected function setUp(): void
	{
		parent::setUp();

		$this->connection = Mockery::mock(Connection::class);

		$this->connection->shouldReceive('getTransactionNestingLevel')->once()->andReturn(1);
		$this->connection->shouldReceive('getTransactionNestingLevel')->once()->andReturn(0);
	}

	protected function tearDown(): void
	{
		parent::tearDown();

		Mockery::close();
	}

	public function testEverythingCommittedWithConnectionArgument(): void
	{
		Assert::false(TransactionHelper::isEverythingCommitted($this->connection));
		Assert::true(TransactionHelper::isEverythingCommitted($this->connection));
	}

	public function testEverythingCommittedWithEntityManagerArgument(): void
	{
		$em = Mockery::mock(EntityManagerInterface::class);

		$em->shouldReceive('getConnection')->andReturn($this->connection);

		Assert::false(TransactionHelper::isEverythingCommitted($em));
		Assert::true(TransactionHelper::isEverythingCommitted($em));
	}
}

(new TransactionHelperTestCase())->run();
