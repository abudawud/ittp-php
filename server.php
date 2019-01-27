#!/usr/local/bin/php -q
<?php
require_once "protocol.php";
require_once "database/db.php";
require_once "apis/log.php";
require_once "utils/logger.php";

error_reporting(E_ALL);

/* Allow the script to hang around waiting for connections. */
set_time_limit(0);

/* Turn on implicit output flushing so we see what we're getting
 * as it comes in. */
ob_implicit_flush();

$address = '0.0.0.0';
$port = 9091;

if (($sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP)) === false) {
    echo "socket_create() failed: reason: " . socket_strerror(socket_last_error()) . "\n";
}

if (socket_bind($sock, $address, $port) === false) {
    echo "socket_bind() failed: reason: " . socket_strerror(socket_last_error($sock)) . "\n";
}

if (socket_listen($sock, 5) === false) {
    echo "socket_listen() failed: reason: " . socket_strerror(socket_last_error($sock)) . "\n";
}

logger("ITTP Started on port $port");

do {
    if (($devsock = socket_accept($sock)) === false) {
        echo "socket_accept() failed: reason: " . socket_strerror(socket_last_error($sock)) . "\n";
        break;
    }
    /* Accept connection. */
    $msg = "IOTe 0.1 $ \n";
    socket_write($devsock, $msg, strlen($msg));
    logger("Client connected");
    $db1 = getDB();

    do {
        $buf = null;
        if (0 == socket_recv($devsock, $buf, 6, MSG_WAITALL)) {
            continue;
        }else{
          $header = unpack(UNPACK_HEADER, $buf);
          logger(json_encode($header));
          if($header['code'] != CODE){
            $msg = "Invalid Code!\n";
            logger("Received Invalid Code");
            socket_write($devsock, $msg, strlen($msg));
            break;
          }

          switch($header['method']){
            case METHOD_POST:{
              if($header['action'] == ACTION_POST_SENSOR_1){
                socket_recv($devsock, $data, $header['length'], MSG_WAITALL);
                addLogSensor1API($db1, $data);
              }
            }
          }
        }
    } while (true);
    socket_close($devsock);
} while (true);

socket_close($sock);
?>
