<?php
$DBhost = "webdb.uvm.edu";
$DBuser = "bgoodwin_admin";
$DBpass = "9ORjQCNJwzLx";
$DBname = "BGOODWIN_CS275";
$DBcon = new MySQLi($DBhost,$DBuser,$DBpass,$DBname);
if ($DBcon->connect_errno) {
         die("ERROR : -> ".$DBcon->connect_error);
     }
