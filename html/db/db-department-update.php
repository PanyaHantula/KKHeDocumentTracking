<?php
include __DIR__ . '/db-config.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $old_id = trim($_POST['old_id']);
    $id = trim($_POST['id']);
    $name = trim($_POST['name']);
    $workgroup_id = trim($_POST['workgroup_id']);

    if (!empty($id) && !empty($name) && !empty($workgroup_id)) {
        // ตรวจสอบรหัสซ้ำ (แต่ยกเว้นของเดิม)
        $checkSql = "SELECT id FROM department WHERE id = ? AND id != ?";
        $checkStmt = $conn->prepare($checkSql);
        $checkStmt->bind_param("ss", $id, $old_id);
        $checkStmt->execute();
        $checkStmt->store_result();

        if ($checkStmt->num_rows > 0) {
            $checkStmt->close();
            header("Location: /department-management.php?msg=รหัสหอผู้ป่วยนี้ถูกใช้แล้ว&type=warning");
            exit();
        }
        $checkStmt->close();

        // อัปเดตข้อมูล
        $sql = "UPDATE department SET id = ?, name = ?, workgroup_id = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $id, $name, $workgroup_id, $old_id);

        if ($stmt->execute()) {
            header("Location: /department-management.php");
            exit();
        } else {
            header("Location: /department-management.php?msg=เกิดข้อผิดพลาดในการบันทึกข้อมูล&type=danger");
            exit();
        }
    } else {
        header("Location: /department-management.php?msg=กรุณากรอกข้อมูลให้ครบถ้วน&type=warning");
        exit();
    }
}
$conn->close();
?>
