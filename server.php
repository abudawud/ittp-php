#!/usr/local/bin/php -q
<?php
require __DIR__ . '/vendor/autoload.php';
require_once "protocol.php";
require_once "database/db.php";
require_once "apis/log.php";
require_once "apis/auth.php";
require_once "utils/logger.php";

$loop = React\EventLoop\Factory::create();
$socket = new React\Socket\TcpServer('0.0.0.0:19091', $loop);

$socket->on('connection', function (React\Socket\ConnectionInterface $connection) {
    $connection->write("Hello " . $connection->getRemoteAddress() . "!\n");
    $connection->write("Welcome to this amazing server!\n");
    $connection->write("Here's a tip: don't say anything.\n");

    $connection->on('data', function ($data) use ($connection) {
      $bin = unpack("Scode/Cmethod/Caction/Slength/Lidentity", $data);
      echo $connection->getRemoteAddress() . " - " . json_encode($bin) . "\n";
    });
});

$loop->run();
?>
