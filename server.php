#!/usr/local/bin/php -q
<?php
require __DIR__ . '/vendor/autoload.php';
require_once "protocol.php";
require_once "database/db.php";
require_once "apis/log.php";
require_once "apis/auth.php";
require_once "utils/logger.php";

date_default_timezone_set("Asia/Jakarta");
$log = new Logger();
$loop = React\EventLoop\Factory::create();
$socket = new React\Socket\TcpServer('0.0.0.0:9091', $loop);
$db = getDB();

$socket->on('connection', function (React\Socket\ConnectionInterface $connection) use ($loop, $log, $db) {
    if(!$db->ping()){
      $log->warning("Database disconnected!");
      $db = getDB();
    }

    $connection->write("IOTe $\n");
    $log->info("Client connected: " . $connection->getRemoteAddress());

    $func = function () use ($connection, $log) {
      $log->warning("Connection timeout for " . $connection->getRemoteAddress());
      $connection->end("Connection Timeout");
    };

    $timer = $loop->addTimer(60, $func);

    $connection->on('data', function ($data) use ($connection, $db, $log, $func, $loop, &$timer) {
      $loop->cancelTimer($timer);

      if(strlen($data) < HEADER_LEN){
        $log->warning("Receive data less then " . HEADER_LEN);
        $connection->end("Invalid IOTe protocol!");
        return;
      }
      $header = substr($data, 0, HEADER_LEN);
      $payload = substr($data, HEADER_LEN);

      $hData = unpack(UNPACK_HEADER, $header);
      if($hData['code'] != CODE){
        $log->warning("Invalid encryption from " . $connection->getRemoteAddress());
        $connection->end("Invalid IOTe code!");
      }else if(strlen($payload) != $hData['length']){
        $log->warning("Receive payload len unequal in header");
        $connection->end("Invalid IOTe payload!");
      }else{
        switch ($hData['method']) {
          case METHOD_POST:
            switch ($hData['action']) {
              case ACTION_POST_SENSOR_1:
                if(addLogSensorsAPI($db, $payload, $hData['identity']) === false){
                  $connection->close();
                  return;
                }
                break;

              default:
                $log->warning("Invalid action " . $hData['action']);
                $connection->close();
                return;
                break;
            }
            break;

          default:
            $log->warning("Invalid method " . $hData['method']);
            $connection->close();
            break;
        }

        $timer = $loop->addTimer(240, $func);
      }
    });

    $connection->on('close', function() use ($connection, $log){
      $log->info("Client Disconnected " . $connection->getRemoteAddress());
    });
});

$log->info("ITTP Started on port 19901");
$loop->run();
?>
