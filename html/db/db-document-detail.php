<?php
include __DIR__ . '/db-config.php';

// รับค่า document_id จาก URL
$docId = isset($_GET['doc_id']) ? $_GET['doc_id'] : null;

$record = null;
if ($docId) {
    $sql = "SELECT
                doc.*,
                dept.name AS department_name,
                wg.name AS workgroup_name,
                auditor.name AS staff_name,
                ds.name AS status_name,
                dt.*, 
                res.name AS resident_name, 
                aud.name AS auditor_name,
                res_status.name AS resident_status,
                res_complete.name AS resident_complete,
                staff_status.name AS staff_status,
                staff_complete.name AS staff_complete,
                staff_complete.name AS staff_complete,
                aud_status.name AS auditor_status,
                aud_complete.name AS auditor_complete,
                med_status.name AS medical_records_status,
                med_complete.name AS medical_records_complete

            FROM document_info doc
            LEFT JOIN department AS dept ON doc.department_id = dept.id
            LEFT JOIN workgroups wg ON doc.workgroup_id = wg.id
            LEFT JOIN auditors auditor ON doc.staff_id = auditor.id
            LEFT JOIN document_track dt ON dt.doc_id = doc.id
            LEFT JOIN document_status ds ON dt.doc_status = ds.id
            LEFT JOIN auditors res ON dt.resident_id = res.id
            LEFT JOIN auditors aud ON dt.auditor_id = aud.id

            LEFT JOIN document_status res_status ON dt.resident_status = res_status.id
            LEFT JOIN document_status res_complete ON dt.resident_complete = res_complete.id
            LEFT JOIN document_status staff_status ON dt.staff_status = staff_status.id
            LEFT JOIN document_status staff_complete ON dt.staff_complete = staff_complete.id
            LEFT JOIN document_status aud_status ON dt.auditor_status = aud_status.id
            LEFT JOIN document_status aud_complete ON dt.auditor_complete = aud_complete.id
            LEFT JOIN document_status med_status ON dt.medical_records_status = med_status.id
            LEFT JOIN document_status med_complete ON dt.medical_records_complete = med_complete.id
            WHERE doc.id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $docId);
    $stmt->execute();

    $result = $stmt->get_result();
    $record = $result->fetch_assoc();
    $stmt->close();
}
$conn->close();

// ถ้ามีข้อมูล แปลงค่า NULL เป็น "-"
if ($record) {
    $record = array_map(function($v) {
        return is_null($v) ? "-" : $v;
    }, $record);
}

// // แสดงผลข้อมูลทั้งหมด
// echo "<pre>";
// print_r($record);
// echo "</pre>";

return $record

// [id] => 6837621
// [create_at] => 2026-02-01
// [customer_name] => AABBCC
// [workgroup_id] => G001
// [department_id] => D001
// [staff_id] => 002
// [remark] => 
// [department_name] => หอผู้ป่วยกระดูกและข้อชาย 1
// [workgroup_name] => กลุ่มงานศัลยกรรมออร์โธปิดิกส์
// [staff_name] => นพ.ปกรณ์ นาระคล
// [status_name] => เสร็จสิ้น
// [doc_id] => 6837621
// [doc_status] => 4
// [resident_id] => 002
// [resident_create_at] => 2026-02-03
// [resident_duedate] => 2
// [resident_status] => เสร็จสิ้น
// [resident_complete] => เสร็จสิ้น
// [resident_complete_at] => 2026-02-04
// [staff_create_at] => 2026-02-04
// [staff_duedate] => 5
// [staff_status] => เสร็จสิ้น
// [staff_complete] => เสร็จสิ้น
// [staff_complete_at] => 2026-02-04
// [auditor_id] => 
// [auditor_create_at] => 2026-02-04
// [auditor_duedate] => 3
// [auditor_status] => เสร็จสิ้น
// [auditor_complete] => เสร็จสิ้น
// [auditor_complete_at] => 2026-02-05
// [medical_records_create_at] => 2026-02-04
// [medical_records_duedate] => 1
// [medical_records_status] => เสร็จสิ้น
// [medical_records_complete] => เสร็จสิ้น
// [medical_records_complete_at] => 2026-02-04
// [resident_name] => นพ.ปกรณ์ นาระคล
// [auditor_name] => 

?>
