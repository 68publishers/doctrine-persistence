# Doctrine Persistence

Persistence utils like transaction objects.

## Installation

The best way to install 68publishers/doctrine-persistence is using Composer:

```bash
composer require 68publishers/doctrine-persistence
```

then you can register extension into DIC:

```neon
extensions:
    68publishers.doctrine_persistence: SixtyEightPublishers\DoctrinePersistence\Bridge\Nette\DI\DoctrinePersistenceExtension
```

## Contributing

Before committing any changes, don't forget to run

```bash
vendor/bin/php-cs-fixer fix --config=.php_cs.dist -v --dry-run
```

and

```bash
vendor/bin/tester ./tests
```
