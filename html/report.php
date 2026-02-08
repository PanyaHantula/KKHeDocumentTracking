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
include __DIR__ . '/service/getsummary.php'; 

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
    .card-total .stat-icon {
        background-color: rgba(40, 81, 166, 0.1);
        color: var(--color-total);
    }

    .card-total .stat-value {
        color: var(--color-total);
    }

    .card-total::after {
        background: var(--color-total);
    }

    .card-wait .stat-icon {
        background-color: rgba(108, 117, 125, 0.1);
        color: var(--color-wait);
    }

    .card-wait .stat-value {
        color: var(--color-wait);
    }

    .card-wait::after {
        background: var(--color-wait);
    }

    .card-process .stat-icon {
        background-color: rgba(255, 193, 7, 0.1);
        color: #d39e00;
    }

    .card-process .stat-value {
        color: #d39e00;
    }

    .card-process::after {
        background: #FFC107;
    }

    .card-overdue .stat-icon {
        background-color: rgba(220, 53, 69, 0.1);
        color: var(--color-overdue);
    }

    .card-overdue .stat-value {
        color: var(--color-overdue);
    }

    .card-overdue::after {
        background: var(--color-overdue);
    }

    .card-complete .stat-icon {
        background-color: rgba(25, 135, 84, 0.1);
        color: var(--color-complete);
    }

    .card-complete .stat-value {
        color: var(--color-complete);
    }

    .card-complete::after {
        background: var(--color-complete);
    }


    /* --- Table Section --- */
    .table-container {
        background: white;
        border-radius: 16px;
        box-shadow: var(--card-shadow);
        padding: 1.5rem;
        margin-top: 2rem;
        overflow: hidden;
        /* เพื่อให้มุมโค้งแสดงผลถูกต้อง */
    }

    /* CSS สำหรับตารางที่แก้ไข */
    .table-custom th,
    .table-custom td {
        white-space: nowrap;
        /* สำคัญ: บังคับข้อความให้อยู่บรรทัดเดียว ไม่ตัดคำ */
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

    /* --- Modern Export Button --- */
    .btn-export-excel {
        background-color: #198754; /* สีเขียวมาตรฐาน Excel */
        color: white;
        border: none;
        border-radius: 10px;
        padding: 0.6rem 1.2rem;
        font-weight: 600;
        transition: all 0.3s ease;
        box-shadow: 0 4px 6px rgba(25, 135, 84, 0.2);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .btn-export-excel:hover {
        background-color: #146c43;
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(25, 135, 84, 0.3);
    }

    .btn-export-excel:active {
        transform: translateY(0);
    }

    /* ปรับระยะห่างของกลุ่มเครื่องมือ */
    .toolbar-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 15px;
        margin-bottom: 20px;
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
                        <h2>รายการเอกสาร</h2>
                    </div>
                </div>

                <div class="table-container">
                    <div class="toolbar-container">
                        <h5 class="mb-0 fw-bold text-primary-navy">
                            <i class="fas fa-list me-2"></i>รายชื่อเอกสาร
                        </h5>
                        
                        <div class="d-flex flex-wrap gap-2 align-items-center">
                            <div class="input-group" style="width: 250px;">
                                <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                                <input type="text" id="searchDocID" class="form-control border-start-0"
                                    placeholder="ค้นหาชื่อ/เลขที่..." onkeyup="filterTable()">
                            </div>
                            
                            <select id="statusFilter" class="form-select border-light bg-light" style="width: 160px;"
                                onchange="filterTable()">
                                <option value="">สถานะทั้งหมด</option>
                                <option value="ยังไม่ส่งสรุป">ยังไม่ส่งสรุป</option>
                                <option value="รอสรุป">รอสรุป</option>
                                <option value="ล่าช้า">ล่าช้า</option>
                                <option value="เสร็จสิ้น">เสร็จสิ้น</option>
                            </select>

                            <button onclick="exportTableToExcel()" class="btn-export-excel">
                                <i class="fas fa-file-excel"></i>
                                <span>ส่งออก Excel</span>
                            </button>
                        </div>
                    </div>
                    
                    <div class="table-responsive">
                        <table id="recordTable" class="table table-custom table-striped table-hover mb-0">
                            <thead>
                                <tr>
                                    <th class="text-center">เลขที่เอกสาร</th>
                                    <th class="text-center">สถานะเอกสาร</th>
                                    <th class="text-center">แพทย์ resident</th>
                                    <th class="text-center">วันที่ส่งสรุป</th>
                                    <th class="text-center">รับคืน</th>
                                    <th class="text-center">สถานะ</th>
                                    <th class="text-center">แพทย์เจ้าของไข้</th>
                                    <th class="text-center">วันที่ส่งสรุป</th>
                                    <th class="text-center">รับคืน</th>
                                    <th class="text-center">สถานะ</th>
                                    <th class="text-center">แพทย์ auditor</th>
                                    <th class="text-center">วันที่ส่งสรุป</th>
                                    <th class="text-center">รับคืน</th>
                                    <th class="text-center">สถานะ</th>
                                    <th class="text-center">หมายเหตุ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                    $records = include __DIR__ . '/db/db-record-select.php';
                                    
                                    if (is_array($records)):
                                        foreach ($records as $row): 
                                            // if (($row['doc_status'] ?? 0) == 4) continue;

                                            $status = $row['status_name'] ?? '-';
                                            $docStatusId = $row['doc_status'] ?? 0;
                                            $badgeClass = 'bg-secondary';

                                            if ($docStatusId == 4) {
                                                $badgeClass = 'bg-success badge-pulse';
                                            }elseif ($docStatusId == 3) {
                                                $badgeClass = 'bg-danger badge-pulse';
                                            } elseif ($docStatusId == 2) {
                                                $badgeClass = 'bg-warning text-dark';
                                            } elseif ($docStatusId == 1) {
                                                $badgeClass = 'bg-secondary text-white';
                                            }
                                ?>
                                <tr>
                                    <td class="text-center">
                                        <a href="document-detail.php?doc_id=<?= urlencode($row['id'] ?? '') ?>" 
                                        class="btn btn-sm btn-outline-primary fw-bold rounded-pill px-3">
                                            <?= htmlspecialchars($row['id'] ?? '-') ?>
                                        </a>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge <?= $badgeClass ?> rounded-pill px-3 py-2 fw-normal"><?= htmlspecialchars($status) ?></span>
                                    </td>

                                    <td><?= htmlspecialchars($row['resident_name'] ?? '-') ?></td>
                                    <td class="text-center"><?= !empty($row['resident_create_at']) ? date("d/m/Y", strtotime($row['resident_create_at'])) : '-' ?></td>
                                    <td class="text-center"><?= !empty($row['resident_complete_at']) ? date("d/m/Y", strtotime($row['resident_complete_at'])) : '-' ?></td>
                                    <?php 
                                        $status = $row['resident_status'] ?? '-';
                                        $tdClass = 'text-center';
                                        if ($status == "ล่าช้า") {
                                            $tdClass = 'text-center text-danger';
                                        }elseif ($status == "รอสรุป") {
                                            $tdClass = 'text-center text-warning';
                                        }elseif ($status == "เสร็จสิ้น") {
                                            $tdClass = 'text-center text-success';
                                        } 
                                    
                                    ?>
                                    <td class="<?= $tdClass ?> "><?= htmlspecialchars($row['resident_status'] ?? '-') ?></td>


                                    <td><?= htmlspecialchars($row['staff_name'] ?? '-') ?></td>
                                    <td class="text-center"><?= !empty($row['staff_create_at']) ? date("d/m/Y", strtotime($row['staff_create_at'])) : '-' ?></td>
                                    <td class="text-center"><?= !empty($row['staff_complete_at']) ? date("d/m/Y", strtotime($row['staff_complete_at'])) : '-' ?></td>
                                    <?php 
                                        $status = $row['staff_status'] ?? '-';
                                        $tdClass = 'text-center';
                                        if ($status == "ล่าช้า") {
                                            $tdClass = 'text-center text-danger';
                                        }elseif ($status == "รอสรุป") {
                                            $tdClass = 'text-center text-warning';
                                        }elseif ($status == "เสร็จสิ้น") {
                                            $tdClass = 'text-center text-success';
                                        } 
                                        
                                    ?>
                                    <td class="<?= $tdClass ?> "><?= htmlspecialchars($row['staff_status'] ?? '-') ?></td>


                                    <td><?= htmlspecialchars($row['auditor_name'] ?? '-') ?></td>
                                    <td class="text-center"><?= !empty($row['auditor_create_at']) ? date("d/m/Y", strtotime($row['auditor_create_at'])) : '-' ?></td>
                                        <td class="text-center"><?= !empty($row['auditor_complete_at']) ? date("d/m/Y", strtotime($row['auditor_complete_at'])) : '-' ?></td>
                                    <?php 
                                            $status = $row['auditor_status'] ?? '-';
                                            $tdClass = 'text-center';
                                            if ($status == "ล่าช้า") {
                                                $tdClass = 'text-center text-danger';
                                            }elseif ($status == "รอสรุป") {
                                                $tdClass = 'text-center text-warning';
                                            }elseif ($status == "เสร็จสิ้น") {
                                                $tdClass = 'text-center text-success';
                                            }    
                                        ?>
                                    <td class="<?= $tdClass ?> "><?= htmlspecialchars($row['auditor_status'] ?? '-') ?></td>

                                    <td class="small"><?= htmlspecialchars($row['remark'] ?? '-') ?></td>
                                </tr>
                                <?php 
                                        endforeach; // ปิด foreach
                                    endif; // ปิด if
                                ?>
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
        var input = document.getElementById("searchDocID");
        var filter = input.value.toUpperCase();
        var statusFilter = document.getElementById("statusFilter").value;
        var table = document.getElementById("recordTable");
        var tr = table.getElementsByTagName("tr");

        for (var i = 1; i < tr.length; i++) {
            var showRow = true;
            
            // ดึงคอลัมน์ที่ต้องการค้นหา
            var tdID = tr[i].getElementsByTagName("td")[0];     // เลขที่เอกสาร
            var tdStatus = tr[i].getElementsByTagName("td")[1]; // สถานะภาพรวม (Badge)
            var tdResident = tr[i].getElementsByTagName("td")[2]; // แพทย์ Resident
            var tdStaff = tr[i].getElementsByTagName("td")[6];    // แพทย์เจ้าของไข้

            // 1. ตรวจสอบการค้นหา (Search)
            var txtContent = (tdID ? tdID.textContent : "") + 
                            (tdResident ? tdResident.textContent : "") + 
                            (tdStaff ? tdStaff.textContent : "");
            
            if (filter !== "" && txtContent.toUpperCase().indexOf(filter) === -1) {
                showRow = false;
            }

            // 2. ตรวจสอบการกรองสถานะ (Dropdown)
            if (showRow && statusFilter !== "") {
                var txtStatus = tdStatus ? tdStatus.textContent.trim() : "";
                if (txtStatus !== statusFilter) {
                    showRow = false;
                }
            }

            tr[i].style.display = showRow ? "" : "none";
        }
    }

    function exportTableToExcel() {
        var table = document.getElementById("recordTable");
        var ws = XLSX.utils.aoa_to_sheet([]); // สร้างแผ่นงานว่าง
        var data = [];

        // ดึงหัวตาราง
        var header = [];
        var headers = table.querySelectorAll("thead th");
        headers.forEach(th => header.push(th.innerText));
        data.push(header);

        // ดึงเฉพาะแถวที่ไม่ได้ถูกซ่อน (display != 'none')
        var rows = table.querySelectorAll("tbody tr");
        rows.forEach(row => {
            if (row.style.display !== "none") {
                var rowData = [];
                row.querySelectorAll("td").forEach(td => rowData.push(td.innerText));
                data.push(rowData);
            }
        });

        // สร้างไฟล์
        var ws = XLSX.utils.aoa_to_sheet(data);
        var wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, "รายการเอกสาร");
        XLSX.writeFile(wb, "รายงานเอกสาร_กรองแล้ว.xlsx");
    }
    </script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

</body>

</html>