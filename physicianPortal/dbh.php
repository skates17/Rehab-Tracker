<?php
//$DBhost = "webdb.uvm.edu";
//$DBuser = "azacchea_admin";
//$DBpass = "cPA2252PsmV4";
//$DBname = "AZACCHEA_CS275";
$DBhost = "webdb.uvm.edu";
$DBuser = "bgoodwin_admin";
$DBpass = "9ORjQCNJwzLx";
$DBname = "BGOODWIN_CS275";
$DBcon = new MySQLi($DBhost,$DBuser,$DBpass,$DBname);
if ($DBcon->connect_errno) {
         die("ERROR : -> ".$DBcon->connect_error);
     }
