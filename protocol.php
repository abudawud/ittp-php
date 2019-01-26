<?php
define(CODE, 0x4521);
define(IDENTITY_SRV, 0x01);
define(METHOD_INFO, 0x80);
define(ACTION_INFO_ACCEPT, 0x80);
define(LEN_ZERO, 0x00);

//HEADER
define(PACK_HEADER, "nNCCn");
define(UNPACK_HEADER, "ncode/Nid/Cmethod/Caction/nlength");
