<?php
include __DIR__ . '/../db/db-config.php'; // ตรวจสอบ Path ให้ถูกต้อง

function getDocumentStats($conn) {
    $sql = "SELECT 
                COUNT(doc_id) AS total_docs,
                SUM(IF(doc_status = 1, 1, 0)) AS status_pending,
                SUM(IF(doc_status = 2, 1, 0)) AS status_waiting,
                SUM(IF(doc_status = 3, 1, 0)) AS status_late,
                SUM(IF(doc_status = 4, 1, 0)) AS status_complete
            FROM document_track";

    $result = $conn->query($sql);
    return ($result) ? $result->fetch_assoc() : null;
}

//ดึงข้อมูล
$stats = getDocumentStats($conn);

if ($stats) {
    // เก็บค่าลง Session เพื่อให้ index.php นำไปใช้ได้
    $_SESSION['total_docs']     = $stats['total_docs'] ?? 0;
    $_SESSION['status_pending']  = $stats['status_pending'] ?? 0;
    $_SESSION['status_waiting']  = $stats['status_waiting'] ?? 0;
    $_SESSION['status_late']     = $stats['status_late'] ?? 0;
    $_SESSION['status_complete'] = $stats['status_complete'] ?? 0;
}
// ไม่ต้อง $conn->close() ที่นี่ เพราะ index.php มีการ include ไฟล์ db-config ซ้ำอีกรอบ
?>