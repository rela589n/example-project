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

Start the server:

```shell
docker compose up -d
```

Open `https://api.example-project.work:8083` to check that everything is up and working.

Api documentation:

- https://api.example-project.work:8083/api/employee-portal/auth/doc.html
