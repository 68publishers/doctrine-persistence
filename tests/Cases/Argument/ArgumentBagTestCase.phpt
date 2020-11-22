<?php

declare(strict_types=1);

namespace SixtyEightPublishers\DoctrinePersistence\Tests\Cases\Argument;

use stdClass;
use ArrayObject;
use Tester\Assert;
use Tester\TestCase;
use SixtyEightPublishers\DoctrinePersistence\Argument\ArgumentBag;
use SixtyEightPublishers\DoctrinePersistence\Exception\InvalidArgumentException;

require __DIR__ . '/../../bootstrap.php';

class ArgumentBagTestCase extends TestCase
{
	/**
	 * @return void
	 */
	public function testArgumentBagConsistency(): void
	{
		$argumentBag = new ArgumentBag([
			'a' => 'A',
			'b' => 15,
			'foo' => $foo = new stdClass(),
			'bar' => NULL,
			'baz' => $baz = new ArrayObject([]),
		]);

		# ::has()
		Assert::true($argumentBag->has('a'));
		Assert::true($argumentBag->has('b'));
		Assert::true($argumentBag->has('foo'));
		Assert::true($argumentBag->has('bar'));
		Assert::true($argumentBag->has('baz'));
		Assert::false($argumentBag->has('undefined'));

		# ::get()
		Assert::same('A', $argumentBag->get('a'));
		Assert::same(15, $argumentBag->get('b'));
		Assert::same($foo, $argumentBag->get('foo'));
		Assert::null($argumentBag->get('bar'));
		Assert::same($baz, $argumentBag->get('baz'));

		Assert::exception(static function () use ($argumentBag) {
			$argumentBag->get('undefined');
		}, InvalidArgumentException::class, 'Missing argument with name "undefined".');

		# ::add()
		Assert::noError(static function () use ($argumentBag) {
			$argumentBag->add('c', 'something');
		});

		Assert::same('something', $argumentBag->get('c'));

		Assert::exception(static function () use ($argumentBag) {
			$argumentBag->add('a', 'something');
		}, InvalidArgumentException::class, 'Argument with a key "a" is already defined.');

		# Countable
		Assert::same(6, $argumentBag->count());
		Assert::same(6, count($argumentBag));

		// IteratorAggregate
		Assert::equal([
			'a' => 'A',
			'b' => 15,
			'foo' => $foo,
			'bar' => NULL,
			'baz' => $baz,
			'c' => 'something',
		], iterator_to_array($argumentBag));

		// ::withArguments(?, FALSE)
		$nonMergedArgumentBag = $argumentBag->withArguments([
			'product' => 'Iphone',
		], FALSE);

		Assert::equal([
			'product' => 'Iphone',
		], iterator_to_array($nonMergedArgumentBag));

		// ::withArguments(?, TRUE)
		$mergedArgumentBag = $argumentBag->withArguments([
			'product' => 'Iphone',
		], TRUE);

		Assert::equal([
			'a' => 'A',
			'b' => 15,
			'foo' => $foo,
			'bar' => NULL,
			'baz' => $baz,
			'c' => 'something',
			'product' => 'Iphone',
		], iterator_to_array($mergedArgumentBag));
	}
}

(new ArgumentBagTestCase())->run();
