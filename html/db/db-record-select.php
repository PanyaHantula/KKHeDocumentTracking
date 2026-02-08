<?php
include __DIR__ . '/db-config.php';

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

// [id] => 6837621
// [create_at] => 2026-02-01
// [customer_name] => BBBB
// [workgroup_id] => G001
// [department_id] => D003
// [staff_id] => 004
// [remark] => 
// [department_name] => หอผู้ป่วยกระดูกหญิง
// [workgroup_name] => กลุ่มงานศัลยกรรมออร์โธปิดิกส์
// [staff_name] => นพ.เกรียงศักดิ์ ปิยกุลมาลา
// [status_name] => ล่าช้า
// [doc_id] => 6837621
// [doc_status] => 3
// [resident_id] => 
// [resident_create_at] => 2026-02-02
// [resident_duedate] => 2
// [resident_status] => ล่าช้า
// [resident_complete] => รอสรุป
// [resident_complete_at] => 
// [staff_create_at] => 
// [staff_duedate] => 5
// [staff_status] => ยังไม่ส่งสรุป
// [staff_complete] => ยังไม่ส่งสรุป
// [staff_complete_at] => 
// [auditor_id] => 
// [auditor_create_at] => 
// [auditor_duedate] => 3
// [auditor_status] => ยังไม่ส่งสรุป
// [auditor_complete] => ยังไม่ส่งสรุป
// [auditor_complete_at] => 
// [medical_records_create_at] => 
// [medical_records_duedate] => 1
// [medical_records_status] => ยังไม่ส่งสรุป
// [medical_records_complete] => ยังไม่ส่งสรุป
// [medical_records_complete_at] => 
// [resident_name] => 
// [auditor_name] => 
?>
