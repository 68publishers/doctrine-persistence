<?php

declare(strict_types=1);

namespace SixtyEightPublishers\DoctrinePersistence\Transaction;

use Nette;
use Doctrine;

final class Helper
{
	use Nette\StaticClass;

	/**
	 * @param \Doctrine\ORM\EntityManagerInterface $em
	 *
	 * @return bool
	 */
	public static function isEverythingCommitted(Doctrine\ORM\EntityManagerInterface $em): bool
	{
		return 0 >= $em->getConnection()->getTransactionNestingLevel();
	}
}
