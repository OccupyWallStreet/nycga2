<?php

$mp3base = "http://voicetest.occupy.net/voice/voicefiles";
$wavurl = "http://sb.angel.com/messages/0a140220-04-133f167ef03-ff69a624-64a/0a14021f-07-133fb848d63-fd542b3d-97a/2011/12/12/0/0a140227-30-13430c2bc53-ca5daa71-8b7.wav";
$sanitize = escapeshellcmd($wavurl);
$wavurl1 = strrchr($wavurl, "/");
$wavurl2 = substr( $wavurl1, 1 );
$wavurl3 = substr($wavurl2, 0, -4); 
$mp3url = "$mp3base/$wavurl3.mp3";

echo $mp3url;

?>