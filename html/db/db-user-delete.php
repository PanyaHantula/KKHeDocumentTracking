<?php
include __DIR__ . '/db-config.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $sql = "DELETE FROM users WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $id);

    if ($stmt->execute()) {
        header("Location: /user-management.php");
        exit();
    } 
}
$conn->close();
?>
