<?php
include __DIR__ . '/db-config.php';
header('Content-Type: application/json; charset=utf-8');

$dept_id = $_POST['dept_id'] ?? null;

$sql = "SELECT a.id AS auditor_id, 
               a.name AS auditor_name, 
               d.name AS department_name
        FROM auditors a
        JOIN department d ON a.department_id = d.id
        WHERE a.department_id = ?
        ORDER BY a.id ASC";

$stmt = $conn->prepare($sql);

$stmt->bind_param("s", $dept_id);
$stmt->execute();
$result = $stmt->get_result();

$auditors = [];
while ($row = $result->fetch_assoc()) {
    $auditors[] = $row;
}

$stmt->close();
$conn->close();

header('Content-Type: application/json; charset=utf-8');
echo json_encode($auditors);
exit;
?>
