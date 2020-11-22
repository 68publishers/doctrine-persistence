<?php

declare(strict_types=1);

namespace SixtyEightPublishers\DoctrinePersistence\Tests\Cases\Bridge\Nette\DI;

use Mockery;
use Tester\Assert;
use Tester\TestCase;
use Nette\Configurator;
use Nette\DI\Container;
use Nette\DI\Definitions\Statement;
use Doctrine\ORM\EntityManagerInterface;
use Nette\DI\Statement as Nette24Statement;
use SixtyEightPublishers\DoctrinePersistence\TransactionFactoryInterface;
use SixtyEightPublishers\DoctrinePersistence\Bridge\Nette\DI\DoctrinePersistenceExtension;

require __DIR__ . '/../../../../bootstrap.php';

if (!class_exists(Statement::class)) {
	class_alias(Nette24Statement::class, Statement::class);
}

class DoctrinePersistenceExtensionTestCase extends TestCase
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
	public function testIntegration(): void
	{
		/** @var \Nette\DI\Container|NULL $container */
		$container = NULL;

		Assert::noError(function () use (&$container) {
			$container = $this->createContainer();
		});

		Assert::type(TransactionFactoryInterface::class, $container->getByType(TransactionFactoryInterface::class));
	}

	/**
	 * @param string|NULL $config
	 *
	 * @return \Nette\DI\Container
	 */
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
