<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// ป้องกันไม่ให้แสดง cache ของหน้านี้
header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
header("Pragma: no-cache"); // HTTP 1.0.
header("Expires: 0"); // Proxies.

$name = $_SESSION['name'];
include __DIR__ . '/auth.php'; // ตรวจสอบ auth 
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>ระบบติดตามงานเอกสารโรงพยาบาล</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <?php include __DIR__ . '/css-link-library.php'; ?>

    <style>
    /* --- Deep Blue (Navy) Theme --- */
    :root {
        --primary-navy: #001f3f;
        --primary-blue: #003366;
        --accent-light: #e7f1ff;
        --text-dark: #2c3e50;
    }

    body {
        background-color: #f0f4f8;
        font-family: 'Sarabun', sans-serif;
    }

    /* Page Header Style */
    .page-header {
        background: linear-gradient(135deg, var(--primary-navy) 0%, var(--primary-blue) 100%);
        color: white;
        padding: 2.5rem;
        border-radius: 0 0 25px 25px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        margin-bottom: 35px;
    }

    /* Card Container */
    .card-custom {
        background: white;
        border-radius: 12px;
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.05);
        border: none;
        overflow: hidden;
    }

    /* Table Styling */
    .table-head-navy {
        background-color: var(--primary-navy);
        color: white;
    }

    /* --- Requirement: ห้ามตัดคำ (Nowrap) --- */
    .table-nowrap th,
    .table-nowrap td {
        white-space: nowrap;
        /* บังคับข้อความให้อยู่บรรทัดเดียว */
        vertical-align: middle;
    }

    .table-hover tbody tr:hover {
        background-color: var(--accent-light);
    }

    /* Input Styling */
    .form-control:focus,
    .form-select:focus {
        border-color: var(--primary-blue);
        box-shadow: 0 0 0 0.25rem rgba(0, 51, 102, 0.25);
    }
    </style>
</head>

<body class="d-flex flex-column min-vh-100">
    <div class="container-fluid flex-fill position-relative d-flex p-0">
        <?php include __DIR__ . '/menu-bar/sidebar.php'; ?>

        <div class="content w-100">
            <?php include __DIR__ . '/menu-bar/navbar.php'; ?>

            <div class="container-fluid pt-0 px-0">
                <div class="page-header d-flex align-items-center justify-content-center">
                    <div class="text-center">
                        <h2 class="mb-2 fw-bold text-white" style="text-shadow: 0 2px 4px rgba(0,0,0,0.5);">
                            <i class="fas fa-file-medical-alt me-2"></i>รายการเอกสารทั้งหมด</h3>
                            <!-- <p class="mb-0 opacity-75"></p> -->
                    </div>
                </div>
            </div>

            <div class="container-fluid px-4 pb-5">
                <div class="card-custom p-4">

                    <div class="d-flex flex-wrap gap-2 mb-4">
                        <div class="input-group" style="max-width: 400px; min-width: 300px;">
                            <span class="input-group-text bg-white border-end-0">
                                <i class="fas fa-search text-muted"></i>
                            </span>
                            <input type="text" id="searchDocID" class="form-control border-start-0"
                                placeholder="ค้นหา เลขที่ / Auditor / ผู้ป่วย" onkeyup="filterTable()">
                        </div>

                        <select id="statusFilter" class="form-select" style="width: auto; min-width: 200px;"
                            onchange="filterTable()">
                            <option value="">ทั้งหมด (สถานะ)</option>
                            <option value="ยังไม่ส่งสรุป">ยังไม่ส่งสรุป</option>
                            <option value="รอสรุป">รอสรุป</option>
                            <option value="ล่าช้า">ล่าช้า</option>
                            <option value="เสร็จสิ้น">เสร็จสิ้น</option>
                        </select>
                    </div>

                    <div class="table-responsive">
                        <table id="recordTable"
                            class="table table-striped table-hover table-bordered table-nowrap mb-0">
                            <thead class="table-head-navy text-center">
                                <tr>
                                    <th class="text-center">เลขที่เอกสาร</th>
                                    <th class="text-center">ชื่อผู้ป่วย</th>
                                    <th class="text-center">วันที่รับเข้า</th>
                                    <th class="text-center">กลุ่มงาน</th>
                                    <th class="text-center">แผนกหอผู้ป่วย</th>
                                    <th class="text-center">แพทย์เจ้าของไข้</th>
                                    <th class="text-center">สถานะเอกสาร</th>
                                    <th class="text-center">หมายเหตุ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $records = include __DIR__ . '/db/db-record-select.php';
                                if (!empty($records)): 
                                    foreach ($records as $row): 
                                        $status = $row['status_name'];
                                        $badgeClass = 'bg-secondary';
                                        if($status == 'เสร็จสิ้น') $badgeClass = 'bg-success';
                                        elseif($status == 'ล่าช้า') $badgeClass = 'bg-danger';
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
                                    <td colspan="7" class="text-center py-5 text-muted">ไม่พบข้อมูลเอกสาร</td>
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
    // ฟังก์ชันกรองตาราง (Search + Dropdown Filter)
    function filterTable() {
        // 1. รับค่าจากช่องค้นหา (Text)
        var searchInput = document.getElementById("searchDocID").value.toUpperCase();

        // 2. รับค่าจาก Dropdown (Status)
        var statusInput = document.getElementById("statusFilter").value.toUpperCase();

        var table = document.getElementById("recordTable");
        var tr = table.getElementsByTagName("tr");

        // วนลูปทุกแถว (เริ่มที่ 1 เพื่อข้าม Header)
        for (var i = 1; i < tr.length; i++) {

            // --- ส่วนที่ 1: ตรวจสอบ Search Text ---
            // ค้นหาจาก: document_id(0), customer_name(1), Auditor(5)
            var tdDocID = tr[i].getElementsByTagName("td")[0];
            var tdName = tr[i].getElementsByTagName("td")[1];
            var tdAuditor = tr[i].getElementsByTagName("td")[5]; // Auditor อยู่ index 5

            var textMatch = false;
            // เช็คว่ามีคำค้นหาในคอลัมน์เหล่านี้หรือไม่
            if (tdDocID && tdDocID.textContent.toUpperCase().indexOf(searchInput) > -1) textMatch = true;
            if (tdName && tdName.textContent.toUpperCase().indexOf(searchInput) > -1) textMatch = true;
            if (tdAuditor && tdAuditor.textContent.toUpperCase().indexOf(searchInput) > -1) textMatch = true;

            // --- ส่วนที่ 2: ตรวจสอบ Dropdown Status ---
            var tdStatus = tr[i].getElementsByTagName("td")[6]; // Status อยู่ index 6
            var statusMatch = false;

            if (statusInput === "") {
                // ถ้าเลือก "ทั้งหมด" ให้ถือว่าผ่าน
                statusMatch = true;
            } else {
                // ถ้าเลือกสถานะ ให้เช็คว่าตรงกับในตารางไหม
                if (tdStatus && tdStatus.textContent.toUpperCase().indexOf(statusInput) > -1) {
                    statusMatch = true;
                }
            }

            // --- สรุปผล: ต้องผ่านทั้ง Search และ Status ---
            if (textMatch && statusMatch) {
                tr[i].style.display = "";
            } else {
                tr[i].style.display = "none";
            }
        }
    }
    </script>

</body>

</html>