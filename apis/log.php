<?php
require_once "models/log.php";
require_once "models/auth.php";
require_once "utils/logger.php";
require_once "utils/datetime.php";
require_once "utils/inttype.php";

function addLogSensorsAPI($db, $packet, $identity){
  $data = unpack("C6id/S6val", $packet);

  $auth = getAuth($db, $identity);
  if($auth === false){
    logger("[ERROR]\n\t" . getDBError($db));
  }

  if(!count($auth)){
    logger("[LOGIN FAILED] ID: " . $identity);
    return false;
  }

  $logs = array();
  $time = getDateTime();

  $i = 1;
  while($i++ < 6){
    if($data["val$i"] === 0)
      continue;

    $log = new Log();
    $log->sensorId = $data["id$i"];
    $log->timestamp = $time;

    if($data["id$i"] == 1 || $data["id$i"] == 2){
      $float = $data["val$i"] / 100.0;
      $log->value = $float;
    }else{
      $log->value = $data["val$i"];
    }
    array_push($logs, $log);
  }

  logger(json_encode($data));
  $status = addLogs($db, $logs);
  if(!$status){
    logger("[ERROR]\n\t" . getDBError($db));
  }else{
    logger("AddLogAPI success");
  }

  return true;
}
?>
