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

include __DIR__ . '/auth.php';

$isLogin          = isset($_SESSION['user_id']);
$department_ID    = $isLogin ? $_SESSION['department_id'] : '-';
$workgroups_ID    = $isLogin ? $_SESSION['workgroup_id'] : '-';    
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>ระบบติดตามงานเอกสารโรงพยาบาล</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <?php include __DIR__ . '/css-link-library.php'; ?>
    
    <style>
        /* --- Deep Blue Theme --- */
        :root {
            --primary-deep: #003366;
            --primary-main: #0056b3;
            --accent-light: #e7f1ff;
        }

        body { 
            background-color: #f0f4f8; 
            font-family: 'Sarabun', sans-serif; 
        }

        /* Page Header Style */
        .page-header {
            background: linear-gradient(135deg, #001f3f 0%, #004085 100%);
            color: white;
            padding: 2rem;
            border-radius: 0 0 20px 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }
        
        .page-header h2 {
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
        }

        /* Card Box Style */
        .card-box {
            background: white;
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 51, 102, 0.08);
            padding: 2rem;
            margin-bottom: 2rem;
            transition: transform 0.2s;
        }
        
        /* Form Styles */
        .form-header {
            border-left: 5px solid var(--primary-deep);
            padding-left: 15px;
            color: var(--primary-deep);
            margin-bottom: 20px;
        }

        .input-group-text {
            background-color: #f8f9fa;
            color: #495057;
            border-color: #dee2e6;
            min-width: 140px; /* จัดความกว้าง Label ให้เท่ากัน */
            justify-content: center;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-main);
            box-shadow: 0 0 0 0.25rem rgba(0, 86, 179, 0.25);
        }

        /* Button Style */
        .btn-deep-blue {
            background-color: var(--primary-deep);
            border-color: var(--primary-deep);
            color: white;
            padding: 10px 30px;
            font-weight: 500;
            border-radius: 8px;
            transition: all 0.3s;
        }
        
        .btn-deep-blue:hover {
            background-color: #002244;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }

        /* Table Style */
        .table-custom thead {
            background-color: var(--primary-deep);
            color: white;
        }
        .table-custom th {
            font-weight: 500;
            border: none;
        }
        .table-hover tbody tr:hover {
            background-color: var(--accent-light);
        }
        
        /* Link Style in Table */
        .doc-link {
            color: var(--primary-main);
            font-weight: bold;
            text-decoration: none;
        }
        .doc-link:hover {
            text-decoration: underline;
            color: var(--primary-deep);
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
                        <h2 class="mb-1 fw-bold text-white"><i class="fas fa-file-import me-2" style="text-shadow: 0 2px 4px rgba(0,0,0,0.5);"></i>ลงทะเบียนเอกสารใหม่</h2>
                        <p class="mb-0 opacity-75">บันทึกข้อมูลนำเข้าเอกสารเพื่อเริ่มกระบวนการติดตาม</p>
                    </div>
                </div>
            </div>

            <?php if (isset($_GET['msg'])): ?>
            <div class="container mt-n4 mb-4" style="position: relative; z-index: 10;">
                <div class="alert alert-<?= htmlspecialchars($_GET['type'] ?? 'info') ?> alert-dismissible fade show shadow-sm border-0"
                    role="alert">
                    <i class="fas fa-info-circle me-2"></i> <?= htmlspecialchars($_GET['msg']) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
            <script>
            setTimeout(() => document.querySelector('.alert')?.remove(), 10000);
            </script>
            <?php endif; ?>
            <div class="container-fluid px-4">
                <div class="row justify-content-center">
                    <div class="col-xl-9">
                        <div class="card-box">
                            <form action="/db/db-document-insert.php" method="POST">

                                <h4 class="form-header fw-bold">ข้อมูลเบื้องต้น</h4>
                                
                                <div class="row g-3 mb-3">
                                    <div class="col-md-6">
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-barcode me-2"></i>เลขที่เอกสาร</span>
                                            <input type="text" class="form-control" name="doc_ID" placeholder="ระบุ AN/HN" autofocus required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-calendar-alt me-2"></i>วันที่รับเข้า</span>
                                            <input type="date" class="form-control" name="dateCreate" id="dateCreate"
                                                value="<?= date('Y-m-d') ?>">
                                        </div>
                                    </div>
                                </div>

                                <div class="input-group mb-3">
                                    <span class="input-group-text"><i class="fas fa-user-injured me-2"></i>ชื่อผู้ป่วย</span>
                                    <input type="text" class="form-control" name="customer_name" placeholder="ชื่อ-นามสกุล ผู้ป่วย" required>
                                </div>

                                <div class="input-group mb-3">
                                    <span class="input-group-text"><i class="fas fa-hospital-user me-2"></i>กลุ่มงาน</span>
                                    <?php $workgroups = include __DIR__ . '/db/db-workgroup-select.php'; ?>
                                    <select class="form-select" name="workgroupID" id="workgroupSelect" required>
                                        <option value="" disabled selected>-- เลือกกลุ่มงาน --</option>
                                        <?php foreach ($workgroups as $wg): ?>
                                        <option value="<?= htmlspecialchars($wg['id']) ?>">
                                            <?= htmlspecialchars($wg['name']) ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="input-group mb-3">
                                    <span class="input-group-text"><i class="fas fa-procedures me-2"></i>หอผู้ป่วย</span>
                                    <select id="departmentSelect" name="department_id" class="form-select" required>
                                        <option value="" disabled selected>-- เลือกหอผู้ป่วย --</option>
                                    </select>

                                    <script>
                                    document.getElementById('workgroupSelect').addEventListener('change', async function() {
                                        const workgroupId = this.value;
                                        const departmentSelect = document.getElementById('departmentSelect');

                                        departmentSelect.innerHTML = '<option>กำลังโหลด...</option>';
                                        departmentSelect.disabled = true;

                                        try {
                                            const response = await fetch('db/db-departments-by-workgroup.php', {
                                                method: 'POST',
                                                headers: {
                                                    'Content-Type': 'application/x-www-form-urlencoded'
                                                },
                                                body: `workgroup_id=${workgroupId}`
                                            });
                                            const data = await response.json();

                                            departmentSelect.innerHTML =
                                                '<option value="" disabled selected>-- เลือกหอผู้ป่วย --</option>';

                                            data.forEach(item => {
                                                const opt = document.createElement('option');
                                                opt.value = item.id;
                                                opt.textContent = item.name;
                                                departmentSelect.appendChild(opt);
                                            });
                                        } catch (error) {
                                            departmentSelect.innerHTML = '<option>ไม่มีข้อมูล</option>';
                                            console.error(error);
                                        } finally {
                                            departmentSelect.disabled = false;
                                        }
                                    });
                                    </script>
                                </div>

                                <div class="input-group mb-3">
                                    <span class="input-group-text"><i class="fas fa-user-md me-2"></i>แพทย์เจ้าของไข้</span>
                                    <select class="form-select" name="staff_id" id="staff_DropDownAuditor" required>
                                        <option value="">- กรุณาเลือกหอผู้ป่วยก่อน -</option>
                                    </select>
                                </div>

                                <script>
                                document.addEventListener("DOMContentLoaded", function() {
                                    document.getElementById('departmentSelect').addEventListener('change', function() {
                                        const deptId = this.value;
                                        const dropdowns = [
                                            //document.getElementById('resident_DropDownAuditor'),
                                            document.getElementById('staff_DropDownAuditor'),
                                            //document.getElementById('auditor_DropDownAuditor')
                                        ];

                                        // แสดงสถานะกำลังโหลด
                                        dropdowns.forEach(dropdown => {
                                            dropdown.innerHTML = '<option>กำลังโหลด...</option>';
                                            dropdown.disabled = true;
                                        });

                                        // ส่ง request แบบ POST
                                        fetch('db/db-auditor-by-department.php', {
                                                method: 'POST',
                                                headers: {
                                                    'Content-Type': 'application/x-www-form-urlencoded'
                                                },
                                                body: `dept_id=${deptId}`
                                            })
                                            .then(response => response.json())
                                            .then(data => {
                                                dropdowns.forEach(dropdown => {
                                                    dropdown.innerHTML =
                                                        '<option value="">- เลือกแพทย์ -</option>';
                                                    data.forEach(item => {
                                                        const opt = document.createElement(
                                                            'option');
                                                        opt.value = item.auditor_id;
                                                        opt.textContent = item.auditor_name;
                                                        dropdown.appendChild(opt);
                                                    });
                                                    dropdown.disabled = false;
                                                });
                                            })
                                            .catch(error => {
                                                console.error('Error fetching auditors:', error);
                                                dropdowns.forEach(dropdown => {
                                                    dropdown.innerHTML =
                                                        '<option>ไม่มีข้อมูล</option>';
                                                    dropdown.disabled = false;
                                                });
                                            });
                                    });
                                });
                                </script>

                                <div class="mb-4">
                                    <label class="form-label fw-bold text-secondary">หมายเหตุ</label>
                                    <textarea class="form-control" rows="3" name="remark" placeholder="ระบุรายละเอียดเพิ่มเติม (ถ้ามี)"></textarea>
                                </div>

                                <div class="d-flex justify-content-center">
                                    <button type="submit" class="btn btn-deep-blue btn-lg shadow">
                                        <i class="fas fa-save me-2"></i> บันทึกข้อมูลเข้าระบบ
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="container-fluid px-4 pb-5">
                <div class="card-box">

                    <div class="d-flex justify-content-between align-items-center mb-3">
                         <h4 class="form-header fw-bold mb-0">รายการเอกสารที่ลงทะเบียน (วันนี้)</h4>
                         <span class="badge bg-primary rounded-pill"><?= date('d/m/Y') ?></span>
                    </div>
                   
                    <div class="table-responsive">
                        <table id="recordTable" class="table table-custom table-hover table-bordered align-middle">
                            <thead class="text-center">
                                <tr>
                                    <th>เลขที่เอกสาร</th>
                                    <th>ชื่อผู้ป่วย</th>
                                    <th>วันที่รับเข้า</th>
                                    <th>กลุ่มงาน</th>
                                    <th>แผนกหอผู้ป่วย</th>
                                    <th>แพทย์เจ้าของไข้</th> 
                                    <th>สถานะ</th>
                                    <th>หมายเหตุ</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php 
                                $records = include __DIR__ . '/db/db-record-select.php';
                                
                                // 1. กำหนดวันที่ปัจจุบัน (Format: ปี-เดือน-วัน)
                                $today = date('Y-m-d'); 
                                $hasData = false; 

                                if (!empty($records)): 
                                    foreach ($records as $row): 
                                        $rowDate = date('Y-m-d', strtotime($row['create_at']));
                                        if ($rowDate !== $today) {
                                            continue;
                                        }
                                        $hasData = true; 
                                ?>
                                <tr>
                                    <td class="text-center">
                                        <a href="document-detail.php?doc_id=<?= urlencode($row['id']) ?>" class="doc-link">
                                            <i class="fas fa-file-alt me-1"></i> <?= htmlspecialchars($row['id']) ?>
                                        </a>
                                    </td>
                                    <td><?= htmlspecialchars($row['customer_name']) ?></td>
                                    <td class="text-center"><?= htmlspecialchars(date("d/m/Y", strtotime($row['create_at']))) ?></td>
                                    
                                    <td class="text-center"><?= htmlspecialchars($row['workgroup_name']) ?></td>
                                    <td class="text-center"><?= htmlspecialchars($row['department_name']) ?></td>

                                    <td class="text-center"><?= htmlspecialchars($row['staff_name']) ?></td>
                                    <td class="text-center">
                                        <?php 
                                            $status = $row['status_name'];
                                            $badgeColor = 'badge bg-secondary text-white rounded-pill';
                                            if($status == 'เสร็จสิ้น') $badgeColor = 'badge rounded-pill text-success';
                                            elseif($status == 'ล่าช้า') $badgeColor = 'badge rounded-pill text-danger';
                                            elseif($status == 'รอสรุป') $badgeColor = 'badge rounded-pill text-warning';
                                            elseif($status == 'ยังไม่ส่งสรุป') $badgeColor = 'badge rounded-pill text-secondary';
                                        ?>
                                        <span class="<?= $badgeColor ?>"><?= htmlspecialchars($status) ?></span>
                                    </td>
                                    <td class="text-center text-muted small"><?= htmlspecialchars($row['remark']) ?></td>
                                </tr>
                                <?php endforeach; ?>
                                
                                <?php if (!$hasData): ?>
                                    <tr>
                                        <td colspan="8" class="text-center py-4 text-muted">
                                            <i class="fas fa-folder-open fa-2x mb-2 d-block opacity-25"></i>
                                            ไม่พบรายการเอกสารของวันนี้
                                        </td>
                                    </tr>
                                <?php endif; ?>

                                <?php else: ?>
                                <tr>
                                    <td colspan="8" class="text-center py-4 text-muted">ไม่พบข้อมูลในระบบ</td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            </div>
    </div>
</body>

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

</html>