<?php
include __DIR__ . '/db-config.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $sql = "DELETE FROM auditors WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $id);

    if ($stmt->execute()) {
        header("Location: /auditor-management.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
}
$conn->close();
?>
