<?php
require_once "models/log.php";
require_once "models/auth.php";
require_once "utils/logger.php";
require_once "utils/datetime.php";
require_once "utils/inttype.php";

$logc = new Logger();
function addLogSensorsAPI($db, $packet, $identity){
  global $logc;

  $auth = getAuth($db, $identity);
  if($auth === false){
    $logc->error(getDBError($db));
    $logc->error(getLogSQL());
    return false;
  }

  if(!count($auth)){
    $logc->warning("Login failed, ID " . $identity);
    return false;
  }

  if(strlen($packet) < 18){
    $logc->warning("Payload less than ACTION_POST_SENSOR_1 needed!");
    return false;
  }
  $data = unpack("C6id/S6val", $packet);
  $logs = array();
  $time = getDateTime();

  $i = 0;
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

  if(count($logs)){
    $status = addLogs($db, $logs);
    if(!$status){
      $logc->error(getDBError($db));
      $logc->error(getLogSQL());
      return false;
    }
  }

  return true;
}
?>
