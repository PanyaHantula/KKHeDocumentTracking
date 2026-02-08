<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include __DIR__ . '/../db/db-config.php'; 

function getDocumentStats($conn) {
    // ใช้ COALESCE เพื่อเปลี่ยน NULL ให้เป็น 0 เสมอ
    $sql = "SELECT 
                COUNT(*) AS total_docs,
                COALESCE(SUM(doc_status = 1), 0) AS status_pending,
                COALESCE(SUM(doc_status = 2), 0) AS status_waiting,
                COALESCE(SUM(doc_status = 3), 0) AS status_late,
                COALESCE(SUM(doc_status = 4), 0) AS status_complete
            FROM document_track";

    $result = $conn->query($sql);
    return ($result) ? $result->fetch_assoc() : null;
}

// ดึงข้อมูล
$stats = getDocumentStats($conn);

if ($stats) {
    // เก็บค่าลง Session 
    // ใช้ (int) เพื่อบังคับให้เป็นตัวเลขเสมอ ป้องกันปัญหาในการนำไปคำนวณต่อ
    $_SESSION['total_docs']      = (int)$stats['total_docs'];
    $_SESSION['status_pending']  = (int)$stats['status_pending'];
    $_SESSION['status_waiting']  = (int)$stats['status_waiting'];
    $_SESSION['status_late']     = (int)$stats['status_late'];
    $_SESSION['status_complete'] = (int)$stats['status_complete'];
}
?>

