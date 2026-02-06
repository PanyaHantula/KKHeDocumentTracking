<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$isLogin  = isset($_SESSION['user_id']);
$userName = $isLogin ? $_SESSION['name'] : 'Guest';
?>

<style>
    /* --- Navbar & Deep Blue Theme Overrides --- */
    :root {
        --primary-navy: #001f3f;
        --primary-blue: #003366;
        --text-dark: #2c3e50;
        --nav-bg: #ffffff;
    }

    .navbar-custom {
        background-color: var(--nav-bg);
        box-shadow: 0 2px 15px rgba(0, 31, 63, 0.08); /* เงาสีน้ำเงินจางๆ */
        height: 70px; /* เพิ่มความสูงให้ดูโปร่ง */
        z-index: 1000;
    }

    /* ปุ่ม Toggle Sidebar */
    .sidebar-toggler {
        color: var(--primary-navy);
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        transition: all 0.3s ease;
        text-decoration: none;
    }
    
    .sidebar-toggler:hover {
        background-color: rgba(0, 31, 63, 0.05);
        color: var(--primary-blue);
    }

    /* ส่วนแสดงชื่อผู้ใช้ */
    .nav-profile {
        color: var(--primary-navy);
        font-weight: 600;
        transition: 0.3s;
        border-radius: 30px;
        padding: 5px 15px;
    }

    .nav-profile:hover {
        background-color: rgba(0, 31, 63, 0.05);
        color: var(--primary-blue);
    }

    /* Dropdown Menu - ทำเป็น Card ลอย */
    .dropdown-menu-custom {
        border: none;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        border-radius: 12px;
        padding: 10px 0;
        margin-top: 15px !important; /* เว้นระยะห่างจาก Navbar นิดหน่อย */
        min-width: 200px;
    }

    .dropdown-item {
        padding: 10px 20px;
        color: var(--text-dark);
        transition: all 0.2s;
        font-size: 0.95rem;
    }

    .dropdown-item:hover {
        background-color: rgba(0, 31, 63, 0.05);
        color: var(--primary-navy);
        padding-left: 25px; /* Effect ขยับเมื่อ Hover */
    }

    .dropdown-item.text-danger:hover {
        background-color: #fff5f5;
        color: #dc3545;
    }
    
    /* Animation เล็กน้อยสำหรับ Dropdown */
    .dropdown-menu.show {
        animation: fadeInDown 0.3s ease-out forwards;
    }

    @keyframes fadeInDown {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>

<nav class="navbar navbar-expand navbar-light sticky-top px-4 py-0 navbar-custom">
    <a href="#" class="sidebar-toggler flex-shrink-0 me-3">
        <i class="fa fa-bars fs-4"></i>
    </a>

    <div class="navbar-nav align-items-center ms-auto">
        <div class="nav-item dropdown">
            <a href="#" class="nav-link dropdown-toggle d-flex align-items-center nav-profile" data-bs-toggle="dropdown">
                <div class="bg-light rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 35px; height: 35px;">
                    <i class="fa fa-user text-primary"></i>
                </div>
                <span class="d-none d-lg-inline-flex"><?= htmlspecialchars($userName) ?></span>
            </a>
            
            <div class="dropdown-menu dropdown-menu-end dropdown-menu-custom m-0">
                <div class="px-3 py-2 border-bottom mb-2">
                    <small class="text-muted d-block">Signed in as</small>
                    <span class="fw-bold text-dark"><?= htmlspecialchars($userName) ?></span>
                </div>

                <a href="/db/db-logout.php" class="dropdown-item text-danger">
                    <i class="fa fa-sign-out-alt me-2"></i> ออกจากระบบ
                </a>
            </div>
        </div>
    </div>
</nav>