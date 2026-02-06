<?php
include __DIR__ . '/db-config.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $old_id = trim($_POST['old_id']);
    $id = trim($_POST['id']);
    $name = trim($_POST['name']);
    $workgroup_id = $_POST['workgroupCode'];
    $department_id = $_POST['departmentCode'];
    $role = $_POST['role'];

    if (!empty($old_id) && !empty($id) && !empty($name) && !empty($department_id)) {
        $sql = "UPDATE auditors SET id=?, name=?, department_id=?, workgroup_id=?, role=?  WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssi", $id, $name, $department_id, $workgroup_id, $role, $old_id);

        if ($stmt->execute()) {
            header("Location: /auditor-management.php");
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }
    } else {
        echo "ข้อมูลไม่ครบถ้วน";
    }
}
$conn->close();
?>
