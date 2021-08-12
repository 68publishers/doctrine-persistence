<?php

declare(strict_types=1);

namespace SixtyEightPublishers\DoctrinePersistence\Bridge\Nette\TransactionExtender;

use Nette\Application\AbortException;
use Nette\Application\ForbiddenRequestException;
use SixtyEightPublishers\DoctrinePersistence\TransactionInterface;
use SixtyEightPublishers\DoctrinePersistence\Badge\NonLoggableErrorBadge;
use SixtyEightPublishers\DoctrinePersistence\Context\ErrorContextInterface;

final class RedirectTransactionExtender
{
	/**
	 * @param \SixtyEightPublishers\DoctrinePersistence\TransactionInterface $transaction
	 */
	public function __invoke(TransactionInterface $transaction): void
	{
		$transaction->error(static function (ErrorContextInterface $context, AbortException $e): void {
			$context->stopPropagation();
			$context->addBadges(new NonLoggableErrorBadge($e));
		});

		$transaction->error(static function (ErrorContextInterface $context, ForbiddenRequestException $e): void {
			$context->stopPropagation();
			$context->addBadges(new NonLoggableErrorBadge($e));
		});
	}
}
