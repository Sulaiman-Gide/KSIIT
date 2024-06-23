<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "staff_promotion";

// Create MySQLi connection
$conn_mysqli = new mysqli($servername, $username, $password, $dbname);

// Check MySQLi connection
if ($conn_mysqli->connect_error) {
    die("MySQLi Connection failed: " . $conn_mysqli->connect_error);
}

// Create PDO connection
try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // Set PDO attributes if needed
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("PDO Connection failed: " . $e->getMessage());
}
?>