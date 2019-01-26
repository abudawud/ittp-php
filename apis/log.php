<?php
require_once "models/log.php";
require_once "utils/logger.php";

function addLogAPI($db, $data){
  $log = new Log();
  $log->sensorId = 1;
  $log->timestamp = "12-13-2012 22:22";
  $log->value = 20.2;

  $status = addLog($db, $log);
  if(!$status){
    logger("[ERROR]\n\t" . getDBError($db));
  }else{
    logger("AddLogAPI success");
  }
}
?>
