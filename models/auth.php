<?php
function getAuth($db, $identity){
  $sql = "SELECT * FROM ms_devices WHERE identity = $identity";
  $data = $db->query($sql);

  if($data === false){
    return false;
  }

  return $data->fetch_assoc();
}
?>
