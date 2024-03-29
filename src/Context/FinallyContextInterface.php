<?php

declare(strict_types=1);

namespace SixtyEightPublishers\DoctrinePersistence\Context;

use Throwable;

interface FinallyContextInterface extends CommonContextInterface
{
	/**
	 * Last result
	 *
	 * @return mixed
	 */
	public function getResult();

	/**
	 * @return \Throwable|NULL
	 */
	public function getError(): ?Throwable;

	/**
	 * @return bool
	 */
	public function hasError(): bool;

	/**
	 * @return int
	 */
	public function getTransactionNestedLevel(): int;

	/**
	 * @return bool
	 */
	public function isEverythingCommitted(): bool;

	/**
	 * @return void
	 * @throws \SixtyEightPublishers\DoctrinePersistence\Exception\TransactionMustBeCommittedException
	 */
	public function needsEverythingCommitted(): void;

	/**
	 * @param \Throwable|NULL $error
	 *
	 * @return \SixtyEightPublishers\DoctrinePersistence\Context\FinallyContextInterface
	 */
	public function withError(?Throwable $error): self;
}
