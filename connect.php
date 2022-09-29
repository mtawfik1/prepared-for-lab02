<?php 
$conn = new mysqli("localhost", "user", "pass", "db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>