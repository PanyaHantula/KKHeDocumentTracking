<?php
include __DIR__ . '/db-config.php';

// ดึงข้อมูล users พร้อมชื่อแผนกและบทบาท
$sql = "SELECT 
            user.*,
            dept.name AS department_name,
            dept.id AS department_id,
            wg.name AS workgroup_name,
            r.name AS role_name
        FROM users AS user
        LEFT JOIN department AS dept ON user.department_id = dept.id
        LEFT JOIN workgroups wg ON user.workgroup_id = wg.id
        LEFT JOIN role AS r ON user.role = r.id
        ORDER BY user.id ASC";
//WHERE r.name NOT IN ('admin', 'ผู้ดูแลระบบ')
$result = $conn->query($sql);

$users = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
}

$conn->close();

// // แสดงผลข้อมูลทั้งหมด
// echo "<pre>";
// print_r($users);
// echo "</pre>";

return $users;
?>
