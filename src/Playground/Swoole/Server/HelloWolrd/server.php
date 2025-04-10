<?php
declare(strict_types=1);

use Swoole\Http\Request;
use Swoole\Http\Response;
use Swoole\Http\Server;

$server = new Swoole\HTTP\Server('0.0.0.0', 9501);

$server->on("start", function (Server $server): void {
    echo "OpenSwoole http server is started at $server->host:$server->port\n";
});

$server->on("request", function (Request $request, Response $response): void {
    $response->header("Content-Type", "text/plain");
    $response->end("Hello World\n");

    echo "The request was processed\n";
});

$server->start();
