<?php

declare(strict_types=1);

namespace SixtyEightPublishers\DoctrinePersistence\Exception;

use Exception;

final class TransactionMustBeCommittedException extends Exception implements ExceptionInterface
{
	public function __construct()
	{
		parent::__construct('', 0);
	}
}
