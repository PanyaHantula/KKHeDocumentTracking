<?php
include __DIR__ . '/db-config.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = trim($_POST['id']);
    $name = trim($_POST['name']);
    $workgroup_id = $_POST['workgroupCode'];
    $department_id = $_POST['departmentCode'];
    $role = $_POST['role'];

    if (!empty($id) && !empty($name)) {

        // ตรวจสอบรหัส auditor ซ้ำในฐานข้อมูล
        $checkSql = "SELECT id FROM auditors WHERE id = ?";
        $checkStmt = $conn->prepare($checkSql);
        $checkStmt->bind_param("s", $id);
        $checkStmt->execute();
        $checkStmt->store_result(); // สำคัญมาก

        if ($checkStmt->num_rows > 0) {
            // ถ้ามีรหัสซ้ำ → redirect กลับพร้อมข้อความเตือน
            $checkStmt->close();
            header("Location: /auditor-management.php?msg=รหัสผู้ตรวจนี้มีอยู่แล้ว&type=warning");
            exit();
        }
        $checkStmt->close();

        // 🔹 2. ถ้าไม่ซ้ำ → ทำการบันทึกข้อมูลใหม่
        $sql = "INSERT INTO auditors (id, name, department_id,workgroup_id,role) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssi", $id, $name, $department_id, $workgroup_id, $role);

        if ($stmt->execute()) {
            header("Location: /auditor-management.php");
            exit();
        } else {
            header("Location: /auditor-management.php?msg=เกิดข้อผิดพลาดในการบันทึกข้อมูล&type=danger");
            exit();
        }

        $stmt->close();
    } else {
        // ⚠️ ถ้าข้อมูลไม่ครบ
        header("Location: /auditor-management.php?msg=กรุณากรอกข้อมูลให้ครบถ้วน&type=warning");
        exit();
    }
}

$conn->close();
?>
