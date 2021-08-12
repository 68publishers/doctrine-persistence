<?php

declare(strict_types=1);

namespace SixtyEightPublishers\DoctrinePersistence\Tests\Cases\Badge;

use Exception;
use Tester\Assert;
use Tester\TestCase;
use SixtyEightPublishers\DoctrinePersistence\Badge\BadgeBag;
use SixtyEightPublishers\DoctrinePersistence\Badge\LoggedErrorBadge;
use SixtyEightPublishers\DoctrinePersistence\Tests\Fixture\DummyBadge;
use SixtyEightPublishers\DoctrinePersistence\Badge\NonLoggableErrorBadge;

require __DIR__ . '/../../bootstrap.php';

class BadgeBagTestCase extends TestCase
{
	public function testBadgeBag(): void
	{
		$argumentBag = new BadgeBag();

		$badge1 = new DummyBadge();
		$badge2 = new DummyBadge();
		$badge3 = new NonLoggableErrorBadge(new Exception('foo'));

		$argumentBag = $argumentBag->with($badge1, $badge3);

		Assert::equal($badge1, $argumentBag->last(DummyBadge::class));
		Assert::equal($badge3, $argumentBag->last(NonLoggableErrorBadge::class));
		Assert::null($argumentBag->last(LoggedErrorBadge::class));

		Assert::equal([$badge1, $badge3], $argumentBag->all());
		Assert::equal([$badge1], $argumentBag->all(DummyBadge::class));
		Assert::equal([$badge3], $argumentBag->all(NonLoggableErrorBadge::class));
		Assert::equal([], $argumentBag->all(LoggedErrorBadge::class));

		$argumentBag = $argumentBag->with($badge2);

		Assert::equal($badge2, $argumentBag->last(DummyBadge::class));
		Assert::equal($badge3, $argumentBag->last(NonLoggableErrorBadge::class));
		Assert::null($argumentBag->last(LoggedErrorBadge::class));

		Assert::equal([$badge1, $badge2, $badge3], $argumentBag->all());
		Assert::equal([$badge1, $badge2], $argumentBag->all(DummyBadge::class));
		Assert::equal([$badge3], $argumentBag->all(NonLoggableErrorBadge::class));
		Assert::equal([], $argumentBag->all(LoggedErrorBadge::class));
	}
}

(new BadgeBagTestCase())->run();
