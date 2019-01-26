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
    $msg = "Welcome to IOTe, Please send your ID";
    socket_write($devsock, $msg, strlen($msg));
    logger("Client connected");
    $db1 = getDB();

    do {
        $buf = null;
        if (0 == socket_recv($devsock, $buf, 10, MSG_WAITALL)) {
            continue;
        }else{
          $header = unpack(UNPACK_HEADER, $buf);
          if($header['id'] != 1){
            $err = "Invalid ID, Communitaion will closed!";
            socket_write($devsock, $err, strlen($err));
            logger("Client Disconnected");
            $db1->close();
            break;
          }else{
            addLogAPI($db1, "123");
          }
        }
    } while (true);
    socket_close($devsock);
} while (true);

socket_close($sock);
?>
