<?php
ob_start();

require_once "dbconfig.php";

$db = new PDO("mysql:host=".DBHOST.";port=8889;dbname=".DBNAME, DBUSER, DBPASS);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

//set timezone
date_default_timezone_set('Europe/London');

function __autoload($class) {
   
   $class = strtolower($class);

   $classpath = __DIR__ . 'classes/class.'.$class . '.php';
   if ( file_exists($classpath)) {
      require_once $classpath;
    }     
     
}