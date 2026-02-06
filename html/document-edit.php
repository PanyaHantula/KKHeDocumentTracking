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

include __DIR__ . '/auth.php'; // ตรวจสอบ session
include __DIR__ . '/db/db-document-edit.php'; // ต้องมั่นใจว่าไฟล์นี้ return $record, $workgroups, $departments, $auditors มาครบถ้วน

$isLogin = isset($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="utf-8">
    <title>แก้ไขเอกสาร</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include __DIR__ . '/css-link-library.php'; ?>

    <style>
        /* --- Deep Blue (Navy) Theme --- */
        :root {
            --primary-navy: #001f3f;
            --primary-blue: #003366;
            --accent-light: #f0f4f8;
            --text-dark: #2c3e50;
        }

        body {
            background-color: var(--accent-light);
            font-family: 'Sarabun', sans-serif;
        }

        /* Page Header Style */
        .page-header-navy {
            background: linear-gradient(135deg, var(--primary-navy) 0%, var(--primary-blue) 100%);
            color: white;
            padding: 2rem;
            border-radius: 0 0 20px 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }

        /* Modern Card Style */
        .card-custom {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 51, 102, 0.08);
            border: none;
        }

        /* Button Styles */
        .btn-navy {
            background-color: var(--primary-blue);
            color: white;
            border: none;
        }

        .btn-navy:hover {
            background-color: var(--primary-navy);
            color: white;
        }
    </style>
</head>

