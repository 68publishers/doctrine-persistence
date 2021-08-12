<?php

declare(strict_types=1);

namespace SixtyEightPublishers\DoctrinePersistence\TransactionExtender;

use Psr\Log\LoggerInterface;
use SixtyEightPublishers\DoctrinePersistence\TransactionInterface;
use SixtyEightPublishers\DoctrinePersistence\Badge\LoggedErrorBadge;
use SixtyEightPublishers\DoctrinePersistence\Badge\NonLoggableErrorBadge;
use SixtyEightPublishers\DoctrinePersistence\Context\ErrorContextInterface;
use SixtyEightPublishers\DoctrinePersistence\Context\FinallyContextInterface;

final class ErrorLogTransactionExtender
{
	private LoggerInterface $logger;

	private array $defaultNonLoggableErrorTypes = [];

	/**
	 * @param \Psr\Log\LoggerInterface $logger
	 */
	public function __construct(LoggerInterface $logger)
	{
		$this->logger = $logger;
	}

	/**
	 * @param array $defaultNonLoggableErrorTypes
	 *
	 * @return \SixtyEightPublishers\DoctrinePersistence\TransactionExtender\ErrorLogTransactionExtender
	 */
	public function setDefaultNonLoggableErrorTypes(array $defaultNonLoggableErrorTypes): self
	{
		$this->defaultNonLoggableErrorTypes = (static fn (string ...$defaultNonLoggableErrorTypes): array => $defaultNonLoggableErrorTypes)(...$defaultNonLoggableErrorTypes);

		return $this;
	}

	/**
	 * @param \SixtyEightPublishers\DoctrinePersistence\TransactionInterface $transaction
	 *
	 * @return void
	 */
	public function __invoke(TransactionInterface $transaction): void
	{
		if (!empty($this->defaultNonLoggableErrorTypes)) {
			$transaction->error(function (ErrorContextInterface $context): void {
				$error = $context->getError();

				foreach ($this->defaultNonLoggableErrorTypes as $defaultNonLoggableErrorType) {
					if ($error instanceof $defaultNonLoggableErrorType) {
						$context->addBadges(new NonLoggableErrorBadge($error));

						return;
					}
				}
			});
		}

		$transaction->finally(function (FinallyContextInterface $context): void {
			$context->needsEverythingCommitted();

			if (!$context->hasError()) {
				return;
			}

			$error = $context->getError();

			foreach ($context->getAllBadges() as $badge) {
				if ($badge instanceof LoggedErrorBadge && $error === $badge->getError()) {
					return;
				}

				if ($badge instanceof NonLoggableErrorBadge && $error === $badge->getError()) {
					return;
				}
			}

			# Dump the error into Tracy Bar if exists
			if (function_exists('bdump')) {
				bdump($error);
			}

			# Send the error into a logger
			$this->logger->error((string) $error);
			$context->addBadges(new LoggedErrorBadge($error));
		});
	}
}
