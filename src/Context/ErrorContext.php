<?php

declare(strict_types=1);

namespace SixtyEightPublishers\DoctrinePersistence\Context;

use Throwable;

final class ErrorContext implements ErrorContextInterface
{
	/** @var \Throwable  */
	private $error;

	/** @var bool  */
	private $propagationStopped = FALSE;

	/** @var bool  */
	private $defaultBehaviourPrevented = FALSE;

	/**
	 * @param \Throwable $error
	 */
	public function __construct(Throwable $error)
	{
		$this->error = $error;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getError(): Throwable
	{
		return $this->error;
	}

	/**
	 * {@inheritDoc}
	 */
	public function stopPropagation(): void
	{
		$this->propagationStopped = TRUE;
	}

	/**
	 * {@inheritDoc}
	 */
	public function preventDefault(): void
	{
		$this->defaultBehaviourPrevented = TRUE;
	}

	/**
	 * {@inheritDoc}
	 */
	public function isPropagationStopped(): bool
	{
		return $this->propagationStopped;
	}

	/**
	 * {@inheritDoc}
	 */
	public function isDefaultBehaviourPrevented(): bool
	{
		return $this->defaultBehaviourPrevented;
	}
}
