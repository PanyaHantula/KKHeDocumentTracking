<?php
include __DIR__ . '/db-config.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $old_id = trim($_POST['old_id']); // รหัสเดิม
    $id = trim($_POST['id']); // รหัสใหม่ (ถ้าเปลี่ยน)
    $name = trim($_POST['name']);

    if (!empty($id) && !empty($name)) {
        // 🔹 ตรวจสอบว่ามี ID ซ้ำกับกลุ่มอื่นหรือไม่
        $checkSql = "SELECT id FROM workgroups WHERE id = ? AND id != ?";
        $checkStmt = $conn->prepare($checkSql);
        $checkStmt->bind_param("ss", $id, $old_id);
        $checkStmt->execute();
        $checkStmt->store_result();

        if ($checkStmt->num_rows > 0) {
            // ❌ มี ID ซ้ำ
            $checkStmt->close();
            header("Location: /workgroup-management.php?msg=รหัสกลุ่มงานนี้ถูกใช้แล้ว&type=warning");
            exit();
        }
        $checkStmt->close();

        // 🔹 ทำการอัปเดตข้อมูล
        $sql = "UPDATE workgroups SET id = ?, name = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $id, $name, $old_id);

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
