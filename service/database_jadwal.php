<?php

$hostname = "localhost";
$username = "root";
$password = "";
$database_name = "gorkharisma";

try {
    $db = new PDO("mysql:host=$hostname;dbname=$database_name", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    die();
}
?>