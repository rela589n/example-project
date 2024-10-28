<?php
use OpenSwoole\Http\Server;
use OpenSwoole\Http\Request;use OpenSwoole\Http\Response;

$server = new OpenSwoole\HTTP\Server("127.0.0.1", 9501);

var_dump($server);
