<?php

declare(strict_types=1);

namespace SixtyEightPublishers\DoctrinePersistence\Context;

interface TransactionContextInterface extends CommonContextInterface
{
	/**
	 * Returns result from a previous callback
	 *
	 * @return mixed
	 */
	public function getPreviousResult();

	/**
	 * @param object $entity
	 *
	 * @return \SixtyEightPublishers\DoctrinePersistence\Context\TransactionContextInterface
	 */
	public function persist(object $entity): self;

	/**
	 * @param object $entity
	 *
	 * @return \SixtyEightPublishers\DoctrinePersistence\Context\TransactionContextInterface
	 */
	public function remove(object $entity): self;
}
