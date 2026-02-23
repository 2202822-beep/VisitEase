<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "museum_db"; // Siguraduhin na "museum_db" ang pangalan ng database mo sa phpMyAdmin

// Gumawa ng connection gamit ang MySQLi
$conn = new mysqli($servername, $username, $password, $dbname);

// I-check kung may error sa connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>