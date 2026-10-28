<?php

declare(strict_types=1);

namespace SixtyEightPublishers\DoctrinePersistence\Tests\Cases\Exception\Persistence;

use Tester\Assert;
use Tester\TestCase;
use SixtyEightPublishers\DoctrinePersistence\Exception\Persistence\EntityNotFoundException;

require __DIR__ . '/../../../bootstrap.php';

class EntityNotFoundExceptionTestCase extends TestCase
{
	public function testScalarIdentifier(): void
	{
		$e = new EntityNotFoundException('App\Entity', 15);

		Assert::same('Entity App\Entity with identifier 15 not found.', $e->getMessage());
		Assert::same(15, $e->getIdentifier());
	}

	public function testCompositeIdentifier(): void
	{
		$e = new EntityNotFoundException('App\Entity', ['a' => 1, 'b' => 'č']);

		Assert::same('Entity App\Entity with identifier {"a":1,"b":"č"} not found.', $e->getMessage());
		Assert::same(['a' => 1, 'b' => 'č'], $e->getIdentifier());
	}
}

(new EntityNotFoundExceptionTestCase())->run();
