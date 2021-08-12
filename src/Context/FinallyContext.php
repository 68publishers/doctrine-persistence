<?php

declare(strict_types=1);

namespace SixtyEightPublishers\DoctrinePersistence\Context;

use Throwable;
use SixtyEightPublishers\DoctrinePersistence\Helper\TransactionHelper;
use SixtyEightPublishers\DoctrinePersistence\Exception\TransactionMustBeCommittedException;

final class FinallyContext implements FinallyContextInterface
{
	use CommonContextProxyTrait;

	/** @var mixed  */
	private $result;

	private ?Throwable $error;

	/**
	 * @param \SixtyEightPublishers\DoctrinePersistence\Context\CommonContextInterface $commonContext
	 * @param mixed                                                                    $result
	 * @param \Throwable|NULL                                                          $error
	 */
	public function __construct(CommonContextInterface $commonContext, $result, ?Throwable $error)
	{
		$this->commonContext = $commonContext;
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
		return $this->getEntityManager()->getConnection()->getTransactionNestingLevel();
	}

	/**
	 * {@inheritDoc}
	 */
	public function isEverythingCommitted(): bool
	{
		return TransactionHelper::isEverythingCommitted($this->getEntityManager()->getConnection());
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
		return new self($this->commonContext, $this->result, $error);
	}
}
