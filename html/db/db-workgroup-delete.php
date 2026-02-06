<?php
include __DIR__ . '/db-config.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $sql = "DELETE FROM workgroups WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $id);

    if ($stmt->execute()) {
        header("Location: /workgroup-management.php");
        exit();
    } 
}
$conn->close();
?>
