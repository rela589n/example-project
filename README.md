# Example project

## Installation

Configure host names:

```shell
sudo nano /etc/hosts
```

Place following domain there:

```shell
# NPU EPO
127.0.0.1       api.example-project.work
```

Build docker image (grpc):

```shell
cd docker-configs/backend/
docker build -t example_project.backend.grpc:v1.0 -f grpc.Dockerfile ./
```

Start the server:

```shell
docker compose up -d
```

Open `https://api.example-project.work:8083` to check that everything is up and working.

Api documentation:

- https://api.example-project.work:8083/api/employee-portal/auth/doc.html

## Terms

### Api

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

### Model

Event - a class that is responsible for the inherent business logic of the application. Once the logic has been
processed, the event could be dispatched to the event.bus.

EventTest - a class that is responsible for testing the core business logic.

### gRPC

Generate library:

```shell
#bin/protoc \
#    --plugin=./bin/protoc-gen-php-grpc \
#    --php_out=./src/Support/Contracts/Common/Playground/HelloWorld/ \
#    ./src/Support/Contracts/Common/Playground/HelloWorld/hello-world.proto
```
