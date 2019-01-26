<?php

class Log {
  public
    $logId, $sensorId, $timestamp, $value;
};

function addLog($db, $log){
  $sql = "INSERT INTO event_logs (sensor_id, timestamp, value) ";
  $sql .= "VALUES ($log->sensorId, '$log->timestamp', $log->value)";
  return $db->query($sql);
}
?>
