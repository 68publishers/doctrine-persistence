<?php

declare(strict_types=1);

namespace SixtyEightPublishers\DoctrinePersistence\Exception\Persistence;

use Throwable;

final class NonDeletableEntityException extends PersistenceException
{
	private object $entity;

	private ?string $reason;

	/**
	 * @param object          $entity
	 * @param string          $reason
	 * @param int             $code
	 * @param \Throwable|NULL $previous
	 */
	public function __construct(object $entity, ?string $reason = NULL, $code = 0, Throwable $previous = NULL)
	{
		parent::__construct(sprintf(
			'Can\'t delete entity of type %s. %s',
			get_class($entity),
			$reason
		), $code, $previous);

		$this->entity = $entity;
		$this->reason = $reason;
	}

	/**
	 * @return object
	 */
	public function getEntity(): object
	{
		return $this->entity;
	}

	/**
	 * @return string|NULL
	 */
	public function getReason(): ?string
	{
		return $this->reason;
	}
}
