<?php

declare(strict_types=1);

namespace SixtyEightPublishers\DoctrinePersistence\Identifier;

interface IIdentifier
{
	/**
	 * @return mixed
	 * @throws \SixtyEightPublishers\DoctrinePersistence\Exception\MalformedIdentifierException
	 */
	public function getValue();
}
