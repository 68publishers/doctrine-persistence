<?php

declare(strict_types=1);

namespace SixtyEightPublishers\DoctrinePersistence\Assembler;

use Doctrine;
use SixtyEightPublishers;

abstract class ORMManyToOneAssembler extends ORMAssembler implements IManyToOneAssembler
{
	/** @var string */
	protected $ownerEntityClass;

	/**
	 * NULL === try detect
	 *
	 * @var NULL|string
	 */
	protected $associationFieldName;

	/**
	 * @return string
	 */
	protected function autodetectAssociationFieldName(): string
	{
		$cm = $this->em->getClassMetadata($this->entityClass);
		$associations = $cm->getAssociationsByTargetClass($this->ownerEntityClass);

		if (!count($associations)) {
			throw new SixtyEightPublishers\DoctrinePersistence\Exception\RuntimeException(sprintf(
				'Association field between entity %s and owner entity %s can\'t be resolved.',
				$this->entityClass,
				$this->ownerEntityClass
			));
		}

		if (1 < count($associations)) {
			throw new SixtyEightPublishers\DoctrinePersistence\Exception\RuntimeException(sprintf(
				'There are more than one associations fields between entities %s an %s, please specify field name manually.',
				$this->entityClass,
				$this->ownerEntityClass
			));
		}

		$association = array_shift($associations);

		return $association['fieldName'];
	}

	/************ interface \SixtyEightPublishers\DoctrinePersistence\Assembler\IManyToOneAssembler ************/

	/**
	 * {@inheritdoc}
	 */
	public function assembleDTOCollection($ownerIdentifier): Doctrine\Common\Collections\Collection
	{
		if (NULL === $this->associationFieldName) {
			$this->associationFieldName = $this->autodetectAssociationFieldName();
		}

		$entities = $this->em->getRepository($this->entityClass)->findBy([
			$this->associationFieldName => $ownerIdentifier,
		]);

		return new Doctrine\Common\Collections\ArrayCollection(array_map(function ($object) {
			return $this->assembleDTOFromEntity($object);
		}, $entities));
	}
}
