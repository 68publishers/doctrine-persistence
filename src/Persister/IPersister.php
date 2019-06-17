<?php

declare(strict_types=1);

namespace SixtyEightPublishers\DoctrinePersistence\Persister;

use SixtyEightPublishers;

interface IPersister
{
	/**
	 * @return \SixtyEightPublishers\DoctrinePersistence\Assembler\IAssembler
	 */
	public function getAssembler(): SixtyEightPublishers\DoctrinePersistence\Assembler\IAssembler;

	/**
	 * @param \SixtyEightPublishers\DoctrinePersistence\DTO\AbstractDTO $dto
	 *
	 * @return \SixtyEightPublishers\DoctrinePersistence\Transaction\ITransaction
	 */
	public function transactional(SixtyEightPublishers\DoctrinePersistence\DTO\AbstractDTO $dto): SixtyEightPublishers\DoctrinePersistence\Transaction\ITransaction;
}
