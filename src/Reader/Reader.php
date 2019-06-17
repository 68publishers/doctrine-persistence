<?php

declare(strict_types=1);

namespace SixtyEightPublishers\DoctrinePersistence\Reader;

use Nette;

final class Reader implements IReader
{
	use Nette\SmartObject;

	/******************** interface \SixtyEightPublishers\DoctrinePersistence\Reader\Reader ********************/

	/**
	 * {@inheritdoc}
	 */
	public function getClassProperties(string $className): array
	{
		$annotations = Nette\Reflection\AnnotationsParser::getAll(new \ReflectionClass($className));

		if (!isset($annotations['property'])) {
			return [];
		}

		$properties = $annotations['property'];
		$result = [];

		foreach ($properties as $property) {
			preg_match('/(?P<TYPE>[^\s]+) \$(?P<NAME>[^\s]+)/', $property, $matches);

			if (!isset($matches['TYPE']) || !isset($matches['NAME'])) {
				continue;
			}

			$nullable = FALSE;
			$validator = implode('|', array_filter(explode('|', $matches['TYPE']), function (string $type) use (&$nullable) {
				if (in_array($type, [ 'NULL', 'null' ])) {
					$nullable = TRUE;
				}

				return 'mixed' !== $type;
			}));

			$prop = new Property();
			$prop->name = $matches['NAME'];
			$prop->nullable = $nullable;
			$prop->validator = empty($validator) ? NULL : $validator;

			$result[$prop->name] = $prop;
		}

		return $result;
	}
}
