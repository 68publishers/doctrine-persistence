<?php

declare(strict_types=1);

namespace SixtyEightPublishers\DoctrinePersistence\Assembler;

use Doctrine;
use SixtyEightPublishers;

abstract class ORMAssembler implements IAssembler
{
	/** @var \Doctrine\ORM\EntityManagerInterface  */
	protected $em;

	/** @var NULL|string */
	protected $dtoClass;

	/** @var NULL|string */
	protected $entityClass;

	/**
	 * @param \Doctrine\ORM\EntityManagerInterface $em
	 */
	public function __construct(Doctrine\ORM\EntityManagerInterface $em)
	{
		$this->em = $em;
	}

	/***************** interface \SixtyEightPublishers\DoctrinePersistence\Assembler\IAssembler *****************/

	/**
	 * {@inheritdoc}
	 */
	public function assembleEmptyDTO(): SixtyEightPublishers\DoctrinePersistence\DTO\AbstractDTO
	{
		return SixtyEightPublishers\DoctrinePersistence\DTO\DTOFactory::create($this->dtoClass);
	}

	/**
	 * {@inheritdoc}
	 */
	public function assembleDTOFromEntity($entity): SixtyEightPublishers\DoctrinePersistence\DTO\AbstractDTO
	{
		if (!$entity instanceof $this->entityClass) {
			throw new SixtyEightPublishers\DoctrinePersistence\Exception\InvalidArgumentException(sprintf(
				'Entity must be instance of %s',
				$this->entityClass
			));
		}

		$dto = $this->assembleEmptyDTO();
		$dto->fill($entity);

		return $dto;
	}

	/**
	 * {@inheritdoc}
	 */
	public function assembleDTOFromIdentifier($identifier): SixtyEightPublishers\DoctrinePersistence\DTO\AbstractDTO
	{
		return $this->assembleDTOFromEntity(
			$this->assembleEntityFromIdentifier($identifier)
		);
	}

	/**
	 * {@inheritdoc}
	 */
	public function assembleEntityFromIdentifier($identifier)
	{
		$entity = $this->em->getRepository($this->entityClass)->find($identifier);

		if (NULL === $entity) {
			throw new SixtyEightPublishers\DoctrinePersistence\Exception\EntityNotFoundException($this->entityClass, $identifier);
		}

		return $entity;
	}
}
