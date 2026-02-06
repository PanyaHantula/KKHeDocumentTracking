<?php
include __DIR__ . '/db-config.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $departmentCode = trim($_POST['departmentCode']);
    $departmentName = trim($_POST['departmentName']);
    $workgroupID = trim($_POST['workgroup_id']);

    if (!empty($departmentCode) && !empty($departmentName)) {

        // ตรวจสอบรหัสแผนกซ้ำในฐานข้อมูล
        $checkSql = "SELECT id FROM department WHERE id = ?";
        $checkStmt = $conn->prepare($checkSql);
        $checkStmt->bind_param("s", $departmentCode);
        $checkStmt->execute();
        $checkStmt->store_result(); 

        if ($checkStmt->num_rows > 0) {
            // ถ้ามีรหัสซ้ำ → กลับไปหน้าเดิมพร้อมข้อความเตือน
            $checkStmt->close();
            header("Location: /department-management.php?msg=รหัสแผนกนี้มีอยู่แล้ว&type=warning");
            exit();
        }
        $checkStmt->close();

        // ถ้าไม่ซ้ำ → ทำการบันทึกข้อมูลใหม่
        $sql = "INSERT INTO department (id, name, workgroup_id) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $departmentCode, $departmentName, $workgroupID);

        if ($stmt->execute()) {
            header("Location: /department-management.php");
            exit();
        } else {
            header("Location: /department-management.php?msg=เกิดข้อผิดพลาดในการบันทึกข้อมูล&type=danger");
            exit();
        }

        $stmt->close();
    } else {
        // ⚠️ ถ้าข้อมูลไม่ครบ
        header("Location: /department-management.php?msg=กรุณากรอกข้อมูลให้ครบถ้วน&type=warning");
        exit();
    }
}

$conn->close();
?>
