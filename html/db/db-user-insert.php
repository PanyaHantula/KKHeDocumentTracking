<?php
include __DIR__ . '/db-config.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $fullname = trim($_POST['fullname']);
    $workgroup_id = $_POST['workgroupCode'];
    $department_id = $_POST['departmentCode'];
    $role = $_POST['role'];

    if (!empty($username) && !empty($password)) {       

        // ตรวจสอบชื่อผู้ใช้ซ้ำ
        $checkSql = "SELECT id FROM users WHERE user = ?";
        $checkStmt = $conn->prepare($checkSql);
        $checkStmt->bind_param("s", $username);
        $checkStmt->execute();
        $checkStmt->store_result();

        if ($checkStmt->num_rows > 0) {
            // พบ username ซ้ำ
            $checkStmt->close();
            header("Location: /user-management.php?msg=ชื่อผู้ใช้นี้ถูกใช้แล้ว&type=warning");
            exit();
        }
        $checkStmt->close();

        //  ถ้าไม่ซ้ำ → เข้ารหัสรหัสผ่านแล้วบันทึก
        $sql = "INSERT INTO users (user, pwd, name, department_id, workgroup_id, role)
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssss", $username, $password, $fullname, $department_id, $workgroup_id, $role);

        if ($stmt->execute()) {
            header("Location: /user-management.php");
            exit();
        } else {
            header("Location: /user-management.php?msg=เกิดข้อผิดพลาดในการบันทึกข้อมูล&type=danger");
            exit();
        }

        $stmt->close();
    } else {
        header("Location: /user-management.php?msg=กรุณากรอกข้อมูลให้ครบถ้วน&type=warning");
        exit();
    }
}

$conn->close();
?>
