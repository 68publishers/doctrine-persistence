<?php

declare(strict_types=1);

namespace SixtyEightPublishers\DoctrinePersistence\Assembler;

use Doctrine;

final class SimpleORMManyToOneAssembler extends ORMManyToOneAssembler
{
	/**
	 * @param string                               $dtoClass
	 * @param string                               $entityClass
	 * @param string                               $ownerEntityClass
	 * @param \Doctrine\ORM\EntityManagerInterface $em
	 */
	public function __construct(string $dtoClass, string $entityClass, string $ownerEntityClass, Doctrine\ORM\EntityManagerInterface $em)
	{
		parent::__construct($em);

		$this->dtoClass = $dtoClass;
		$this->entityClass = $entityClass;
		$this->ownerEntityClass = $ownerEntityClass;
	}

	/**
	 * @param string $associationFieldName
	 *
	 * @return void
	 */
	public function setAssociationFieldName(string $associationFieldName): void
	{
		$this->associationFieldName = $associationFieldName;
	}
}
