<?php

declare(strict_types=1);

namespace SixtyEightPublishers\DoctrinePersistence\Persister;

use Nette;
use Doctrine;
use SixtyEightPublishers;

abstract class ORMManyToOnePersister implements IManyToOnePersister
{
	use Nette\SmartObject;

	/** @var \SixtyEightPublishers\DoctrinePersistence\Assembler\IManyToOneAssembler  */
	private $assembler;

	/** @var \SixtyEightPublishers\DoctrinePersistence\Transaction\ITransactionFactory  */
	private $transactionFactory;

	/** @var callable  */
	private $callback;

	/**
	 * @param \SixtyEightPublishers\DoctrinePersistence\Assembler\IManyToOneAssembler   $assembler
	 * @param \SixtyEightPublishers\DoctrinePersistence\Transaction\ITransactionFactory $transactionFactory
	 */
	public function __construct(SixtyEightPublishers\DoctrinePersistence\Assembler\IManyToOneAssembler $assembler, SixtyEightPublishers\DoctrinePersistence\Transaction\ITransactionFactory $transactionFactory)
	{
		$this->assembler = $assembler;
		$this->transactionFactory = $transactionFactory;
		$this->callback = $this->createTransactionProcess();
	}

	/**
	 * Arguments are Owner's ID and Collection of DTOs
	 *
	 * @return callable
	 */
	abstract protected function createTransactionProcess(): callable;

	/********************** interface \SixtyEightPublishers\DoctrinePersistence\Persister\IManyToOnePersister **********************/

	/**
	 * {@inheritdoc}
	 */
	public function getAssembler(): SixtyEightPublishers\DoctrinePersistence\Assembler\IManyToOneAssembler
	{
		return $this->assembler;
	}

	/**
	 * {@inheritdoc}
	 */
	public function transactional($ownerIdentifier, Doctrine\Common\Collections\Collection $dtoCollection): SixtyEightPublishers\DoctrinePersistence\Transaction\ITransaction
	{
		return $this->transactionFactory->create($this->callback)->immutable($ownerIdentifier, $dtoCollection);
	}
}
