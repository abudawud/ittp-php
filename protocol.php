<?php
define(CODE, 0x4521);

define(METHOD_POST, 0x01);
define(METHOD_INFO, 0x80);

define(ACTION_POST_SENSOR_1, 0x01);
define(ACTION_POST_AUTH, 0x02);

define(ACTION_INFO_AUTH_OK, 0x01);
define(ACTION_INFO_AUTH_FAILED, 0x02);

define(LEN_ZERO, 0x00);

//HEADER
define(PACK_HEADER, "SCCS");
define(UNPACK_HEADER, "Scode/Cmethod/Caction/Slength/Lidentity");
