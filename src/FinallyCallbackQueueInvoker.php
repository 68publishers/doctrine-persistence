<?php

declare(strict_types=1);

namespace SixtyEightPublishers\DoctrinePersistence;

use SixtyEightPublishers\DoctrinePersistence\Context\FinallyContextInterface;
use SixtyEightPublishers\DoctrinePersistence\Exception\TransactionMustBeCommittedException;

class FinallyCallbackQueueInvoker
{
	/** @var array  */
	private $callbacks = [];

	/**
	 * @param callable                                                                  $callback
	 * @param \SixtyEightPublishers\DoctrinePersistence\Context\FinallyContextInterface $context
	 *
	 * @return void
	 */
	public function enqueue(callable $callback, FinallyContextInterface $context): void
	{
		$this->callbacks[] = [$callback, $context, FALSE];
	}

	/**
	 * @return void
	 */
	public function invoke(): void
	{
		/**
		 * @var callable                                                                  $callback
		 * @var \SixtyEightPublishers\DoctrinePersistence\Context\FinallyContextInterface $context
		 * @var bool                                                                      $needsEverythingCommitted
		 */
		foreach ($this->callbacks as $key => [$callback, $context, $needsEverythingCommitted]) {
			if (FALSE === $needsEverythingCommitted) {
				try {
					$callback($context);
					unset($this->callbacks[$key]);
				} catch (TransactionMustBeCommittedException $e) {
					$this->callbacks[$key][2] = TRUE;
				}

				continue;
			}

			if ($context->isEverythingCommitted()) {
				$callback($context);
				unset($this->callbacks[$key]);
			}
		}
	}
}
