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


### gRPC

Generate library:

```shell
#bin/protoc \
#    --plugin=./bin/protoc-gen-php-grpc \
#    --php_out=./src/Support/Contracts/Common/Playground/HelloWorld/ \
#    ./src/Support/Contracts/Common/Playground/HelloWorld/hello-world.proto
```
