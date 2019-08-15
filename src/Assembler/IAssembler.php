<?php

declare(strict_types=1);

namespace SixtyEightPublishers\DoctrinePersistence\Assembler;

use SixtyEightPublishers;

interface IAssembler
{
	/**
	 * @return \SixtyEightPublishers\DoctrinePersistence\DTO\AbstractDTO
	 */
	public function assembleEmptyDTO(): SixtyEightPublishers\DoctrinePersistence\DTO\AbstractDTO;

	/**
	 * @param object $entity
	 *
	 * @return \SixtyEightPublishers\DoctrinePersistence\DTO\AbstractDTO
	 */
	public function assembleDTOFromEntity($entity): SixtyEightPublishers\DoctrinePersistence\DTO\AbstractDTO;

	/**
	 * @param mixed $identifier
	 *
	 * @return \SixtyEightPublishers\DoctrinePersistence\DTO\AbstractDTO
	 */
	public function assembleDTOFromIdentifier($identifier): SixtyEightPublishers\DoctrinePersistence\DTO\AbstractDTO;

	/**
	 * @param mixed $identifier
	 *
	 * @return object
	 */
	public function assembleEntityFromIdentifier($identifier);
}
