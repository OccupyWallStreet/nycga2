CREATE TABLE `nycga`.`voice_log` (
`id` int( 11 ) NOT NULL AUTO_INCREMENT ,
`currentseq` tinyint( 4 ) NOT NULL ,
`datetime` datetime NOT NULL ,
`currentvm` longtext NOT NULL ,
`callername` tinytext NOT NULL ,
`userid` text NOT NULL ,
`callerid` text NOT NULL ,
)