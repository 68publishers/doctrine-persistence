<?php

declare(strict_types=1);

namespace SixtyEightPublishers\DoctrinePersistence\Context;

use Throwable;
use Doctrine\DBAL\Connection;
use SixtyEightPublishers\DoctrinePersistence\Helper\TransactionHelper;
use SixtyEightPublishers\DoctrinePersistence\Exception\TransactionMustBeCommittedException;

final class FinallyContext implements FinallyContextInterface
{
	/** @var \Doctrine\DBAL\Connection  */
	private $connection;

	/** @var mixed  */
	private $result;

	/** @var \Throwable|NULL  */
	private $error;

	/**
	 * @param \Doctrine\DBAL\Connection $connection
	 * @param mixed                     $result
	 * @param \Throwable|NULL           $error
	 */
	public function __construct(Connection $connection, $result, ?Throwable $error)
	{
		$this->connection = $connection;
		$this->result = $result;
		$this->error = $error;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getResult()
	{
		return $this->result;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getError(): ?Throwable
	{
		return $this->error;
	}

	/**
	 * {@inheritDoc}
	 */
	public function hasError(): bool
	{
		return NULL !== $this->error;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getTransactionNestedLevel(): int
	{
		return $this->connection->getTransactionNestingLevel();
	}

	/**
	 * {@inheritDoc}
	 */
	public function isEverythingCommitted(): bool
	{
		return TransactionHelper::isEverythingCommitted($this->connection);
	}

	/**
	 * {@inheritDoc}
	 */
	public function needsEverythingCommitted(): void
	{
		if (!$this->isEverythingCommitted()) {
			throw new TransactionMustBeCommittedException();
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function withError(?Throwable $error): FinallyContextInterface
	{
		return new self($this->connection, $this->result, $error);
	}
}
