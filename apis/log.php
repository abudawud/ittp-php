<?php
require_once "models/log.php";
require_once "models/auth.php";
require_once "utils/logger.php";
require_once "utils/datetime.php";
require_once "utils/inttype.php";

function addLogSensor1API($db, $packet){
  $data = unpack("Nidentity/Cid/nval", $packet);

  $auth = getAuth($db, $data['identity']);
  if($auth === false){
    logger("[ERROR]\n\t" . getDBError($db));
  }

  if(!count($auth)){
    logger("[LOGIN FAILED] ID: " . $data['identity']);
    return false;
  }

  $log = new Log();
  $log->sensorId = $data['id'];
  $log->timestamp = getDateTime();
  $float = intU2S($data['val']) / 100.0;
  $log->value = $float;

  logger(json_encode($data));
  $status = addLog($db, $log);
  if(!$status){
    logger("[ERROR]\n\t" . getDBError($db));
  }else{
    logger("AddLogAPI success");
  }

  return true;
}
?>
