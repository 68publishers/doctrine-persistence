<?php

declare(strict_types=1);

namespace SixtyEightPublishers\DoctrinePersistence\Context;

final class TransactionContext implements TransactionContextInterface
{
	use CommonContextProxyTrait;

	private $previousResult;

	/**
	 * @param \SixtyEightPublishers\DoctrinePersistence\Context\CommonContextInterface $commonContext
	 * @param mixed                                                                    $previousResult
	 */
	public function __construct(CommonContextInterface $commonContext, $previousResult)
	{
		$this->commonContext = $commonContext;
		$this->previousResult = $previousResult;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getPreviousResult()
	{
		return $this->previousResult;
	}

	/**
	 * {@inheritDoc}
	 */
	public function persist(object $entity): TransactionContextInterface
	{
		$this->getEntityManager()->persist($entity);

		return $this;
	}
}
