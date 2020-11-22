<?php

declare(strict_types=1);

namespace SixtyEightPublishers\DoctrinePersistence\Helper;

use Nette\StaticClass;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;

final class TransactionHelper
{
	use StaticClass;

	/**
	 * @param \Doctrine\DBAL\Connection|\Doctrine\ORM\EntityManagerInterface $connection
	 *
	 * @return bool
	 */
	public static function isEverythingCommitted($connection): bool
	{
		assert($connection instanceof Connection || $connection instanceof EntityManagerInterface);

		if ($connection instanceof EntityManagerInterface) {
			$connection = $connection->getConnection();
		}

		return 0 >= $connection->getTransactionNestingLevel();
	}
}
