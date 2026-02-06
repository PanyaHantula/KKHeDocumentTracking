<?php
include __DIR__ . '/db-config.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = trim($_POST['workgroupCode']);
    $workgroupName = trim($_POST['workgroupName']);

    if (!empty($id) && !empty($workgroupName)) {

        // 🔹 1. ตรวจสอบว่ามี ID ซ้ำหรือไม่
        $checkSql = "SELECT id FROM workgroups WHERE id = ?";
        $checkStmt = $conn->prepare($checkSql);
        $checkStmt->bind_param("s", $id);
        $checkStmt->execute();
        $checkStmt->store_result(); // ต้องมีบรรทัดนี้!

        if ($checkStmt->num_rows > 0) {
            // ❌ พบ ID ซ้ำ → ยกเลิกการ Insert
            $checkStmt->close();
            header("Location: /workgroup-management.php?msg=รหัสกลุ่มงานนี้มีอยู่แล้ว&type=warning");
            exit();
        }

        $checkStmt->close();

        // 🔹 2. ถ้าไม่ซ้ำ → INSERT
        $sql = "INSERT INTO workgroups (id, name) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $id, $workgroupName);

        if ($stmt->execute()) {
            header("Location: /workgroup-management.php");
            exit();
        } else {
            header("Location: /workgroup-management.php?msg=เกิดข้อผิดพลาดในการบันทึกข้อมูล&type=danger");
            exit();
        }

        $stmt->close();
    } else {
        header("Location: /workgroup-management.php?msg=กรุณากรอกข้อมูลให้ครบถ้วน&type=warning");
        exit();
    }
}

$conn->close();
?>
