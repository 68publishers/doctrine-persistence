<?php

declare(strict_types=1);

namespace SixtyEightPublishers\DoctrinePersistence;

interface TransactionInterface
{
	public const ARGUMENT_NAME_RESULT = 'result';

	/**
	 * @param iterable $arguments
	 *
	 * @return \SixtyEightPublishers\DoctrinePersistence\TransactionInterface
	 */
	public function withArguments(iterable $arguments): TransactionInterface;

	/**
	 * Adds new callback into a transaction.
	 * The result returned from the previous callback is passed into the next callback under the name `$result`.
	 *
	 * ```php
	 * <?php
	 *     $args = [
	 *         'id' => 15,
	 *         'name' => 'John',
	 *         'password' => 'pass',
	 *         'agreement' => TRUE,
	 *     ];
	 *
	 *     $transaction = $transactionFactory->create(function (EntityManagerInterface $em, int $id) {
	 *         $user = $em->getRepository(User::class)->find($id) ?? new User($id);
	 *         $em->persist($user);
	 *
	 *         return $user;
	 *     }, $args);
	 *
	 *     $transaction->then(function (User $result, EntityManagerInterface $em, string $name, string $password) {
	 *         $user->setName($name);
	 *         $user->setPassword($password);
	 *
	 *         return $result;
	 *     });
	 *
	 *     $transaction->then(function (EntityManagerInterface $em, User $result, bool $agreement) {
	 *         $user->setAgreement($agreement);
	 *
	 *         return $result;
	 *     });
	 *
	 *     $user = $transaction->run();
	 * ```
	 *
	 * @param callable $callback
	 *
	 * @return \SixtyEightPublishers\DoctrinePersistence\TransactionInterface
	 */
	public function then(callable $callback): TransactionInterface;

	/**
	 * This callback is called when a current transaction successfully ends.
	 * The only parameter is a value returned from the first (initial) callback.
	 *
	 * Note: everything may not be committed if the transaction is wrapped into a another transaction!
	 *
	 * ```php
	 * <?php
	 *     $transaction->success(function ($value) {});
	 * ```
	 *
	 * @param callable $callback
	 *
	 * @return \SixtyEightPublishers\DoctrinePersistence\TransactionInterface
	 */
	public function success(callable $callback): TransactionInterface;

	/**
	 * Callback can specify an Exception type with callback's second argument
	 *
	 * ```php
	 * <?php
	 *     $transaction->finally(function (ErrorContextInterface $context, RuntimeException $e) {
	 *         // do something with RuntimeException
	 *     });
	 *
	 *     $transaction->finally(function (ErrorContextInterface $context, LogicException $e) {
	 *         // do something with LogicException
	 *     });
	 *
	 *     $transaction->finally(function (ErrorContextInterface $context) {
	 *         // do something with any exception, both previous exceptions will be passed into this callback too if a propagation is not stopped.
	 *     });
	 * ```
	 *
	 * @param callable $callback
	 *
	 * @return \SixtyEightPublishers\DoctrinePersistence\TransactionInterface
	 */
	public function error(callable $callback): TransactionInterface;

	/**
	 * The callback is invoked always after successful or unsuccessful Transaction
	 *
	 * ```php
	 * <?php
	 *     $transaction->finally(function (FinallyContextInterface $context) {});
	 * ```
	 *
	 * @param callable $callback
	 *
	 * @return \SixtyEightPublishers\DoctrinePersistence\TransactionInterface
	 */
	public function finally(callable $callback): TransactionInterface;

	/**
	 * Runs transaction, each transaction instance can be invoked only once.
	 *
	 * @return mixed
	 */
	public function run();

	/**
	 * Calls ::run() internally
	 *
	 * @return mixed
	 */
	public function __invoke();
}
