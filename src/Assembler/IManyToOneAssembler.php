<?php

declare(strict_types=1);

namespace SixtyEightPublishers\DoctrinePersistence\Assembler;

use Doctrine;
use SixtyEightPublishers;

interface IManyToOneAssembler extends IAssembler
{
	/**
	 * @param mixed $ownerIdentifier
	 *
	 * @return \Doctrine\Common\Collections\Collection
	 */
	public function assembleDTOCollection($ownerIdentifier): Doctrine\Common\Collections\Collection;
}
