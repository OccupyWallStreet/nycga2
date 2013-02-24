<?php
self::$aryGlobalSettings['track_search'] = false;
self::$aryGlobalSettings['connection_timeout'] = 5;
if (isset(self::$aryGlobalSettings['track_compress']) && self::$aryGlobalSettings['track_compress'])
	self::$aryGlobalSettings['track_mode'] = 1;
else 
	self::$aryGlobalSettings['track_mode'] = 0;

if (isset(self::$aryGlobalSettings['track_compress']))
	unset(self::$aryGlobalSettings['track_compress']);