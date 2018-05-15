<?php 
//echo 'Hello';
session_start();

$servername = "host = localhost";
$port = "port = 5432";
$credentials = "user = postgres password = henk.11";
$dbname = "dbname = pr";

$conn = pg_connect( "$servername $port $dbname $credentials" );
?>
