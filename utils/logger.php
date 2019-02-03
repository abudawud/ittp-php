<?php
class Logger {
  function info($log){
    $this->log("[INFO]", $log);
  }

  function warning($log){
    $this->log("[WARNING]", $log);
  }

  function error($log){
    $this->log("[ERROR]", $log);
  }

  private function log($header, $log){
    echo date("Y-m-d H:i:s") . "$header >> $log \n";
  }
}
?>
