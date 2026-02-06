<?php
include __DIR__ . '/db-config.php';

if (isset($_GET['id'])) {
    $id = $_GET['id']; // เช่น "K001"

    $sql = "DELETE FROM department WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $id);  

    if ($stmt->execute()) {
        header("Location: /department-management.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}
$conn->close();
?>
