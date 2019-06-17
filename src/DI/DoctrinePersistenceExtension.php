<?php

declare(strict_types=1);

namespace SixtyEightPublishers\DoctrinePersistence\DI;

use Nette;
use SixtyEightPublishers;

final class DoctrinePersistenceExtension extends Nette\DI\CompilerExtension
{
	/** @var array  */
	private $defaults = [
		'reader_storage' => '@' . Nette\Caching\IStorage::class,
		'debug_mode' => '%debugMode%',
	];

	/**
	 * {@inheritdoc}
	 */
	public function loadConfiguration(): void
	{
		$builder = $this->getContainerBuilder();

		/** @noinspection PhpInternalEntityUsedInspection */
		$config = $this->validateConfig(Nette\DI\Helpers::expand($this->defaults, $builder->parameters));

		$builder->addDefinition($this->prefix('transactionFactory'))
			->setImplement(SixtyEightPublishers\DoctrinePersistence\Transaction\ITransactionFactory::class);

		$storage = $config['reader_storage'];

		if (!is_string($storage) || !Nette\Utils\Strings::startsWith($storage, '@')) {
			$storage = $builder->addDefinition($this->prefix('reader.storage'))
				->setType(Nette\Caching\IStorage::class)
				->setFactory($storage);
		}

		$builder->addDefinition($this->prefix('reader'))
			->setType(SixtyEightPublishers\DoctrinePersistence\Reader\IReader::class)
			->setFactory(new Nette\DI\Statement(SixtyEightPublishers\DoctrinePersistence\Reader\CachedReader::class, [
				new Nette\DI\Statement(SixtyEightPublishers\DoctrinePersistence\Reader\Reader::class),
				$storage,
				(bool) $config['debug_mode'],
			]));
	}

	/**
	 * {@inheritdoc}
	 */
	public function afterCompile(Nette\PhpGenerator\ClassType $class): void
	{
		$initialize = $class->methods['initialize'];

		$initialize->addBody('?::setReader($this->getService(?));', [
			new Nette\PhpGenerator\PhpLiteral(SixtyEightPublishers\DoctrinePersistence\DTO\DTOFactory::class),
			$this->prefix('reader'),
		]);
	}
}
