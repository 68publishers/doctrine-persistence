<?php

declare(strict_types=1);

namespace SixtyEightPublishers\DoctrinePersistence\Context;

use SixtyEightPublishers\DoctrinePersistence\Helper\TransactionHelper;

final class SuccessContext implements SuccessContextInterface
{
	use CommonContextProxyTrait;

	private $result;

	/**
	 * @param \SixtyEightPublishers\DoctrinePersistence\Context\CommonContextInterface $commonContext
	 * @param mixed                                                                    $result
	 */
	public function __construct(CommonContextInterface $commonContext, $result)
	{
		$this->commonContext = $commonContext;
		$this->result = $result;
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
	public function isEverythingCommitted(): bool
	{
		return TransactionHelper::isEverythingCommitted($this->getEntityManager()->getConnection());
	}
}
