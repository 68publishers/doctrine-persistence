<?php

declare(strict_types=1);

namespace SixtyEightPublishers\DoctrinePersistence\Persister;

use Nette;
use SixtyEightPublishers;

abstract class ORMPersister implements IPersister
{
	use Nette\SmartObject;

	/** @var \SixtyEightPublishers\DoctrinePersistence\Assembler\IAssembler  */
	private $assembler;

	/** @var \SixtyEightPublishers\DoctrinePersistence\Transaction\ITransactionFactory  */
	private $transactionFactory;

	/** @var callable  */
	private $callback;

	/**
	 * @param \SixtyEightPublishers\DoctrinePersistence\Assembler\IAssembler            $assembler
	 * @param \SixtyEightPublishers\DoctrinePersistence\Transaction\ITransactionFactory $transactionFactory
	 */
	public function __construct(SixtyEightPublishers\DoctrinePersistence\Assembler\IAssembler $assembler, SixtyEightPublishers\DoctrinePersistence\Transaction\ITransactionFactory $transactionFactory)
	{
		$this->assembler = $assembler;
		$this->transactionFactory = $transactionFactory;
		$this->callback = $this->createTransactionProcess();
	}

	/**
	 * Arguments are DTO and ID
	 *
	 * @return callable
	 */
	abstract protected function createTransactionProcess(): callable;

	/********************** interface \SixtyEightPublishers\DoctrinePersistence\Persister\IPersister **********************/

	/**
	 * {@inheritdoc}
	 */
	public function getAssembler(): SixtyEightPublishers\DoctrinePersistence\Assembler\IAssembler
	{
		return $this->assembler;
	}

	/**
	 * {@inheritdoc}
	 */
	public function transactional(SixtyEightPublishers\DoctrinePersistence\DTO\AbstractDTO $dto): SixtyEightPublishers\DoctrinePersistence\Transaction\ITransaction
	{
		return $this->transactionFactory->create($this->callback)->immutable($dto);
	}
}
