<?php
define("SIGNED_INT_16_BIT", 32767);
define("SIGNED_INT_16_BIT_C", 65536);

function intU2S($num){
  if($num > SIGNED_INT_16_BIT){
    return $num - SIGNED_INT_16_BIT_C;
  }

  return $num;
}
