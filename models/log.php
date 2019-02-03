<?php

class Log {
  public
    $logId, $sensorId, $timestamp, $value;
};

$sql = "";

function getLogSQL(){
  global $sql;
  return $sql;
}
function addLog($db, $log){
  global $sql;

  $sql = "INSERT INTO event_logs (sensor_id, timestamp, value) ";
  $sql .= "VALUES ($log->sensorId, '$log->timestamp', $log->value)";
  return $db->query($sql);
}

function addLogs($db, $datas){
  global $sql;
  
  $vals_arr = array();
  foreach($datas as $data){
    $val = "($data->sensorId, '$data->timestamp', $data->value)";
    array_push($vals_arr, $val);
  }

  $val_sql = implode(',', $vals_arr);

  $sql = "INSERT INTO event_logs (sensor_id, timestamp, value) ";
  $sql .= "VALUES $val_sql";

  return $db->query($sql);
}
?>
