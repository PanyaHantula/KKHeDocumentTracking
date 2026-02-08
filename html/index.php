<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// ป้องกัน cache
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

$name = $_SESSION['name'];

include __DIR__ . '/auth.php'; 
include __DIR__ . '/service/update-overdue.php'; 
include __DIR__ . '/service/getsummary.php'; 

$totalRecords      = $_SESSION['total_docs'] ?? 0;
$totalOutOfProcess = $_SESSION['status_pending'] ?? 0;
$totalOnProcess    = $_SESSION['status_waiting'] ?? 0;
$totalOverDueDate  = $_SESSION['status_late'] ?? 0;
$totalComplete     = $_SESSION['status_complete'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>ระบบติดตามงานเอกสารโรงพยาบาล</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <?php include __DIR__ . '/css-link-library.php'; ?>

    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    <style>
        /* --- Modern Deep Blue Theme --- */
        :root {
            --primary-navy: #0F2557;
            --primary-blue: #2851A6;
            --accent-blue: #3B71CA;
            --bg-color: #F4F7FE;
            --text-dark: #2D3748;
            --card-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);

            /* Status Colors */
            --color-total: #2851A6;
            --color-wait: #6C757D;
            --color-process: #FFC107;
            --color-overdue: #DC3545;
            --color-complete: #198754;
        }

        body {
            background-color: var(--bg-color);
            font-family: 'Sarabun', sans-serif;
            color: var(--text-dark);
        }

        .content {
            background-color: var(--bg-color);
            min-height: 100vh;
        }

        /* --- Header Section --- */
        .welcome-section {
            background: white;
            border-radius: 15px;
            padding: 1.5rem 2rem;
            margin-bottom: 2rem;
            box-shadow: var(--card-shadow);
            border-left: 5px solid var(--primary-navy);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .welcome-title h2 {
            font-weight: 700;
            color: var(--primary-navy);
            margin-bottom: 0.2rem;
            font-size: 1.8rem;
        }

        .welcome-title h5 {
            font-weight: 400;
            color: #718096;
            margin-bottom: 0;
        }

        /* --- Stat Cards (Fixed Size) --- */
        .stat-card {
            background: white;
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: var(--card-shadow);
            border: 1px solid rgba(0, 0, 0, 0.03);
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
        }

        .stat-card::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: currentColor;
        }

        .stat-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1rem;
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .stat-value {
            font-size: 2.8rem;
            font-weight: 800;
            line-height: 1;
            margin-bottom: 0;
            text-align: right;
        }

        .stat-label {
            font-size: 1rem;
            color: #718096;
            font-weight: 600;
        }

        /* Color Variations */
        .card-total .stat-icon { background-color: rgba(40, 81, 166, 0.1); color: var(--color-total); }
        .card-total .stat-value { color: var(--color-total); }
        .card-total::after { background: var(--color-total); }

        .card-wait .stat-icon { background-color: rgba(108, 117, 125, 0.1); color: var(--color-wait); }
        .card-wait .stat-value { color: var(--color-wait); }
        .card-wait::after { background: var(--color-wait); }

        .card-process .stat-icon { background-color: rgba(255, 193, 7, 0.1); color: #d39e00; }
        .card-process .stat-value { color: #d39e00; }
        .card-process::after { background: #FFC107; }

        .card-overdue .stat-icon { background-color: rgba(220, 53, 69, 0.1); color: var(--color-overdue); }
        .card-overdue .stat-value { color: var(--color-overdue); }
        .card-overdue::after { background: var(--color-overdue); }

        .card-complete .stat-icon { background-color: rgba(25, 135, 84, 0.1); color: var(--color-complete); }
        .card-complete .stat-value { color: var(--color-complete); }
        .card-complete::after { background: var(--color-complete); }


        /* --- Table Section --- */
        .table-container {
            background: white;
            border-radius: 16px;
            box-shadow: var(--card-shadow);
            padding: 1.5rem;
            margin-top: 2rem;
            overflow: hidden; /* เพื่อให้มุมโค้งแสดงผลถูกต้อง */
        }

        /* CSS สำหรับตารางที่แก้ไข */
        .table-custom th, 
        .table-custom td {
            white-space: nowrap; /* สำคัญ: บังคับข้อความให้อยู่บรรทัดเดียว ไม่ตัดคำ */
            vertical-align: middle;
            padding: 12px 15px;
        }

        .table-custom thead th {
            background-color: var(--primary-navy);
            color: white;
            font-weight: 500;
            border: none;
        }
        
        /* Scrollbar Style (Optional: ทำให้แถบเลื่อนดูสวยขึ้น) */
        .table-responsive::-webkit-scrollbar {
            height: 8px;
        }
        .table-responsive::-webkit-scrollbar-track {
            background: #f1f1f1; 
            border-radius: 4px;
        }
        .table-responsive::-webkit-scrollbar-thumb {
            background: #ccc; 
            border-radius: 4px;
        }
        .table-responsive::-webkit-scrollbar-thumb:hover {
            background: #aaa; 
        }
    </style>
</head>

<body class="d-flex flex-column min-vh-100">
    <div class="container-fluid flex-fill position-relative d-flex p-0">
        <?php include __DIR__ . '/menu-bar/sidebar.php'; ?>

        <div class="content flex-fill">
            <?php include __DIR__ . '/menu-bar/navbar.php'; ?>

            <div class="container-fluid pt-4 px-4 pb-5">

                <div class="welcome-section">
                    <div class="welcome-title">
                        <h2>ยินดีต้อนรับสู่ระบบติดตามงานเอกสาร</h2>
                        <h5>โรงพยาบาลขอนแก่น</h5>
                    </div>
                </div>

                <div class="row g-3 mb-4 row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-xl-5">
                    <div class="col">
                        <div class="stat-card card-total">
                            <div class="stat-header">
                                <span class="stat-label">รายการทั้งหมด</span>
                                <div class="stat-icon"><i class="fas fa-folder-open"></i></div>
                            </div>
                            <h2 class="stat-value"><?= $totalRecords ?></h2>
                        </div>
                    </div>

                    <div class="col">
                        <div class="stat-card card-wait">
                            <div class="stat-header">
                                <span class="stat-label">ยังไม่ส่งสรุป</span>
                                <div class="stat-icon"><i class="fas fa-hourglass-start"></i></div>
                            </div>
                            <h2 class="stat-value"><?= $totalOutOfProcess ?></h2>
                        </div>
                    </div>

                    <div class="col">
                        <div class="stat-card card-process">
                            <div class="stat-header">
                                <span class="stat-label">รอสรุป</span>
                                <div class="stat-icon"><i class="fas fa-sync-alt"></i></div>
                            </div>
                            <h2 class="stat-value"><?= $totalOnProcess ?></h2>
                        </div>
                    </div>

                    <div class="col">
                        <div class="stat-card card-overdue">
                            <div class="stat-header">
                                <span class="stat-label">ล่าช้า</span>
                                <div class="stat-icon"><i class="fas fa-exclamation-triangle"></i></div>
                            </div>
                            <h2 class="stat-value"><?= $totalOverDueDate ?></h2>
                        </div>
                    </div>

                    <div class="col">
                        <div class="stat-card card-complete">
                            <div class="stat-header">
                                <span class="stat-label">เสร็จสิ้น</span>
                                <div class="stat-icon"><i class="fas fa-check-double"></i></div>
                            </div>
                            <h2 class="stat-value"><?= $totalComplete ?></h2>
                        </div>
                    </div>
                </div>

                <div class="table-container">
                    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
                        <h5 class="mb-0 fw-bold text-primary-navy">
                            <i class="fas fa-list me-2"></i>รายชื่อเอกสาร</h5>
                        <div class="d-flex gap-2">
                            <div class="input-group" style="width: 250px;">
                                <span class="input-group-text bg-white border-end-0"><i
                                        class="fas fa-search text-muted"></i></span>
                                <input type="text" id="searchDocID" class="form-control border-start-0"
                                    placeholder="ค้นหา..." onkeyup="filterTable()">
                            </div>
                            <select id="statusFilter" class="form-select border-light bg-light" style="width: 150px;"
                                onchange="filterTable()">
                                <option value="">สถานะทั้งหมด</option>
                                <option value="ยังไม่ส่งสรุป">ยังไม่ส่งสรุป</option>
                                <option value="รอสรุป">รอสรุป</option>
                                <option value="ล่าช้า">ล่าช้า</option>
                            </select>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table id="recordTable" class="table table-custom table-striped table-hover mb-0">
                            <thead>
                                <tr>
                                    <th class="text-center">เลขที่เอกสาร</th>
                                    <th class="text-center">ชื่อผู้ป่วย</th>
                                    <th class="text-center">วันที่รับเข้า</th>
                                    <th class="text-center">กลุ่มงาน</th>
                                    <th class="text-center">แผนกหอผู้ป่วย</th>
                                    <th class="text-center">แพทย์เจ้าของไข้</th>
                                    <th class="text-center">สถานะ</th>
                                    <th class="text-center">หมายเหตุ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $records = include __DIR__ . '/db/db-record-select.php';
                                if (!empty($records)): 
                                    
                                    foreach ($records as $row): 
                                        
                                        // Skip 'เสร็จสิ้น'
                                        if($row['status_name'] == 'เสร็จสิ้น') continue;

                                        $status = $row['status_name'];
                                        $badgeClass = 'bg-secondary';

                                        if($status == 'ล่าช้า') $badgeClass = 'bg-danger badge-pulse';
                                        elseif($status == 'รอสรุป') $badgeClass = 'bg-warning text-dark';
                                ?>
                                <tr>
                                    <td class="text-center">
                                        <a href="document-detail.php?doc_id=<?= urlencode($row['id']) ?>"
                                            class="btn btn-sm btn-outline-primary fw-bold rounded-pill px-3">
                                            <?= htmlspecialchars($row['id']) ?>
                                        </a>
                                    </td>
                                    <td class="fw-bold"><?= htmlspecialchars($row['customer_name']) ?></td>
                                    <td class="text-center text-muted">
                                        <?= htmlspecialchars(date("d/m/Y", strtotime($row['create_at']))) ?>
                                    </td>
                                    <td><?= htmlspecialchars($row['workgroup_name']) ?></td>
                                    <td><?= htmlspecialchars($row['department_name']) ?></td>
                                    <td><?= htmlspecialchars($row['staff_name']) ?></td>
                                    <td class="text-center">
                                        <span class="badge <?= $badgeClass ?> rounded-pill px-3 py-2 fw-normal">
                                            <?= htmlspecialchars($status) ?>
                                        </span>
                                    </td>
                                    <td class="text-muted small"><?= htmlspecialchars($row['remark']) ?></td>
                                </tr>
                                <?php endforeach; ?>
                                <?php else: ?>
                                <tr>
                                    <td colspan="8" class="text-center py-5 text-muted">ไม่พบข้อมูลเอกสาร</td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="lib/chart/chart.min.js"></script>
    <script src="lib/easing/easing.min.js"></script>
    <script src="lib/waypoints/waypoints.min.js"></script>
    <script src="lib/owlcarousel/owl.carousel.min.js"></script>
    <script src="lib/tempusdominus/js/moment.min.js"></script>
    <script src="lib/tempusdominus/js/moment-timezone.min.js"></script>
    <script src="lib/tempusdominus/js/tempusdominus-bootstrap-4.min.js"></script>
    <script src="js/main.js"></script>

    <script>
    function filterTable() {
    var input, filter, table, tr, td, i;
    var statusFilter = document.getElementById("statusFilter").value; // ดึงค่าจาก Select
    input = document.getElementById("searchDocID");
    filter = input.value.toUpperCase(); // ดึงค่าจากช่องค้นหา
    table = document.getElementById("recordTable");
    tr = table.getElementsByTagName("tr");

    // วนลูปเริ่มจาก i=1 เพื่อข้ามหัวตาราง
    for (i = 1; i < tr.length; i++) {
        var showRow = true; // ตั้งต้นให้แสดงผลไว้ก่อน
        
        // ดึงข้อมูลแต่ละคอลัมน์ (Index เริ่มจาก 0)
        var tdID = tr[i].getElementsByTagName("td")[0];      // เลขที่เอกสาร
        var tdName = tr[i].getElementsByTagName("td")[1];    // ชื่อผู้ป่วย
        var tdDr = tr[i].getElementsByTagName("td")[5];      // แพทย์เจ้าของไข้
        var tdStatus = tr[i].getElementsByTagName("td")[6];  // สถานะ (คอลัมน์ที่ 7)

        // 1. ตรวจสอบการค้นหาด้วยข้อความ (Search Box)
        if (filter !== "") {
            var txtID = tdID ? tdID.textContent || tdID.innerText : "";
            var txtName = tdName ? tdName.textContent || tdName.innerText : "";
            var txtDr = tdDr ? tdDr.textContent || tdDr.innerText : "";
            
            if (txtID.toUpperCase().indexOf(filter) === -1 && 
                txtName.toUpperCase().indexOf(filter) === -1 && 
                txtDr.toUpperCase().indexOf(filter) === -1) {
                showRow = false; // ถ้าไม่เจอคำที่ค้นหาในทั้ง 3 คอลัมน์ ให้ซ่อน
            }
        }

        // 2. ตรวจสอบการกรองด้วยสถานะ (Dropdown)
        if (showRow && statusFilter !== "") {
            var txtStatus = tdStatus ? tdStatus.textContent.trim() : "";
            if (txtStatus !== statusFilter) {
                showRow = false; // ถ้าสถานะไม่ตรงกับที่เลือก ให้ซ่อน
            }
        }

        // สั่งแสดงผลหรือซ่อนแถว
        tr[i].style.display = showRow ? "" : "none";
    }
}
    </script>
</body>

</html>