<!-- sidebbar.php -->
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$isLogin          = isset($_SESSION['user_id']);

$userName         = $isLogin ? ($_SESSION['name'] ?? 'Guest') : 'Guest';
$department_ID    = $isLogin ? ($_SESSION['department_id'] ?? '-') : '-';
$department_name  = $isLogin ? ($_SESSION['department_name'] ?? '-') : '-'; 
$workgroup_name  = $isLogin ? ($_SESSION['workgroup_name'] ?? '-') : '-'; 
$roleId           = $isLogin ? ($_SESSION['role'] ?? '-') : '-';         
$roleName         = $isLogin ? ($_SESSION['role_name'] ?? '-') : '-';
?>

<style>
    /* --- CSS Variables & Deep Blue Theme --- */
    :root {
        --sidebar-bg: #001f3f;          /* สีพื้นหลังหลัก Navy */
        --sidebar-text: #ecf0f1;        /* สีตัวอักษรขาวนวล */
        --card-bg: rgba(255, 255, 255, 0.1); /* สีพื้นหลัง Card แบบโปร่งแสง */
        --accent-color: #39cccc;        /* สีตกแต่ง */
    }

    /* --- ส่วนที่ 1: Logo Card Design --- */
    .logo-card {
        background-color: #ffffff;
        border-radius: 12px;
        padding: 15px;
        margin: 15px 15px 25px 15px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.3);
        display: flex;
        justify-content: center;
        align-items: center;
        transition: transform 0.3s;
    }
    .logo-card:hover {
        transform: scale(1.02);
    }

    /* --- ส่วนที่ 2: User Profile Card Design --- */
    .user-card {
        background: var(--card-bg);
        border: 1px solid rgba(255,255,255,0.1);
        backdrop-filter: blur(5px); /* Effect กระจกฝ้า */
        border-radius: 15px;
        padding: 15px;
        margin: 0 15px 20px 15px;
    }

    .user-info-text h6 {
        color: #070133;
        font-weight: 700;
        letter-spacing: 0.5px;
        white-space: nowrap;      /* บรรทัดเดียว */
        overflow: hidden;         /* ซ่อนส่วนที่เกิน */
        text-overflow: ellipsis;  /* เติม ... ถ้าข้อความยาวเกิน */
        max-width: 130px;         /* กำหนดความกว้างสูงสุดเพื่อให้ ... ทำงาน */
    }
    
    .user-info-text span {
        color: #bdc3c7;
        font-size: 0.85rem;
        white-space: nowrap;      /* บรรทัดเดียว */
    }

    .dept-badge {
        background-color: rgba(0, 0, 0, 0.2);
        color: var(--accent-color);
        font-size: 0.75rem;       /* ลดขนาดฟอนต์ลงเล็กน้อยเพื่อให้พอดีบรรทัด */
        padding: 5px 10px;
        border-radius: 20px;
        margin-top: 5px;
        display: block;           /* ให้แสดงเป็นบล็อก */
        width: 100%;
        text-align: left;         /* ชิดซ้ายจะดูเป็นระเบียบกว่าเมื่ออยู่ในบรรทัดเดียว */
        white-space: nowrap;      /* บรรทัดเดียว */
        border: 1px dashed rgba(57, 204, 204, 0.3);
    }

</style>

<!-- Spinner Start -->
<div id="spinner"
    class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
    <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
        <span class="sr-only">Loading...</span>
    </div>
</div>

<!-- Sidebar -->
<div class="sidebar pe-2 pb-1">
    <nav class="navbar bg-light navbar-light">
        <!-- Banner logo -->
        <div class="logo-card">
            <img src="img/KhonKaenHospital.png" alt="Logo" style="height: auto; width: 100%; max-width: 180px; object-fit: contain;">
        </div>

        <!-- User Profile -->
        <div class="user-card">
            <div class="d-flex align-items-center mb-2">
                <div class="position-relative flex-shrink-0"> 
                    <img src="img/user.png" alt="User" style="width: 45px; height: 45px; object-fit: cover;" class="rounded-circle border">
                    <div class="bg-success rounded-circle border border-2 position-absolute end-0 bottom-0 p-1"></div>
                </div>
                
                <div class="ms-3 user-info-text" style="overflow: hidden;">
                    <h6 class="mb-0">
                        <?= htmlspecialchars($userName) ?>
                    </h6>
                    <span class="d-block">
                        <?= htmlspecialchars($roleName) ?>
                    </span>
                </div>
            </div>
            
            <div class="dept-badge text-dark mt-2">
                <i class="fas fa-hospital-symbol me-1"></i> <?= htmlspecialchars($workgroup_name) ?>
            </div>
            <div class="dept-badge text-dark mt-1">
                <i class="fas fa-hospital-symbol me-1"></i> <?= htmlspecialchars($department_name) ?>
            </div>
        </div>

        <!-- Manu -->
        <div class="navbar-nav w-100">
            <a href="index.php" class="nav-item nav-link"><i class="fas fa-columns"></i>ผลการดำเนินงาน</a>

            <?php if ($roleId == 1 || $roleId == 2): ?>
            <a href="document-record.php" class="nav-item nav-link"><i class="fas fa-search"></i>บันทึกงาน</a>
            <a href="document-register.php" class="nav-item nav-link"><i
                    class="fas fa-window-restore"></i>ลงทะเบียนเอกสาร</a>
            <?php endif; ?>

            <a href="document-list.php" class="dropdown-item mx-2 pt-2"><i class="fas fa-list-alt" style="margin-right:8px;"></i>รายการทั้งหมด</a>

            <!-- manager viwer -->
            <?php if ($roleId == 2): ?>
            <div class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle " data-bs-toggle="dropdown"><i class="fas fa-sliders-h"
                        style="margin-right:8px;"></i>เพิ่มเติม</a>
                <div class="dropdown-menu bg-transparent border-0 mx-2">
                    <a href="auditor-register.php" class="dropdown-item  mx-2"><i class="fas fa-user-circle"
                            style="margin-right:8px;"></i>ผู้ตรวจเอกสาร</a>
                </div>
            </div>
            <?php endif; ?>

            <!-- admin viwer -->
            <?php if ($roleId == 1): ?>
            <div class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle " data-bs-toggle="dropdown"><i class="fas fa-sliders-h"
                        style="margin-right:8px;"></i>เพิ่มเติม</a>
                <div class="dropdown-menu bg-transparent border-0 mx-2">
                    <a href="workgroup-management.php" class="dropdown-item  mx-2"><i class="fas fa-users"
                            style="margin-right:8px;"></i>กลุ่มงาน</a>
                    <a href="department-management.php" class="dropdown-item  mx-2"><i class="fas fa-stethoscope"
                            style="margin-right:8px;"></i>หอผู้ป่วย</a>
                    <a href="auditor-management.php" class="dropdown-item  mx-2"><i class="fas fa-user-circle"
                            style="margin-right:8px;"></i>ผู้ตรวจเอกสาร</a>
                </div>
            </div>

            <a href="user-management.php" class="nav-item nav-link"><i class="fas fa-user-lock"></i>จัดการผู้ใช้งาน</a>
            <?php endif; ?>

            <div class="bg-light text-center py-4 shadow-sm">
                <a href="/db/db-logout.php" class="btn btn-danger btn-lg">
                    <i class="fa fa-sign-out-alt me-2"></i> ออกจากระบบ
                </a>
            </div>


        </div>
        <!-- End Manu -->
    </nav>
</div>