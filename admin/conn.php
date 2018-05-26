<?php 
session_start();
$servername = "host = localhost";
$port = "port = 5432";
$credentials = "user = postgres password = henk";
$dbname = "dbname = pr";

// $conn = mysqli_connect($servername, $username, $password, $dbname);
$conn = pg_connect( "$servername $port $dbname $credentials" );
if(!$conn) {
    /* echo "Error : Unable to process request right now. \n"; */
} else {}
?>

