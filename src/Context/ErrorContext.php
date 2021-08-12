<?php

declare(strict_types=1);

namespace SixtyEightPublishers\DoctrinePersistence\Context;

use Throwable;

final class ErrorContext implements ErrorContextInterface
{
	use CommonContextProxyTrait;

	private Throwable $error;

	private bool $propagationStopped = FALSE;

	private bool $defaultBehaviourPrevented = FALSE;

	/**
	 * @param \SixtyEightPublishers\DoctrinePersistence\Context\CommonContextInterface $commonContext
	 * @param \Throwable                                                               $error
	 */
	public function __construct(CommonContextInterface $commonContext, Throwable $error)
	{
		$this->commonContext = $commonContext;
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
	public function stopPropagation(): ErrorContextInterface
	{
		$this->propagationStopped = TRUE;

		return $this;
	}

	/**
	 * {@inheritDoc}
	 */
	public function preventDefault(): ErrorContextInterface
	{
		$this->defaultBehaviourPrevented = TRUE;

		return $this;
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
