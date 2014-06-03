<?php
$link = mysql_connect('localhost', 'root', 'mooie');
$db = mysql_select_db('surflogs', $link);

ini_set('max_execution_time', 300); //300 seconds = 5 minutes
ini_set('memory_limit', '-1');
ini_set('mysql.connect_timeout', 300);
ini_set('default_socket_timeout', 300);
?>