<?php
include __DIR__ . '/db-config.php';

header('Content-Type: application/json; charset=utf-8');

// ตรวจสอบว่ามีการส่งค่า workgroup_id มาหรือไม่
if (isset($_POST['workgroup_id']) && $_POST['workgroup_id'] !== '') {
    $workgroup_id = trim($_POST['workgroup_id']); 

    $sql = "SELECT id, name 
            FROM department 
            WHERE workgroup_id = ?
            ORDER BY name ASC";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $workgroup_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $departments = [];
    while ($row = $result->fetch_assoc()) {
        $departments[] = $row;
    }

    // ถ้าไม่มีผลลัพธ์
    if (empty($departments)) {
        echo json_encode(['message' => 'No departments found']);
    } else {
        echo json_encode($departments, JSON_UNESCAPED_UNICODE);
    }

    $stmt->close();
    $conn->close();

} else {
    echo json_encode(['error' => 'Missing or invalid workgroup_id']);
}
?>
