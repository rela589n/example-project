# Example project

## Installation

Configure host names:

```shell
sudo nano /etc/hosts
```

Place following domain there:

```shell
127.0.0.1       api.example-project.work
```

Build docker image (grpc):

```shell
cd docker-configs/backend/
docker build -t example_project.backend.grpc:v1.1 -f grpc.Dockerfile ./
```

Run composer install:

```shell
docker compose run backend composer install
```

Start the server:

```shell
docker compose up -d
```

Generate JWT keys:

```shell
bin/console lexik:jwt:generate-keypair --overwrite
```

Recreate database:

```shell
composer app:recreate-test-database
```

Open `https://api.example-project.work:8083` to check that everything is up and working.

Api documentation:

- https://api.example-project.work:8083/api/example-project/auth/doc.html

## Terms

### Feature

> Example of action:
> [User\Features\Register](src/EmployeePortal/Authentication/User/Actions/Register)

Features are self-sufficient modules that represent some features of the system (like user registration, login).
Each feature is usually related to some entity, or concept it belongs to.

### Event

> Example of
> event: [UserRegisteredEvent.php](src/EmployeePortal/Authentication/User/Features/Register/UserRegisteredEvent.php)

Event is a class that is responsible for the core business logic of the application.
For example, during user registration, we must verify that the email is free.

All the logic should be placed in `process()` method, and if any invariant fails, it must throw an exception.
If invariants were met, the event must delegate itself to be applied by the entity itself. For example, see
`User::login()` method.

Once the event is processed, it must be dispatched into the event bus.

EventTest - a class that is responsible for testing the core business logic.

### Port

> Example of Port: `User\Features\Login\Port`

Port namespace represents classes that make the event happen.
Usually, at the top level it contains Command and Service that serve for the event by creating it, processing it, and
dispatching it into the event.bus.

### ApiPoint

ApiPoint - an entry point to the application from the API. It is a class that is responsible for retrieving the request,
sending command to the application service, and returning the response.

ApiPointTest - a class that is responsible for testing the ApiPoint using http client.

### Console

ConsoleCommand - an entry point to the application from the console. It is a class that is responsible for retrieving
console input, sending command to the application service, and writing the outputs.

ConsoleCommandTest - class that is responsible for testing the ConsoleCommand using console api.

### Service

Service - a class that represents scoped set of services that is given to Command as a method parameter.

Command - a class that represents action carried out by the application.

### Outbox

Outbox namespace represents set of inner actions that come after the main action has fulfilled its responsibility.

### Dump Server

To debug Temporal Workflows, you can use var-dump-server, and then analyse output with lnav:

```shell
docker compose logs --no-log-prefix -f dump_server | lnav
```

### gRPC

Generate library:

```shell
protoc \
   --php_out=./src/Support/Contracts/Playground/HelloWorld/ \
   --php-grpc_out=./src/Support/Contracts/Playground/HelloWorld/ \
   ./src/Support/Contracts/Playground/HelloWorld/hello-world.proto
```
