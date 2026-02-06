<?php
// Database connection
$db_host = "mysql";
$db_user = "root";
$db_pass = "rootpassword";
$db_name = "eDocumentTracker"; 

$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);
mysqli_set_charset($conn, "utf8mb4");

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>