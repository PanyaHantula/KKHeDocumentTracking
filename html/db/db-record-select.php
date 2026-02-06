<?php
include __DIR__ . '/db-config.php';

    $sql = "SELECT 
                doc.*,
                dept.name AS department_name,
                wg.name AS workgroup_name,
                auditor.name AS staff_name,
                ds.name AS status_name

            FROM document_info doc
            LEFT JOIN document_track dt ON dt.doc_id = doc.id
            LEFT JOIN document_status ds ON dt.doc_status = ds.id
            LEFT JOIN department AS dept ON doc.department_id = dept.id
            LEFT JOIN workgroups wg ON doc.workgroup_id = wg.id
            LEFT JOIN auditors auditor ON doc.staff_id = auditor.id
            ORDER BY doc.id DESC";

$result = $conn->query($sql);

$records = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $records[] = $row;
    }
} else {
    echo "ไม่พบข้อมูล";
}

$conn->close();

// // แสดงผลข้อมูลทั้งหมด
// echo "<pre>";
// print_r($records);
// echo "</pre>";

return $records;
?>
