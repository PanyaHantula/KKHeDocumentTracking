<?php
session_start();
session_unset();
session_destroy();

// ป้องกัน cache หน้าเก่า
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// กลับไปหน้า login
header("Location: /login.php");
exit();
