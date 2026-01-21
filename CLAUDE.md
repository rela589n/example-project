# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Symfony 7.3 enterprise backend API with PHP 8.4+, RoadRunner async runtime, and modular feature-based architecture.

## Common Commands

### Development
```bash
docker compose up -d                                                      # Start all services
docker compose exec backend bin/console lexik:jwt:generate-keypair --overwrite  # Generate JWT keys
docker compose exec backend composer app:recreate-test-database           # Recreate test database with fixtures
```

### Testing
Run inside backend container (`docker compose exec backend`):
```bash
composer ci:unit-tests         # Unit tests (*UnitTest.php)
composer ci:integration-tests  # Integration tests (*ServiceTest.php)
composer ci:functional-tests   # Functional/API tests (*ApiPointTest.php)
composer ci:tests              # Run all test suites
bin/phpunit --filter=TestClassName  # Run single test class
```

### Code Quality
Run inside backend container (`docker compose exec backend`):
```bash
composer ci:ecs                # Check code style (ECS with PSR-12)
composer ci:ecs-fix            # Auto-fix code style
composer ci:phpstan            # Static analysis (level: max)
composer ci:rector             # Rector dry-run
composer ci:rector-fix         # Apply Rector fixes
composer ci:pack               # Full CI suite
```

### Database
Run inside backend container (`docker compose exec backend`):
```bash
composer app:recreate-test-database   # Test environment
composer app:recreate-dev-database    # Dev environment
bin/console doctrine:migrations:migrate --no-interaction
```

## Architecture

### Domain Structure
```
src/
├── EmployeePortal/     # Business domains (Authentication, Voucher, Blog, Profile)
├── Playground/         # Experimental features (AmPHP, gRPC, Temporal, Workflows)
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
- **Events**: Core business logic in `process()` method; dispatched to event bus after processing
- **Commands**: Self-contained action descriptors with `PhPhD\ExceptionalValidation` attributes
- **Services**: Injected as method parameters to Commands, provide repository access
- **Outbox**: Post-action async operations (e.g., sending emails after registration)

### Code Standards
- `declare(strict_types=1)` on all files
- Heavy use of `readonly` properties and classes
- PHPStan level: max
- All return/parameter/property types required

## Infrastructure

- **Runtime**: RoadRunner (HTTP :8083 SSL, gRPC :50051)
- **Database**: PostgreSQL 17 with Doctrine ORM + Cycle ORM
- **Cache/Sessions**: Redis 8.0
- **Message Queue**: Symfony Messenger (Doctrine/AMQP/Redis transports)
- **Workflows**: Temporal SDK
- **WebSockets**: Centrifugo
- **File Storage**: Flysystem with MinIO

## Debugging Temporal

```bash
docker compose logs --no-log-prefix -f dump_server | lnav
```

## gRPC

Generate PHP code from proto files:
```bash
protoc \
   --php_out=./src/Support/Contracts/Playground/HelloWorld/ \
   --php-grpc_out=./src/Support/Contracts/Playground/HelloWorld/ \
   ./src/Support/Contracts/Playground/HelloWorld/hello-world.proto
```
