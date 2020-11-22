<?php

declare(strict_types=1);

namespace SixtyEightPublishers\DoctrinePersistence\Bridge\Nette\DI;

use Nette;

final class DoctrinePersistenceExtension extends Nette\DI\CompilerExtension
{
	/**
	 * {@inheritdoc}
	 */
	public function loadConfiguration(): void
	{
		if (method_exists($this, 'loadDefinitionsFromConfig')) {
			# Nette 2.4
			$this->loadDefinitionsFromConfig(
				$this->loadFromFile(__DIR__ . '/../config/services.neon')['services']
			);
		} else {
			# Nette 3.x
			$this->compiler::loadDefinitions(
				$this->getContainerBuilder(),
				$this->loadFromFile(__DIR__ . '/../config/services.neon')['services'],
				$this->name
			);
		}
	}
}