<body class="d-flex flex-column min-vh-100">
    <div class="container-fluid flex-fill position-relative d-flex p-0">
        <?php include __DIR__ . '/menu-bar/sidebar.php'; ?>

        <div class="content w-100">
            <?php include __DIR__ . '/menu-bar/navbar.php'; ?>

            <div class="container-fluid pt-0 px-0">
                <div class="page-header-navy d-flex align-items-center justify-content-center">
                    <div class="text-center">
                        <h2 class="mb-1 fw-bold text-white" style="text-shadow: 0 2px 4px rgba(0,0,0,0.5);">
                            แก้ไขเอกสาร
                        </h2>
                    </div>
                </div>
            </div>

            <div class="container-fluid px-4 pb-5">
                <div class="card-custom p-4 col-sm-12 col-xl-8 mx-auto">

                    <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-2">
                        <h5 class="fw-bold text-dark mb-0">แบบฟอร์มแก้ไข</h5>
                        <span class="badge bg-secondary">Doc ID: <?= htmlspecialchars($record['id'] ?? '-') ?></span>
                    </div>

                    <?php if ($record): ?>
                        <form method="POST">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">เลขที่เอกสาร</label>
                                    <input type="text" class="form-control bg-light" value="<?= htmlspecialchars($record['id']) ?>" readonly>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">วันที่รับเข้า</label>
                                    <input type="date" class="form-control" name="dateCreate" id="dateCreate"
                                        value="<?= htmlspecialchars($record['create_at']) ?>">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">ชื่อผู้ป่วย</label>
                                <input type="text" class="form-control" name="customer_name"
                                    value="<?= htmlspecialchars($record['customer_name']) ?>" required>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6 mb-3 mb-md-0">
                                    <label class="form-label fw-bold">กลุ่มงาน</label>
                                    <select class="form-select" name="workgroup_id" id="workgroupSelect" required>
                                        <option value="">-- เลือกกลุ่มงาน --</option>
                                        <?php foreach ($workgroups as $wg): ?>
                                            <option value="<?= $wg['id'] ?>" <?= ($wg['id'] == $record['workgroup_id']) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($wg['name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold">แผนกหอผู้ป่วย</label>
                                    <select id="departmentSelect" class="form-select" name="department_id" required>
                                        <option value="">-- เลือกแผนก --</option>
                                        <?php foreach ($departments as $dept): ?>
                                            <option value="<?= $dept['id'] ?>" <?= ($dept['id'] == $record['department_id']) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($dept['name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">แพทย์เจ้าของไข้</label>
                                <select class="form-select" name="staff_id" id="staff_DropDownAuditor" required>
                                    <option value="">-- เลือกแพทย์เจ้าของไข้ --</option>
                                    <?php foreach ($auditors as $doctor_staff): ?>
                                        <option value="<?= $doctor_staff['id'] ?>" <?= ($doctor_staff['id'] == $record['staff_id']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($doctor_staff['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold">หมายเหตุ</label>
                                <textarea class="form-control" rows="3" name="remark" placeholder="ระบุหมายเหตุเพิ่มเติม (ถ้ามี)"><?= htmlspecialchars($record['remark']) ?></textarea>
                            </div>

                            <div class="d-flex justify-content-end gap-2">
                                <a href="document-detail.php?doc_id=<?= urlencode($record['id']) ?>" class="btn btn-secondary px-4">ยกเลิก</a>
                                <button type="submit" class="btn btn-navy px-4 shadow-sm"><i class="fas fa-save me-2"></i>บันทึกการแก้ไข</button>
                            </div>
                        </form>
                    <?php else: ?>
                        <div class="alert alert-warning text-center">
                            <i class="fas fa-exclamation-triangle me-2"></i> ไม่พบข้อมูลสำหรับแก้ไข
                        </div>
                    <?php endif; ?>
                </div>
            </div>

        </div> </div> <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
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
        document.addEventListener("DOMContentLoaded", function() {
            
            const workgroupSelect = document.getElementById('workgroupSelect');
            const departmentSelect = document.getElementById('departmentSelect');
            const staffSelect = document.getElementById('staff_DropDownAuditor');

            // 1. เมื่อเปลี่ยนกลุ่มงาน (Workgroup)
            workgroupSelect.addEventListener('change', async function() {
                const workgroupId = this.value;

                // Reset Department & Staff
                departmentSelect.innerHTML = '<option value="">กำลังโหลด...</option>';
                staffSelect.innerHTML = '<option value="">-- รอเลือกแผนก --</option>'; // รีเซ็ตแพทย์ด้วย เพราะแผนกเปลี่ยน
                departmentSelect.disabled = true;
                staffSelect.disabled = true;

                try {
                    const response = await fetch('db/db-departments-by-workgroup.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: `workgroup_id=${workgroupId}`
                    });
                    const data = await response.json();

                    departmentSelect.innerHTML = '<option value="" selected>-- เลือกแผนก --</option>';
                    
                    if(data.length > 0){
                        data.forEach(item => {
                            const opt = document.createElement('option');
                            opt.value = item.id;
                            opt.textContent = item.name;
                            departmentSelect.appendChild(opt);
                        });
                        departmentSelect.disabled = false;
                    } else {
                        departmentSelect.innerHTML = '<option value="">ไม่มีข้อมูลแผนก</option>';
                    }

                } catch (error) {
                    departmentSelect.innerHTML = '<option>เกิดข้อผิดพลาด</option>';
                    console.error('Error:', error);
                }
            });

            // 2. เมื่อเปลี่ยนแผนก (Department)
            departmentSelect.addEventListener('change', async function() {
                const deptId = this.value;

                staffSelect.innerHTML = '<option value="">กำลังโหลด...</option>';
                staffSelect.disabled = true;

                try {
                    const response = await fetch('db/db-auditor-by-department.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: `dept_id=${deptId}`
                    });
                    
                    const data = await response.json();
                    
                    staffSelect.innerHTML = '<option value="">-- เลือกแพทย์เจ้าของไข้ --</option>';

                    if(data.length > 0){
                         data.forEach(item => {
                            const opt = document.createElement('option');
                            // เช็คชื่อ field ดีๆ ว่า backend ส่งอะไรมา (id หรือ auditor_id)
                            opt.value = item.auditor_id || item.id; 
                            opt.textContent = item.auditor_name || item.name;
                            staffSelect.appendChild(opt);
                        });
                        staffSelect.disabled = false;
                    } else {
                         staffSelect.innerHTML = '<option value="">ไม่พบรายชื่อแพทย์</option>';
                    }

                } catch (error) {
                    staffSelect.innerHTML = '<option>เกิดข้อผิดพลาด</option>';
                    console.error('Error:', error);
                    staffSelect.disabled = false;
                }
            });
        });
    </script>

</body>
</html>