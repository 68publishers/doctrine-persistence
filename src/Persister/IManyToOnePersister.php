<?php

declare(strict_types=1);

namespace SixtyEightPublishers\DoctrinePersistence\Persister;

use Doctrine;
use SixtyEightPublishers;

interface IManyToOnePersister
{
	/**
	 * @return \SixtyEightPublishers\DoctrinePersistence\Assembler\IManyToOneAssembler
	 */
	public function getAssembler(): SixtyEightPublishers\DoctrinePersistence\Assembler\IManyToOneAssembler;

	/**
	 * @param mixed                                   $ownerIdentifier
	 * @param \Doctrine\Common\Collections\Collection $dtoCollection
	 *
	 * @return \SixtyEightPublishers\DoctrinePersistence\Transaction\ITransaction
	 */
	public function transactional($ownerIdentifier, Doctrine\Common\Collections\Collection $dtoCollection): SixtyEightPublishers\DoctrinePersistence\Transaction\ITransaction;
}
