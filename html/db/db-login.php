<?php
if (session_status() === PHP_SESSION_NONE) {

    ini_set('session.cookie_lifetime', 0); // หมดอายุเมื่อปิด browser
    ini_set('session.gc_maxlifetime', 0);
    session_start();
    
}

include __DIR__ . '/db-config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // ดึงข้อมูลจาก DB
    $sql = "SELECT 
                u.*,
                d.name AS department_name,
                r.name AS role_name,
                wg.name AS workgroup_name
            FROM users u
            LEFT JOIN department d ON u.department_id = d.id
            LEFT JOIN workgroups wg ON d.workgroup_id = wg.id
            LEFT JOIN role r ON u.role = r.id
            WHERE u.user = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        // debug
        // echo "<pre>";
        // print_r($row);
        // echo "</pre>";

        if ($password === $row['pwd']) {   
            $_SESSION['user_id']       = $row['id'];
            $_SESSION['name']          = $row['name'];              // ชื่อผู้ใช้จริงจาก users
            $_SESSION['department_id'] = $row['department_id'];
            $_SESSION['department_name']    = $row['department_name'];   // แผนก
            $_SESSION['workgroup_id'] = $row['workgroup_id'];
            $_SESSION['workgroup_name']    = $row['workgroup_name'];   // แผนก
            $_SESSION['role']          = $row['role'];              // role id
            $_SESSION['role_name']     = $row['role_name'];         // role ชื่อจากตาราง role

            header("Location: index.php");
            exit();
        } else {
            $error = "รหัสผ่านไม่ถูกต้อง";
        }
     } else {
         $error = "ไม่พบผู้ใช้งาน";
     }
    
    // close connection
    $stmt->close();
    $conn->close();
}
?>