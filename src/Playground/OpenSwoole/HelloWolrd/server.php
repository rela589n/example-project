<?php
use OpenSwoole\Http\Server;
use OpenSwoole\Http\Request;
use OpenSwoole\Http\Response;

$server = new OpenSwoole\HTTP\Server('0.0.0.0', 9501);

$server->on("start", function (Server $server) {
    echo "OpenSwoole http server is started at $server->host:$server->port\n";
});

$server->on("request", function (Request $request, Response $response) {
    $response->header("Content-Type", "text/plain");
    $response->end("Hello World\n");

    echo "The request was processed\n";
});

$server->start();
