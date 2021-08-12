<?php

declare(strict_types=1);

namespace SixtyEightPublishers\DoctrinePersistence\Tests\Cases\Bridge\Nette\DI;

use Mockery;
use Tester\Assert;
use ReflectionClass;
use Tester\TestCase;
use Nette\Configurator;
use Nette\DI\Container;
use Nette\DI\Definitions\Statement;
use Doctrine\ORM\EntityManagerInterface;
use SixtyEightPublishers\DoctrinePersistence\TransactionFactory;
use SixtyEightPublishers\DoctrinePersistence\TransactionFactoryInterface;
use SixtyEightPublishers\DoctrinePersistence\Bridge\Nette\DI\DoctrinePersistenceExtension;

require __DIR__ . '/../../../../bootstrap.php';

class DoctrinePersistenceExtensionTestCase extends TestCase
{
	protected function tearDown(): void
	{
		parent::tearDown();

		Mockery::close();
	}

	public function testBaseIntegration(): void
	{
		/** @var \Nette\DI\Container|NULL $container */
		$container = NULL;

		Assert::noError(function () use (&$container) {
			$container = $this->createContainer();
		});

		Assert::type(TransactionFactoryInterface::class, $container->getByType(TransactionFactoryInterface::class));
	}

	public function testIntegrationWithTransactionExtenders(): void
	{
		/** @var \Nette\DI\Container|NULL $container */
		$container = NULL;

		Assert::noError(function () use (&$container) {
			$container = $this->createContainer(CONFIG_DIR . '/transaction_extender.neon');
		});

		$transactionFactory = $container->getByType(TransactionFactoryInterface::class);
		$extender1Service = $container->getService('extender1');
		$extender2Service = $container->getService('extender2');

		Assert::type(TransactionFactoryInterface::class, $transactionFactory);

		$property = (new ReflectionClass(TransactionFactory::class))->getProperty('transactionsExtenders');

		$property->setAccessible(TRUE);

		Assert::equal([
			0 => [$extender1Service],
			10 => [$extender2Service],
		], $property->getValue($transactionFactory));
	}

	private function createContainer(?string $config = NULL): Container
	{
		$configurator = new Configurator();

		$configurator->setTempDirectory(TEMP_PATH);

		# @todo real entity manager would be better :thonk:
		$configurator->addConfig([
			'extensions' => [
				'68publishers.doctrine_persistence' => DoctrinePersistenceExtension::class,
			],
			'services' => [
				'em' => [
					'type' => EntityManagerInterface::class,
					'factory' => new Statement([Mockery::class, 'mock'], [EntityManagerInterface::class]),
				],
			],
		]);

		if (NULL !== $config) {
			$configurator->addConfig($config);
		}

		return $configurator->createContainer();
	}
}

(new DoctrinePersistenceExtensionTestCase())->run();
