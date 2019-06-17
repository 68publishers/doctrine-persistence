<?php

declare(strict_types=1);

namespace SixtyEightPublishers\DoctrinePersistence\Reader;

use Nette;

final class CachedReader implements IReader
{
	use Nette\SmartObject;

	/** @var \SixtyEightPublishers\DoctrinePersistence\Reader\IReader  */
	private $reader;

	/** @var \Nette\Caching\Cache  */
	private $cache;

	/** @var bool  */
	private $debug;

	/** @var array  */
	private $loaded = [];

	/**
	 * @param \SixtyEightPublishers\DoctrinePersistence\Reader\IReader $reader
	 * @param \Nette\Caching\IStorage                                  $storage
	 * @param bool                                                     $debug
	 */
	public function __construct(IReader $reader, Nette\Caching\IStorage $storage, bool $debug)
	{
		$this->reader = $reader;
		$this->cache = new Nette\Caching\Cache($storage, 'SixtyEightPublishers.DoctrinePersistence.Reader.CachedReader');
		$this->debug = $debug;
	}

	/******************** interface \SixtyEightPublishers\DoctrinePersistence\Reader\Reader ********************/

	/**
	 * {@inheritdoc}
	 */
	public function getClassProperties(string $className): array
	{
		# already loaded
		if (isset($this->loaded[$className])) {
			return $this->loaded[$className];
		}

		# ignore real cache if debug mode enabled
		if (TRUE === $this->debug) {
			return $this->loaded[$className] = $this->reader->getClassProperties($className);
		}

		# use real cache
		if (NULL === ($data = $this->cache->load($className))) {
			$data = $this->cache->save($className, function () use ($className) {
				return $this->reader->getClassProperties($className);
			});
		}

		return $this->loaded[$className] = $data;
	}
}
