<?php
define(CODE, 0x4521);

define(METHOD_POST, 0x01);
define(METHOD_INFO, 0x80);

define(ACTION_POST_SENSOR_1, 0x01);
define(ACTION_INFO_ACCEPT, 0x01);

define(LEN_ZERO, 0x00);

//HEADER
define(PACK_HEADER, "nCCn");
define(UNPACK_HEADER, "ncode/Cmethod/Caction/nlength");
