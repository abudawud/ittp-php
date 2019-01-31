<?php
require_once "models/auth.php";
require_once "protocol.php";

function addAuthAPI($db, $packet, $sock){
  $data = unpack("Nidentity", $packet);
  $auth = getAuth($db, $data['identity']);
  if($auth === false){
    logger("[ERROR]\n\t" . getDBError($db));
  }

  if(!count($auth)){
    logger("[LOGIN FAILED] ID: " . $data['identity']);
    $msg = pack(PACK_HEADER, CODE, METHOD_INFO, ACTION_INFO_AUTH_FAILED, LEN_ZERO);
    socket_write($sock, $msg, strlen($msg));
    return false;
  }

  $msg = pack(PACK_HEADER, CODE, METHOD_INFO, ACTION_INFO_AUTH_OK, LEN_ZERO);
  socket_write($sock, $msg, strlen($msg));
  logger("[LOGIN OK] ID: " . $data['identity']);
  return true;
}

?>
