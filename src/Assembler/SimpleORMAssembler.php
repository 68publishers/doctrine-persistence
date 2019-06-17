<?php

declare(strict_types=1);

namespace SixtyEightPublishers\DoctrinePersistence\Assembler;

use Doctrine;

final class SimpleORMAssembler extends ORMAssembler
{
	/**
	 * @param string                               $dtoClass
	 * @param string                               $entityClass
	 * @param \Doctrine\ORM\EntityManagerInterface $em
	 */
	public function __construct(string $dtoClass, string $entityClass, Doctrine\ORM\EntityManagerInterface $em)
	{
		parent::__construct($em);

		$this->dtoClass = $dtoClass;
		$this->entityClass = $entityClass;
	}
}
