<?php

declare(strict_types=1);

namespace SixtyEightPublishers\DoctrinePersistence\Bridge\Nette\DI;

use Nette;

final class DoctrinePersistenceExtension extends Nette\DI\CompilerExtension
{
	public const TAG_TRANSACTION_FACTORY_EXTENDER = '68publishers.doctrine_persistence.transaction_factory_extender';

	/**
	 * {@inheritdoc}
	 */
	public function loadConfiguration(): void
	{
		$this->loadDefinitionsFromConfig(
			$this->loadFromFile(__DIR__ . '/../config/services.neon')['services']
		);
	}

	/**
	 * {@inheritDoc}
	 */
	public function beforeCompile(): void
	{
		$builder = $this->getContainerBuilder();

		/** @var \Nette\DI\Definitions\ServiceDefinition $transactionFactory */
		$transactionFactory = $builder->getDefinition($this->prefix('transaction_factory.base'));

		foreach ($builder->findByTag(self::TAG_TRANSACTION_FACTORY_EXTENDER) as $serviceName => $priority) {
			$transactionFactory->addSetup('addTransactionExtender', ['@' . $serviceName, is_int($priority) ? $priority : 0]);
		}
	}
}
