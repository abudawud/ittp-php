#!/usr/local/bin/php -q
<?php
require_once "protocol.php";
require_once "database/db.php";
require_once "apis/log.php";
require_once "apis/auth.php";
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
        if (false === socket_recv($devsock, $buf, 10, MSG_WAITALL)) {
          logger("[ERROR] " . socket_strerror(socket_last_error($devsock)) );
          $db1->close();
          break;
        }else{
          $header = unpack(UNPACK_HEADER, $buf);
          logger(json_encode($header));
          if($header['code'] != CODE){
            $msg = "Invalid Code!\n";
            logger("Received Invalid Code");
            logger(json_encode($header));
            socket_write($devsock, $msg, strlen($msg));
            $db1->close();
            break;
          }else{
            logger("CODE MATCH");
          }

          socket_recv($devsock, $data, $header['length'], MSG_WAITALL);
          switch($header['method']){
            case METHOD_POST:{
              if($header['action'] == ACTION_POST_AUTH){
                if(!addAuthAPI($db1, $data, $devsock)){
                  $db1->close();
                  break 2;
                }
              }

              if($header['action'] == ACTION_POST_SENSOR_1){
                addLogSensorsAPI($db1, $data, $header['identity']);
              }

              break;
            }
          }
        }
    } while (true);
    logger("[INFO] Client Disconnected!");
    socket_close($devsock);
} while (true);

socket_close($sock);
?>
