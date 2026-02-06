<?php
include __DIR__ . '/../db/db-config.php';

// 1. อัปเดตสถานะรายบุคคลที่เกิน Due Date จาก 2 เป็น 3 ก่อน
$sql_update_individual = "UPDATE document_track 
SET 
    resident_status = IF(resident_status = 2 AND DATE_ADD(resident_create_at, INTERVAL resident_duedate DAY) < CURDATE(), 3, resident_status),
    staff_status = IF(staff_status = 2 AND DATE_ADD(staff_create_at, INTERVAL staff_duedate DAY) < CURDATE(), 3, staff_status),
    medical_records_status = IF(medical_records_status = 2 AND DATE_ADD(medical_records_create_at, INTERVAL medical_records_duedate DAY) < CURDATE(), 3, medical_records_status),
    auditor_status = IF(auditor_status = 2 AND DATE_ADD(auditor_create_at, INTERVAL auditor_duedate DAY) < CURDATE(), 3, auditor_status)
WHERE 
    resident_status = 2 OR staff_status = 2 OR medical_records_status = 2 OR auditor_status = 2";

$conn->query($sql_update_individual);

// 2. อัปเดต doc_status ภาพรวมของเอกสาร
// เงื่อนไข: ถ้ามีใครคนใดคนหนึ่งเป็น 3 (ล่าช้า) ให้ doc_status เป็น 3
// แต่ถ้าไม่มีใครเป็น 3 เลย และยังมีคนเป็น 2 (รอสรุป) ให้คงสถานะภาพรวมเป็น 2 (หรือค่าเดิมที่แสดงว่ากำลังดำเนินการ)
$sql_update_global = "UPDATE document_track 
SET doc_status = CASE 
    WHEN (resident_status = 3 OR staff_status = 3 OR medical_records_status = 3 OR auditor_status = 3) THEN 3
    WHEN (resident_status = 2 OR staff_status = 2 OR medical_records_status = 2 OR auditor_status = 2) THEN 2
    ELSE doc_status 
END
WHERE resident_status IN (2, 3) 
   OR staff_status IN (2, 3) 
   OR medical_records_status IN (2, 3) 
   OR auditor_status IN (2, 3)";

if ($conn->query($sql_update_global) === TRUE) {
    // อัปเดตสำเร็จ
} else {
    error_log("Update Global Status Error: " . $conn->error);
}
?>