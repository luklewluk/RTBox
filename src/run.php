<?php

namespace luklew\RTBox;

use noFlash\Shout\Shout;
use noFlash\TinyWs\Server;

require_once('../vendor/autoload.php');
require_once('ShoutBoxUser.php');
require_once('WebServer.php');


$logger = new Shout();
$logger->setMaximumLogLevel(6); // Ignoring DEBUG logs
$server = new Server($logger);
$server->run(new WebServer());

