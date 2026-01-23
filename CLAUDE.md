# CLAUDE.md

## Project Overview

Symfony 7.3 enterprise backend API with PHP 8.4+, RoadRunner async runtime, and modular feature-based architecture.

## Architecture

### Project Structure
```
src/
├── EmployeePortal/     # Business domains (Authentication, Voucher, Blog, Profile)
├── Playground/         # Experimental features
├── Infra/              # Infrastructure (WebSocket/Centrifugo)
└── Support/            # Shared utilities (API, Contracts, Doctrine, MessageBus, Temporal)
```

### Feature Pattern
Each feature follows this structure:
```
Features/{FeatureName}/
├── Port/
│   ├── {Feature}Command.php       # Action descriptor with validation
│   ├── {Feature}Service.php       # Scoped dependencies
│   ├── Api/
│   │   ├── {Feature}ApiPoint.php      # REST endpoint
│   │   └── {Feature}ApiPointTest.php  # HTTP client test
│   └── Console/{Feature}ConsoleCommand.php
├── {Feature}Event.php             # Domain event with process() logic
└── Outbox/                        # Post-action operations (emails, etc.)
```

### Key Patterns

#### Event-first

Core business logic belongs to `process()` method.
Event is dispatched to event bus after processing;

#### Command/Service Pattern

Core application logic belongs to Command's `process()` method.
Services provide Commands with access to DI services.

#### Validation

Validation is Domain-first using Symfony Validator in Value-Objects.
Constraint violations are mapped to the Command using `PhPhD\ExceptionalValidation` attributes.

#### Outbox

Outbox directory contains post-action async operations (e.g., sending emails after registration)

### Code Standards
- `declare(strict_types=1)` on all files
- Heavy use of `readonly` properties and classes
- PHPStan level: max
- All return/parameter/property types required

## Development

### Docker
Environment is dockerized.\
Custom images are in `docker-configs/`.

Start all services:
```bash
docker compose up -d
```
Every maintenance operation (`composer`, CI scripts, tests) must be ran from the container:

```shell
docker compose exec backend $your_command
```

### Commands

```bash
composer app:recreate-test-database
```

### Testing

Composer scripts:
- `ci:tests` - run all tests
- `ci:unit-tests`
- `ci:integration-tests`
- `ci:functional-tests`

Single test:
```bash
bin/phpunit --filter=SingleTestClassName
```

### Code Quality
Composer scripts:
- `ci:pack` - Full CI suite
- `ci:ecs` - Check code style
- `ci:ecs-fix` - Fix code style
- `ci:phpstan` - Static analysis (level: max
- `ci:rector` - Rector dry-run
- `ci:rector-fix` - Apply Rector fixes (DANGEROUS)

### Database

Composer scripts:
- `app:recreate-test-database`
- `app:recreate-dev-database`

Console scripts:
- `doctrine:migrations:migrate --no-interaction`

## Infrastructure

- **Runtime**: RoadRunner (HTTP :8083 SSL, gRPC :50051)
- **Database**: PostgreSQL 17 with Doctrine ORM
- **Cache/Sessions**: Redis 8.0
- **Message Queue**: Symfony Messenger (Doctrine/AMQP/Redis transports)
- **Workflows**: Temporal SDK
- **WebSockets**: Centrifugo
- **File Storage**: MinIO with Flysystem 

## Debugging Temporal

```bash
docker compose logs --no-log-prefix -f dump_server | lnav
```
