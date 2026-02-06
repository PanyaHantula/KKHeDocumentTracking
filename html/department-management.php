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
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>ระบบติดตามงานเอกสารโรงพยาบาล</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <?php include __DIR__ . '/css-link-library.php'; ?>

    <style>
        /* --- Deep Blue Theme Variables --- */
        :root {
            --primary-deep: #003366;   /* น้ำเงินเข้ม */
            --primary-main: #0056b3;   /* น้ำเงินหลัก */
            --accent-light: #e7f1ff;   /* ฟ้าอ่อนพื้นหลัง */
            --text-dark: #2c3e50;
        }

        body { 
            background-color: #f0f4f8; 
            font-family: 'Sarabun', sans-serif; 
        }

        /* Page Header */
        .page-header {
            background: linear-gradient(135deg, #001f3f 0%, #004085 100%);
            color: white;
            padding: 2rem;
            border-radius: 0 0 20px 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }
        .page-header h3 {
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
            font-weight: bold;
        }

        /* Card Container */
        .card-box {
            background: white;
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 51, 102, 0.08);
            padding: 1.5rem;
            transition: all 0.3s;
        }

        /* Custom Table */
        .table-custom thead {
            background-color: var(--primary-deep);
            color: white;
        }
        .table-custom th {
            border: none;
            font-weight: 500;
        }
        .table-hover tbody tr:hover {
            background-color: var(--accent-light);
        }

        /* Buttons */
        .btn-navy {
            background-color: var(--primary-deep);
            color: white;
            border: none;
        }
        .btn-navy:hover {
            background-color: #002244;
            color: white;
            transform: translateY(-1px);
        }
        
        /* Modal Styles */
        .modal-header-custom {
            background-color: var(--primary-deep);
            color: white;
        }
        .modal-header-custom .btn-close {
            filter: invert(1) grayscale(100%) brightness(200%);
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
                        <h3 class="mb-1 text-white fw-bold"><i class="fas fa-procedures me-2"></i>จัดการรายชื่อหอผู้ป่วย</h3>
                        <p class="mb-0 opacity-75">บริหารจัดการข้อมูลหอผู้ป่วยและแผนกต่างๆ</p>
                    </div>
                </div>
            </div>

            <?php if (isset($_GET['msg'])): ?>
            <div class="container mt-n4 mb-3 position-relative" style="z-index: 10;">
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
            <div class="container-fluid px-4 pb-5">
                <div class="card-box">
                    <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
                        <h5 class="fw-bold mb-0 text-dark"><i class="fas fa-list-ul me-2 text-primary"></i>รายชื่อหอผู้ป่วย</h5>
                        <button type="button" class="btn btn-success shadow-sm" data-bs-toggle="modal"
                            data-bs-target="#addDepartmentModal">
                            <i class="fas fa-plus-circle me-1"></i> เพิ่มหอผู้ป่วยใหม่
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-custom table-hover table-bordered align-middle">
                            <thead class="text-center">
                                <tr>
                                    <th scope="col" style="width: 60px;">ลำดับ</th>
                                    <th scope="col" style="width: 100px;">รหัส</th>
                                    <th scope="col">หอผู้ป่วย</th>
                                    <th scope="col">กลุ่มงาน</th>
                                    <th scope="col" style="width: 180px;">จัดการ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $departments = include __DIR__ . '/db/db-departments-select.php';
                                if (count($departments) > 0) {
                                    $i=1;
                                    foreach ($departments as $department): ?>
                                <tr>
                                    <td class="text-center fw-bold text-muted"><?= $i++ ?></td>
                                    <td class="text-center"><span class="badge bg-light text-dark border"><?= htmlspecialchars($department['department_id']) ?></span></td>
                                    <td class="fw-bold text-primary"><?= htmlspecialchars($department['department_name']) ?></td>
                                    <td>
                                        <span class="badge bg-secondary"><?= htmlspecialchars($department['workgroup_name'] ?? 'N/A') ?></span>
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-navy btn-sm me-1 shadow-sm" data-bs-toggle="modal"
                                            data-bs-target="#editDepartmentModal"
                                            data-id="<?= htmlspecialchars($department['department_id']) ?>"
                                            data-name="<?= htmlspecialchars($department['department_name']) ?>"
                                            data-workgroupid="<?= htmlspecialchars($department['workgroup_id'] ?? '') ?>">
                                            <i class="fas fa-edit"></i> แก้ไข
                                        </button>

                                        <a href="db/db-department-delete.php?id=<?= urlencode($department['department_id']) ?>"
                                            class="btn btn-danger btn-sm shadow-sm"
                                            onclick="return confirm('คุณแน่ใจหรือไม่ว่าต้องการลบ <?= addslashes($department['department_name']) ?> ??');">
                                            <i class="fas fa-trash-alt"></i> ลบ
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach;
                                } else {
                                    echo "<tr><td colspan='5' class='text-center py-4 text-muted'>ไม่มีข้อมูลแผนก</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                    </div>
            </div>

            <div class="modal fade" id="addDepartmentModal" tabindex="-1" aria-labelledby="addDepartmentLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow">
                        <div class="modal-header modal-header-custom">
                            <h5 class="modal-title fw-bold"><i class="fas fa-plus-circle me-2"></i>เพิ่มหอผู้ป่วยใหม่</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="ปิด"></button>
                        </div>
                        <div class="modal-body p-4">
                            <form id="addDepartmentForm" method="POST" action="db/db-department-insert.php">

                                <div class="mb-3">
                                    <label for="departmentCode" class="form-label fw-bold">รหัสแผนก</label>
                                    <input type="text" class="form-control" id="departmentCode" name="departmentCode"
                                        placeholder="กรอกรหัสแผนก เช่น D001" required>
                                </div>

                                <div class="mb-3">
                                    <label for="departmentName" class="form-label fw-bold">ชื่อหอผู้ป่วย</label>
                                    <input type="text" class="form-control" id="departmentName" name="departmentName"
                                        placeholder="กรอกชื่อหอผู้ป่วย" required>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label fw-bold">กลุ่มงาน</label>
                                    <?php $workgroups = include __DIR__ . '/db/db-workgroup-select.php'; ?>
                                    <select class="form-select" name="workgroup_id" id="addworkgroup" required>
                                        <option value="" disabled selected>-- เลือกกลุ่มงาน --</option>
                                        <?php foreach ($workgroups as $wg): ?>
                                        <option value="<?= htmlspecialchars($wg['id']) ?>">
                                            <?= htmlspecialchars($wg['name']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="d-grid">
                                    <button type="submit" class="btn btn-success py-2"><i class="fas fa-save me-2"></i>บันทึกข้อมูล</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="editDepartmentModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow">
                        <form action="db/db-department-update.php" method="POST">
                            <div class="modal-header modal-header-custom">
                                <h5 class="modal-title fw-bold"><i class="fas fa-edit me-2"></i>แก้ไขหอผู้ป่วย</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body p-4">
                                <input type="hidden" name="old_id" id="oldDeptId">

                                <div class="mb-3">
                                    <label for="editDeptId" class="form-label fw-bold">รหัสหอผู้ป่วย</label>
                                    <input type="text" class="form-control" name="id" id="editDeptId" required>
                                </div>

                                <div class="mb-3">
                                    <label for="editDeptName" class="form-label fw-bold">ชื่อหอผู้ป่วย</label>
                                    <input type="text" class="form-control" name="name" id="editDeptName" required>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label fw-bold">กลุ่มงาน</label>
                                    <?php 
                                    // โหลดซ้ำหรือไม่ก็ได้ ถ้าตัวแปร $workgroups ยังอยู่จากข้างบนก็ใช้ต่อได้เลย
                                    // แต่เพื่อความชัวร์ใน scope นี้ จะใช้ตัวแปรเดิมก็ได้
                                    ?>
                                    <select class="form-select" name="workgroup_id" id="editworkgroup" required>
                                        <option value="" disabled selected>-- เลือกกลุ่มงาน --</option>
                                        <?php foreach ($workgroups as $wg): ?>
                                        <option value="<?= htmlspecialchars($wg['id']) ?>">
                                            <?= htmlspecialchars($wg['name']) ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-navy py-2"><i class="fas fa-save me-2"></i>บันทึกการแก้ไข</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            </div>
        </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var editModal = document.getElementById('editDepartmentModal');
        editModal.addEventListener('show.bs.modal', function(event) {
            var button = event.relatedTarget;

            var id = button.getAttribute('data-id');
            var name = button.getAttribute('data-name');
            var workgroupID = button.getAttribute('data-workgroupid');

            // เซ็ตค่าฟิลด์ input
            document.getElementById('oldDeptId').value = id;
            document.getElementById('editDeptId').value = id;
            document.getElementById('editDeptName').value = name;

            // เซ็ตค่า select (workgroup)
            var select = document.getElementById('editworkgroup');
            if (select && workgroupID) {
                Array.from(select.options).forEach(option => {
                    option.selected = (option.value === workgroupID);
                });
            }
        });
    });
    </script>

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
</body>

</html>