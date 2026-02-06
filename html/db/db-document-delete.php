<?php
include __DIR__ . '/db-config.php';

$docId = isset($_GET['doc_id']) ? $_GET['doc_id'] : null;

if ($docId) {

    $sql = "DELETE FROM document_track WHERE doc_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $docId);
    $stmt->execute();
    $stmt->close();

    $sql = "DELETE FROM document_info WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $docId);

    if ($stmt->execute()) {
        $stmt->close();
        $conn->close();
        header("Location: /document-list.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}

$conn->close();
?>
