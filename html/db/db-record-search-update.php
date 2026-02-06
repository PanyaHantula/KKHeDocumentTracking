<?php
include_once __DIR__ . '/db-config.php';

// 1. กำหนดตัวแปรเริ่มต้น
$all_fields = [
    'id', 'create_at', 'customer_name', 'workgroup_id', 'department_id', 'staff_id',
    'status', 'complete', 'complete_at', 'remark',
    'department_name', 'workgroup_name', 'staff_name', 'status_name',
    'doc_id', 'resident_id', 'resident_name', 'resident_create_at', 'resident_duedate',
    'resident_status', 'resident_complete', 'resident_complete_at',
    'staff_create_at', 'staff_duedate', 'staff_status', 'staff_complete', 'staff_complete_at',
    'auditor_id', 'auditor_name', 'auditor_create_at', 'auditor_duedate',
    'auditor_status', 'auditor_complete', 'auditor_complete_at',
    'medical_records_create_at','medical_records_duedate', 'medical_records_status',
    'medical_records_complete','medical_records_complete_at'   
];

// เติมค่าว่าง (-) เพื่อกัน Error ในหน้า HTML
$record = array_fill_keys($all_fields, '-');
// ==========================================
// ส่วนที่ 1: จัดการการบันทึกข้อมูล (POST)
// ==========================================
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && isset($_GET['doc_id'])) {
    $action = $_POST['action'];
    $docId = $_GET['doc_id'];
    $now = date('Y-m-d'); 
    
    $update_sql = "";
    $params = [];
    $types = "";

    switch ($action) {
        // --- 1. Resident ---
        case 'resident_start':
            $res_id = $_POST['resident_id'] ?? null;
            $res_date = $_POST['resident_create_at'] ?? $now;
            $res_due = $_POST['resident_duedate'] ?? 0;
            $update_sql = "UPDATE document_track SET resident_id = ?, resident_duedate = ?, resident_status = 2, resident_complete = 2, resident_create_at = ? WHERE doc_id = ?";
            $params = [$res_id, $res_due, $res_date, $docId];
            $types = "siss"; 
            break;
        case 'resident_finish':
            $update_sql = "UPDATE document_track SET resident_status = 4, resident_complete = 4, resident_complete_at = ? WHERE doc_id = ?";
            $params = [$now, $docId];
            $types = "ss";
            break;
        case 'resident_manual_update':
            $new_status = $_POST['new_status'];
            $update_sql = "UPDATE document_track SET resident_status = ?, resident_complete = ? , resident_complete_at = ? WHERE doc_id = ?"; 
            $params = [$new_status, $new_status, null, $docId];
            $types = "iiss";
            break;

        // --- 2. Staff ---
        case 'staff_start':
            $stf_id = $_POST['staff_id'] ?? null;
            $stf_date = $_POST['staff_create_at'] ?? $now;
            $stf_due = $_POST['staff_duedate'] ?? 0;
            $update_sql = "UPDATE document_track SET staff_id = ?, staff_duedate = ?, staff_status = 2, staff_complete = 2, staff_create_at = ? WHERE doc_id = ?";
            $params = [$stf_id, $stf_due, $stf_date, $docId];
            $types = "siss";
            break;
        case 'staff_finish':
            $update_sql = "UPDATE document_track SET staff_status = 4, staff_complete = 4, staff_complete_at = ? WHERE doc_id = ?";
            $params = [$now, $docId];
            $types = "ss";
            break;
        case 'staff_manual_update':
            $new_status = $_POST['staff_new_status'];
            $update_sql = "UPDATE document_track SET staff_status = ?, staff_complete = ?, staff_complete_at = ? WHERE doc_id = ?"; 
            $params = [$new_status, $new_status, null, $docId];
            $types = "iiss";
            break;

        // --- 3. Medical Records (เวชระเบียน) ---
        case 'medical_records_start':
            $med_date = $_POST['medical_records_create_at'] ?? $now;
            $med_due = $_POST['medical_records_duedate'] ?? 0;
            $update_sql = "UPDATE document_track SET medical_records_duedate = ?, medical_records_status = 2, medical_records_complete = 2, medical_records_create_at = ? WHERE doc_id = ?";
            $params = [$med_due, $med_date, $docId];
            $types = "iss";
            break;
        case 'medical_records_finish':
            $update_sql = "UPDATE document_track SET medical_records_status = 4, medical_records_complete = 4, medical_records_complete_at = ? WHERE doc_id = ?";
            $params = [$now, $docId];
            $types = "ss";
            break;
        case 'medical_records_manual_update':
            $new_status = $_POST['medical_records_new_status'];
            $update_sql = "UPDATE document_track SET medical_records_status = ?, medical_records_complete = ?, medical_records_complete_at = ? WHERE doc_id = ?"; 
            $params = [$new_status, $new_status,null, $docId];
            $types = "iiss";
            break;

        // --- 4. Auditor ---
        case 'auditor_start':
            $aud_id = $_POST['auditor_id'] ?? null;
            $aud_date = $_POST['auditor_create_at'] ?? $now;
            $aud_due = $_POST['auditor_duedate'] ?? 0;
            $update_sql = "UPDATE document_track SET auditor_id = ?, auditor_duedate = ?, auditor_status = 2, auditor_complete = 2, auditor_create_at = ? WHERE doc_id = ?";
            $params = [$aud_id, $aud_due, $aud_date, $docId];
            $types = "siss";
            break;
        case 'auditor_finish':
            $update_sql = "UPDATE document_track SET auditor_status = 4, auditor_complete = 4, auditor_complete_at = ? WHERE doc_id = ?";
            $params = [$now, $docId];
            $types = "ss";
            break;
        case 'auditor_manual_update':
            $new_status = $_POST['auditor_new_status'];
            $update_sql = "UPDATE document_track SET auditor_status = ?, auditor_complete = ?,auditor_complete_at = ?  WHERE doc_id = ?"; 
            $params = [$new_status, $new_status, null, $docId];
            $types = "iiss";
            break;


    }

    if (!empty($update_sql)) {
        $stmt_up = $conn->prepare($update_sql);
        $stmt_up->bind_param($types, ...$params);
        
        if ($stmt_up->execute()) {
            // -------------------------------------
            // Logic ตรวจสอบสถานะรวม (Master Status Calculation)
            // -------------------------------------
            $check_sql = "SELECT resident_status, staff_status, medical_records_status, auditor_status FROM document_track WHERE doc_id = ?";
            $stmt_check = $conn->prepare($check_sql);
            $stmt_check->bind_param("s", $docId);
            $stmt_check->execute();
            $res_check = $stmt_check->get_result()->fetch_assoc();
            

            $new_doc_status = null;

            // Priority Logic: 
                       // 1. ถ้าทุกคนเป็น 1 -> สถานะรวม 1
            if ($res_check['resident_status'] == 1 && $res_check['staff_status'] == 1 && $res_check['medical_records_status'] == 1 && $res_check['auditor_status'] == 1) {
                $conn->query("UPDATE document_track SET doc_status = 1 WHERE doc_id = '$docId'");
                $record['status_name'] = 'ยังไม่ส่งสรุป';
            }
            // 2. ถ้ามีใครคนใดคนหนึ่งเป็น 2 -> สถานะรวม 2 (กำลังดำเนินการ)
            elseif ($res_check['resident_status'] == 2 || $res_check['staff_status'] == 2 || $res_check['medical_records_status'] == 2 || $res_check['auditor_status'] == 2 || $res_check['resident_status'] == 1 || $res_check['staff_status'] == 1 || $res_check['medical_records_status'] == 1 || $res_check['auditor_status'] == 1) {
                $conn->query("UPDATE document_track SET doc_status = 2 WHERE doc_id = '$docId'");
                $record['status_name'] = 'รอสรุป';
            }
            // 3. ถ้าทุกคนเป็น 4 -> สถานะรวม 4 (เสร็จสิ้น)
            elseif ($res_check['resident_status'] == 4 && $res_check['staff_status'] == 4 && $res_check['medical_records_status'] == 4 && $res_check['auditor_status'] == 4) {
                $conn->query("UPDATE document_track SET doc_status = 4 WHERE doc_id = '$docId'");
                $record['status_name'] = 'เสร็จสิ้น';
            }
            // 4. ถ้ามีใครคนใดคนหนึ่งเป็น 3 -> สถานะรวม 3 (ล่าช้า/ติดขัด)
            elseif ($res_check['resident_status'] == 3 || $res_check['staff_status'] == 3 || $res_check['medical_records_status'] == 3 || $res_check['auditor_status'] == 3) {
                $conn->query("UPDATE document_track SET doc_status = 3 WHERE doc_id = '$docId'");
                $record['status_name'] = 'ล่าช้า';
            }
 
            
            $stmt_check->close(); 
            // -------------------------------------

        } else {
            echo "<script>alert('เกิดข้อผิดพลาด: " . $conn->error . "');</script>
            ";
        }
        $stmt_up->close();
    }
}

// ==========================================
// ส่วนที่ 2: ดึงข้อมูลมาแสดง (GET/Search)
// ==========================================
if (isset($_GET['doc_id']) && !empty($_GET['doc_id'])) {
    $docId = trim($_GET['doc_id']);

    // SQL Query
    // หมายเหตุ: dt.* จะดึงข้อมูลจาก document_track
    // *** สำคัญ: ในฐานข้อมูล ตาราง document_track ต้องมีชื่อคอลัมน์ตรงกับตัวแปรใหม่ 
    // เช่น medical_records_create_at, medical_records_status ฯลฯ ***
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
    
    // ถ้าเจอข้อมูล ให้เอาข้อมูลจริงมาใส่แทนที่ค่า "-"
    if ($result->num_rows > 0) {
        $db_data = $result->fetch_assoc();
        
        foreach ($db_data as $key => $value) {
            // เช็คว่า key จาก DB ตรงกับที่เรากำหนดไว้ไหม
            if (array_key_exists($key, $record)) {
                $record[$key] = is_null($value) ? "-" : $value;
            }
        }
    } 
    
    $stmt->close();

    // // แสดงผลข้อมูลทั้งหมด
    // echo "<pre>";
    // print_r($record);
    // echo "</pre>";

}

?>