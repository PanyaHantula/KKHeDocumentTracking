<?php
include __DIR__ . '/db-config.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = intval($_POST['id']);
    $username = trim($_POST['username']);
    $fullname = trim($_POST['fullname']);
    $workgroup_id = $_POST['workgroupCode'];
    $department_id = $_POST['departmentCode'];
    $role = $_POST['role'];
    $new_password = trim($_POST['password']);

    if ($id > 0 && !empty($username) && !empty($fullname)) {

        // check a new user 
        $check = $conn->prepare("SELECT id FROM users WHERE user = ? AND id != ?");
        $check->bind_param("si", $username, $id);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $check->close();
            header("Location: /user-management.php?msg=ชื่อผู้ใช้นี้ถูกใช้แล้ว&type=warning");
            exit();
        }
        $check->close();

        // ถ้ามีกรอกรหัสใหม่ → เข้ารหัสแล้วอัปเดต
        if (!empty($new_password)) {
            $sql = "UPDATE users 
                    SET user = ?, name = ?, department_id = ?, role = ?, pwd = ?
                    WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssssi", $username, $fullname, $department_id, $role, $new_password, $id);
        } else {
            // ไม่เปลี่ยนรหัสผ่าน
            $sql = "UPDATE users 
                    SET user = ?, name = ?, department_id = ?, workgroup_id = ?, role = ?
                    WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssssi", $username, $fullname, $department_id, $workgroup_id, $role, $id);
        }

        if ($stmt->execute()) {
            header("Location: /user-management.php");
            exit();
        } else {
            header("Location: /user-management.php?msg=เกิดข้อผิดพลาดในการอัปเดตข้อมูล&type=danger");
            exit();
        }

        $stmt->close();
    } else {
        header("Location: /user-management.php?msg=ข้อมูลไม่ครบถ้วน&type=warning");
        exit();
    }
}

$conn->close();
?>
