<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// ป้องกัน cache หน้าก่อน logout (กันกด back แล้วเห็นหน้าเดิม)
header("Cache-Control: no-cache, no-store, must-revalidate");   // HTTP 1.1
header("Pragma: no-cache");                                     // HTTP 1.0
header("Expires: 0");                                           // Proxies
?>