<?php

require __DIR__ . "/vendor/autoload.php";

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$username = $_ENV["DATABASE_USERNAME"];
$password = $_ENV["DATABASE_PASSWORD"];
$hostname = $_ENV["DATABASE_HOSTNAME"];
$databasename = $_ENV["DATABASE_NAME"]; 

$dsn = 'mysql:dbname=' . $databasename . ';host=' . $hostname;
$db = new PDO($dsn, $username, $password);
$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
?>