<?php
$host = "localhost";
$db_name = "tracking";
$username = "root";
$password = "Esis@12345";
 
try {
    $conn = new PDO("mysql:host=$host;dbname=$db_name", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $exception){
    echo "Connection error: " . $exception->getMessage();
}
?>
