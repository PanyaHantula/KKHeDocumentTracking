<?php
include __DIR__ . '/db-config.php';

$docId = isset($_GET['doc_id']) ? $_GET['doc_id'] : null;
$record = null;

// --- ดึงข้อมูลเอกสาร
if ($docId) {
    $sql = "SELECT * FROM document_info WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $docId);
    $stmt->execute();

    $result = $stmt->get_result();
    $record = $result->fetch_assoc();
    $stmt->close();

}

// --- ดึง workgroup
$workgroups = [];
$resWorkGroup = $conn->query("SELECT id, name FROM workgroups ORDER BY id ASC");
while ($row = $resWorkGroup->fetch_assoc()) {
    $workgroups[] = $row;
}

// --- ดึง department
$departments = [];
$resDept = $conn->query("SELECT id, name FROM department ORDER BY id ASC");
while ($row = $resDept->fetch_assoc()) {
    $departments[] = $row;
}

// --- ดึง auditors
$auditors = [];
$resAud = $conn->query("SELECT id, name FROM auditors ORDER BY id ASC");
while ($row = $resAud->fetch_assoc()) {
    $auditors[] = $row;
}

// --- เมื่อ submit
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $doc_Create_at = $_POST['dateCreate'];
    $customer_name = trim($_POST['customer_name']);
    $workgroup_id = trim($_POST['workgroup_id']);
    $department_id = trim($_POST['department_id']);
    $staff_id = trim($_POST['staff_id']);
    $remark = trim($_POST['remark']);

    $sql = "UPDATE document_track SET staff_id = ? WHERE doc_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss",$staff_id, $docId);

    if ($stmt->execute()) {

    } else {
        echo "Error: " . $stmt->error;  
    }
    $stmt->close();

    $sql = "UPDATE document_info SET
            create_at = ?,  
            customer_name = ?,
            workgroup_id = ?,
            department_id = ?,
            staff_id = ?,
            remark = ?
            WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssss",$doc_Create_at, $customer_name, $workgroup_id, $department_id, $staff_id, $remark, $docId);

    if ($stmt->execute()) {
        header("Location: document-detail.php?doc_id=" . urlencode($docId));
        exit();
    } else {
        echo "Error: " . $stmt->error;  
    }
    $stmt->close();
}

$conn->close();


    // echo "<pre>";
    // print_r($record);
    // echo "</pre>";
    // echo "<pre>";
    // print_r($departments);
    // echo "</pre>";
    // echo "<pre>";
    // print_r($auditors);
    // echo "</pre>";

    // record
    // [id] => 002
    // [create_at] => 2026-01-19
    // [customer_name] => B
    // [workgroup_id] => G001
    // [department_id] => D004
    // [staff_id] => 005
    // [status] => 2
    // [complete] => 
    // [complete_at] => 
    // [remark] => 

    // departments
    // [id] => D004
    // [name] => หอผู้ป่วยกระดูกสันหลัง

    // auditors
    // [id] => 003
    // [name] => นพ.ธนิต ฟูเจริญ
?>