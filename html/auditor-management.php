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

// -------------------------------------------------------------------------
$workgroups = include __DIR__ . '/db/db-workgroup-select.php';
if (!is_array($workgroups)) $workgroups = [];

$departments = include __DIR__ . '/db/db-departments-select.php';
if (!is_array($departments)) $departments = [];

$roles = include __DIR__ . '/db/db-role-select.php';
if (!is_array($roles)) $roles = [];
// -------------------------------------------------------------------------
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

        /* Custom Table Styling */
        .table-custom thead {
            background-color: var(--primary-deep);
            color: white;
        }
        
        /* --- จุดที่ปรับแก้: บังคับไม่ให้ตัดคำ --- */
        .table-custom th, 
        .table-custom td {
            white-space: nowrap; /* ข้อความยาวแค่ไหนก็ห้ามขึ้นบรรทัดใหม่ */
            border: none;
            vertical-align: middle;
        }
        /* ----------------------------------- */

        .table-custom th {
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
                        <h3 class="mb-1 text-white fw-bold"><i class="fas fa-user-md me-2"></i>จัดการรายชื่อแพทย์ผู้ตรวจสอบ</h3>
                        <p class="mb-0 opacity-75">บริหารจัดการข้อมูลแพทย์และผู้มีสิทธิ์ตรวจสอบเอกสาร</p>
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
                        <h5 class="fw-bold mb-0 text-dark"><i class="fas fa-list-ul me-2 text-primary"></i>รายชื่อผู้ตรวจสอบ</h5>
                        <button type="button" class="btn btn-success shadow-sm" data-bs-toggle="modal"
                            data-bs-target="#addDepartmentModal">
                            <i class="fas fa-plus-circle me-1"></i> เพิ่มผู้ตรวจสอบ
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-custom table-hover table-bordered mb-0">
                            <thead class="text-center">
                                <tr>
                                    <th style="width: 60px;">ลำดับ</th>
                                    <th style="width: 100px;">รหัส</th>
                                    <th>ผู้ตรวจสอบ</th>
                                    <th>กลุ่มงาน</th>
                                    <th>แผนกหอผู้ป่วย</th>
                                    <th>ระดับการตรวจสอบ</th>
                                    <th style="width: 180px;">จัดการ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $results = include __DIR__ . '/db/db-auditors-select.php';
                                if (count($results) > 0) {
                                    $i=1;
                                    foreach ($results as $result): ?>
                                <tr>
                                    <td class="text-center fw-bold text-muted"><?= $i++ ?></td>
                                    <td class="text-center"><?= htmlspecialchars($result['id']) ?></span></td>
                                    <td class="fw-bold"><?= htmlspecialchars($result['name']) ?></td>
                                    <td><?= htmlspecialchars($result['workgroup_name']) ?></td>
                                    <td><?= htmlspecialchars($result['department_name']) ?></td>
                                    <td class="text-center"><span class="badge bg-secondary"><?= htmlspecialchars($result['role_name']) ?></span></td>
                                    <td class="text-center">
                                        <button class="btn btn-navy btn-sm me-1 shadow-sm" data-bs-toggle="modal"
                                            data-bs-target="#editAuditorModal"
                                            data-id="<?= htmlspecialchars($result['id']) ?>"
                                            data-name="<?= htmlspecialchars($result['name']) ?>"
                                            data-wg="<?= htmlspecialchars($result['workgroup_id']) ?>"
                                            data-dept="<?= htmlspecialchars($result['department_id']) ?>"
                                            data-role="<?= htmlspecialchars($result['role']) ?>">
                                            <i class="fas fa-edit"></i> แก้ไข
                                        </button>

                                        <a href="db/db-auditor-delete.php?id=<?= urlencode($result['id']) ?>"
                                            class="btn btn-danger btn-sm shadow-sm"
                                            onclick="return confirm('คุณแน่ใจหรือไม่ว่าต้องการลบผู้ตรวจสอบ <?= addslashes($result['name']) ?> ?');">
                                            <i class="fas fa-trash-alt"></i> ลบ
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach;
                                } else {
                                    echo "<tr><td colspan='7' class='text-center py-4 text-muted'>ไม่มีข้อมูลผู้ตรวจสอบ</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="addDepartmentModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <form action="db/db-auditor-insert.php" method="POST">
                        <div class="modal-header modal-header-custom">
                            <h5 class="modal-title fw-bold"><i class="fas fa-user-plus me-2"></i>เพิ่มผู้ตรวจสอบ</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body p-4">
                            <div class="mb-3">
                                <label class="form-label fw-bold">รหัส</label>
                                <input type="text" class="form-control" name="id" placeholder="กรอกรหัส เช่น 001"
                                    required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">ชื่อแพทย์ผู้ตรวจสอบ</label>
                                <input type="text" class="form-control" name="name" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold">กลุ่มงาน</label>
                                <select class="form-select" name="workgroupCode" id="addWorkgroup" required>
                                    <option value="" disabled selected>-- เลือกกลุ่มงาน --</option>
                                    <?php foreach ($workgroups as $wg): ?>
                                    <option value="<?= htmlspecialchars($wg['id']) ?>">
                                        <?= htmlspecialchars($wg['name']) ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">แผนก</label>
                                <select class="form-select" name="departmentCode" id="addDepartment" required>
                                    <option value="" disabled selected>-- เลือกหอผู้ป่วย --</option>
                                    <?php foreach ($departments as $dept): ?>
                                    <option value="<?= htmlspecialchars($dept['department_id']) ?>">
                                        <?= htmlspecialchars($dept['department_name']) ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label fw-bold">ระดับการตรวจสอบ</label>
                                <select class="form-select" name="role" id="addRole" required>
                                    <option value="" disabled selected>-- เลือกสิทธิ์ --</option>
                                    <?php foreach ($roles as $r): ?>
                                    <option value="<?= $r['id'] ?>"><?= htmlspecialchars($r['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-success py-2"><i class="fas fa-save me-2"></i>บันทึกข้อมูล</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade" id="editAuditorModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <form action="db/db-auditor-update.php" method="POST">
                        <div class="modal-header modal-header-custom">
                            <h5 class="modal-title fw-bold"><i class="fas fa-user-edit me-2"></i>แก้ไขข้อมูลผู้ตรวจสอบ</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body p-4">
                            <input type="hidden" name="old_id" id="editOldId">

                            <div class="mb-3">
                                <label class="form-label fw-bold">รหัส</label>
                                <input type="text" class="form-control" name="id" id="editId" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">ชื่อแพทย์ผู้ตรวจสอบ</label>
                                <input type="text" class="form-control" name="name" id="editName" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">กลุ่มงาน</label>
                                <select name="workgroupCode" id="editUserWorkgroup" class="form-select">
                                    <?php foreach ($workgroups as $wg): ?>
                                    <option value="<?= $wg['id'] ?>">
                                        <?= htmlspecialchars($wg['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">แผนก</label>
                                <select name="departmentCode" id="editUserDept" class="form-select">
                                    <?php foreach ($departments as $dept): ?>
                                    <option value="<?= $dept['department_id'] ?>">
                                        <?= htmlspecialchars($dept['department_name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold">ระดับการตรวจสอบ</label>
                                <select name="role" id="editUserRole" class="form-select">
                                    <?php foreach ($roles as $r): ?>
                                    <option value="<?= $r['id'] ?>"><?= htmlspecialchars($r['name']) ?></option>
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

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const editModal = document.getElementById('editAuditorModal');
        editModal.addEventListener('show.bs.modal', function(event) {
            var button = event.relatedTarget;
            // เซ็ตค่า input
            document.getElementById('editOldId').value = button.getAttribute('data-id');
            document.getElementById('editId').value = button.getAttribute('data-id');
            document.getElementById('editName').value = button.getAttribute('data-name');
            document.getElementById('editUserWorkgroup').value = button.getAttribute('data-wg');
            document.getElementById('editUserDept').value = button.getAttribute('data-dept');
            document.getElementById('editUserRole').value = button.getAttribute('data-role');
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