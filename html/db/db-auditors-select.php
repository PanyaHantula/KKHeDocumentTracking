<?php
include __DIR__ . '/db-config.php';

// ดึงข้อมูลผู้ตรวจสอบ พร้อมชื่อหอผู้ป่วย
$sql = "SELECT 
            auditor.*,
            dept.name AS department_name,
            dept.id AS department_id,
            wg.name AS workgroup_name,
            r.name AS role_name
        FROM auditors AS auditor
        LEFT JOIN department AS dept ON auditor.department_id = dept.id
        LEFT JOIN workgroups wg ON auditor.workgroup_id = wg.id
        LEFT JOIN role AS r ON auditor.role = r.id
        ORDER BY auditor.id ASC";

$result = $conn->query($sql);
$auditors = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $auditors[] = $row;
    }
}

$conn->close();

// // แสดงผลข้อมูลทั้งหมด
// echo "<pre>";
// print_r($auditors);
// echo "</pre>";

    // [id] => 001
    // [name] => นพ.ธวัชชัย เทียมกลาง
    // [department_id] => D001
    // [workgroup_id] => G001
    // [role] => 3
    // [department_name] => หอผู้ป่วยกระดูกและข้อชาย 1
    // [workgroup_name] => กลุ่มงานศัลยกรรมออร์โธปิดิกส์
    // [role_name] => ผู้บริหาร

    return $auditors;
?>
