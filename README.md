# Doctrine Persistence

Abstraction layers and utils for data persistence in Nette Framework via Doctrine - transaction object, entity persisting via DTOs and Persisters etc. ... 

## Installation

The best way to install 68publishers/doctrine-persistence is using Composer:

```bash
composer require 68publishers/doctrine-persistence
```

then you can register extension into DIC:

```yaml
extensions:
    doctrine_persistence: SixtyEightPublishers\DoctrinePersistence\DI\DoctrinePersistenceExtension
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
