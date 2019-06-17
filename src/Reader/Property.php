<?php

declare(strict_types=1);

namespace SixtyEightPublishers\DoctrinePersistence\Reader;

final class Property
{
	/** @var string  */
	public $name = '';

	/** @var NULL|string  */
	public $validator = NULL;

	/** @var bool  */
	public $nullable = FALSE;
}
