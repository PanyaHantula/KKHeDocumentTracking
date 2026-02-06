<?php
include_once __DIR__ . '/db-config.php'; // ใช้ include_once ป้องกันเรียกซ้ำ

$record = null; // 1. กำหนดค่าเริ่มต้นเป็น null (ว่าง)

    $docId = "001";

    $sql = "SELECT
                doc.*,
                dept.name AS department_name,
                wg.name AS workgroup_name,
                auditor.name AS staff_name,
                ds.name AS status_name,
                dt.*,
                res.name AS resident_name, 
                aud.name AS auditor_name 
            FROM document_info doc
            LEFT JOIN department AS dept ON doc.department_id = dept.id
            LEFT JOIN workgroups wg ON doc.workgroup_id = wg.id
            LEFT JOIN auditors auditor ON doc.staff_id = auditor.id
            LEFT JOIN document_status ds ON doc.status = ds.id
            LEFT JOIN document_track dt ON dt.doc_id = doc.id
            LEFT JOIN auditors res ON dt.resident_id = res.id
            LEFT JOIN auditors aud ON dt.auditor_id = aud.id
            WHERE doc.id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $docId);
    $stmt->execute();

    $result = $stmt->get_result();
    
    // ดึงข้อมูล
    if ($result->num_rows > 0) {
        $record = $result->fetch_assoc();
        
        // จัดการข้อมูล null ให้เป็น "-"
        $record = array_map(function($v) {
            return is_null($v) ? "-" : $v;
        }, $record);
    } 
    
    $stmt->close();

    echo "<pre>";
print_r($record);
echo "</pre>";

?>