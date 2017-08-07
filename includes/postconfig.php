<?php

require_once "dbconfig.php";

$dbwriter = new PDO("mysql:host=".DBHOST.";port=8889;dbname=".DBNAME, DBUSERPOST, DBPASSPOST);
$dbwriter->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

