<?php
include __DIR__ . '/../db/db-config.php';

// 1. อัปเดตสถานะรายบุคคล (Individual Status) จาก 2 เป็น 3 หากเกิน Due Date
$sql_update_individual = "UPDATE document_track 
SET 
    resident_status = IF(resident_status = 2 AND DATE_ADD(resident_create_at, INTERVAL resident_duedate DAY) < CURDATE(), 3, resident_status),
    staff_status = IF(staff_status = 2 AND DATE_ADD(staff_create_at, INTERVAL staff_duedate DAY) < CURDATE(), 3, staff_status),
    medical_records_status = IF(medical_records_status = 2 AND DATE_ADD(medical_records_create_at, INTERVAL medical_records_duedate DAY) < CURDATE(), 3, medical_records_status),
    auditor_status = IF(auditor_status = 2 AND DATE_ADD(auditor_create_at, INTERVAL auditor_duedate DAY) < CURDATE(), 3, auditor_status)
WHERE 2 IN (resident_status, staff_status, medical_records_status, auditor_status)";

$conn->query($sql_update_individual);

// 2. อัปเดต doc_status ภาพรวม (Global Status) ตาม Priority Logic
$sql_update_global = "UPDATE document_track 
SET doc_status = CASE 
    -- ลำดับ 1: ถ้ามีใครคนใดคนหนึ่งเป็น 3 (ล่าช้า) -> ภาพรวมต้องเป็น 3 ทันที (สำคัญที่สุด)
    WHEN 3 IN (resident_complete, staff_complete, medical_records_complete, auditor_complete) THEN 3

    -- ลำดับ 2: ถ้าไม่มีใครล่าช้า แต่มีใครคนใดคนหนึ่งเป็น 2 (รอสรุป) -> ภาพรวมเป็น 2
    WHEN 2 IN (resident_complete, staff_complete, medical_records_complete, auditor_complete) THEN 2

    -- ลำดับ 3: ถ้าทุกคนเป็น 4 (เสร็จสิ้น) -> ภาพรวมเป็น 4
    WHEN (resident_complete = 4 AND staff_complete = 4 AND medical_records_complete = 4 AND auditor_complete = 4) THEN 4

    -- ลำดับ 4: ถ้าทุกคนเป็น 1 (รอดำเนินการ) -> ภาพรวมเป็น 1
    WHEN (resident_complete= 1 AND staff_complete = 1 AND medical_records_complete = 1 AND auditor_complete = 1) THEN 1

    ELSE doc_status 
END";
// หมายเหตุ: ตัด WHERE doc_status != 4 ออก เพื่อให้ระบบสามารถเปลี่ยนสถานะเป็น 4 ได้เมื่อทุกคนทำเสร็จ

if ($conn->query($sql_update_global) === TRUE) {
    // อัปเดตสำเร็จ
} else {
    error_log("Update Global Status Error: " . $conn->error);
}
?>