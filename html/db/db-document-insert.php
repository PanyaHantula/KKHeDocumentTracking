<?php
include __DIR__ . '/db-config.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $doc_ID = trim($_POST['doc_ID']);
    $doc_Create_at = $_POST['dateCreate'];
    $customer_name = trim($_POST['customer_name']);
    $workgroup_id = trim($_POST['workgroupID']);
    $department_id = trim($_POST['department_id']);
    $staff_id = trim($_POST['staff_id']);
    $status = 1;
    $remark = trim($_POST['remark']);

    // ตรวจสอบว่ามีรหัสเอกสารส่งมาหรือไม่
    if (!empty($doc_ID)) {
        
        // --- 1. ตรวจสอบข้อมูลซ้ำ (Check Duplicate) ---
        $sql_check = "SELECT id FROM document_info WHERE id = ?";
        $stmt = $conn->prepare($sql_check);
        $stmt->bind_param("s", $doc_ID);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            // ถ้ารหัสซ้ำ ให้แจ้งเตือนและหยุดการทำงาน
            $stmt->close();
            header("Location: /document-register.php?msg=รหัสเอกสารนี้ ($doc_ID) มีอยู่แล้ว โปรดระบุหมายเลขใหม่&type=warning");
            exit();
        }
        $stmt->close(); // ปิด statement ของการตรวจสอบก่อน

        // --- 2. บันทึกข้อมูล (Insert Data) ---
        // (ย้ายมาไว้นอก else เพื่อให้ทำงานต่อเมื่อไม่พบข้อมูลซ้ำ)
        $sql_insert = "INSERT INTO document_info (id, create_at, customer_name, workgroup_id, department_id, staff_id, remark)
                       VALUES (?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql_insert);
        
        // ใช้ try-catch เพื่อดักจับ Error กรณีอื่นๆ
        try {
            $stmt->bind_param("sssssss", $doc_ID, $doc_Create_at, $customer_name, $workgroup_id, $department_id, $staff_id, $remark);
            if ($stmt->execute()) {
            } else {
                echo "Error executing statement: " . $stmt->error;
            }
        } catch (mysqli_sql_exception $e) {
            // กรณีเกิด Error ทาง SQL อื่นๆ (รวมถึง Duplicate ที่อาจเล็ดลอดมา)
            echo "Error: " . $e->getMessage();
        }

        $stmt->close();

        //----------------------------------------
        
        // --- 2. บันทึกข้อมูล (Insert Data) ---
        // (ย้ายมาไว้นอก else เพื่อให้ทำงานต่อเมื่อไม่พบข้อมูลซ้ำ)
        $sql_insert = "INSERT INTO document_track (doc_id, staff_id) VALUES (?, ?)";

        $stmt = $conn->prepare($sql_insert);
        
        // ใช้ try-catch เพื่อดักจับ Error กรณีอื่นๆ
        try {
            $stmt->bind_param("ss", $doc_ID, $staff_id);
            if ($stmt->execute()) {
                header("Location: /document-register.php?msg=บันทึกข้อมูลสำเร็จ&type=success");
                exit();
            } else {
                echo "Error executing statement: " . $stmt->error;
            }
        } catch (mysqli_sql_exception $e) {
            // กรณีเกิด Error ทาง SQL อื่นๆ (รวมถึง Duplicate ที่อาจเล็ดลอดมา)
            echo "Error: " . $e->getMessage();
        }

        $stmt->close();

        //----------------------------------------
        

    } else {
        // กรณีไม่ได้กรอก ID มา
        header("Location: /document-register.php?msg=กรุณาระบุเลขที่เอกสาร&type=danger");
        exit();
    }
}
$conn->close();
?>