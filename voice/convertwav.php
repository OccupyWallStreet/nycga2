<?php
//mattlepacek: techops: nyc:
//downloads wav file from url provided and converts to mp3
//install instructions for box
//1) apt-get update
//2) apt-get upgrade
//3) apt-remove ffmpeg
//4) apt-get install lame
//5) apt-get install ffmpeg
//6) apt-get install ffmpeg libavcodec-extra-52 (or -53 if error)
//7) mkdir voicefiles in /var/www/nycga.net/web/voice
//8) chmod 777 voicefiles


$wavurl0 = @$_GET['wavurl'] ;
$wavurl0s = escapeshellcmd($wavurl0);
$wavurl1 = strrchr($wavurl0, "/");
$wavurl2 = substr( $wavurl1, 1 );
$wavurl2s = escapeshellcmd($wavurl2);
$wavurl3 = substr($wavurl2, 0, -4);
$wavurl3s = escapeshellcmd($wavurl3); 

$wget = "wget -P /var/www/nycga411/audiofiles $wavurl0s";
$ffmpeg = "ffmpeg -i /var/www/nycga411/audiofiles/$wavurl2s -ab 128k /var/www/nycga411/audiofiles/$wavurl3s.mp3";
echo exec($wget);
echo exec ($ffmpeg);

?>
