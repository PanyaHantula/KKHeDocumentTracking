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
        .table-custom th, 
        .table-custom td {
            white-space: nowrap; /* ข้อความยาวแค่ไหนก็ห้ามขึ้นบรรทัดใหม่ */
            border: none;
            vertical-align: middle;
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
                        <h2 class="mb-1 fw-bold text-white" style="text-shadow: 0 2px 4px rgba(0,0,0,0.5);">
                            <i class="fas fa-sitemap me-2"></i>จัดการกลุ่มงาน</h3>
                        <p class="mb-0 opacity-75">บริหารจัดการข้อมูลกลุ่มงานภายในโรงพยาบาล</p>
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
            setTimeout(() => document.querySelector('.alert')?.remove(), 3000);
            </script>
            <?php endif; ?>

            <div class="container-fluid px-4 pb-5">
                <div class="card-box">
                    <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
                        <h5 class="fw-bold mb-0 text-dark"><i class="fas fa-list-ul me-2 text-primary"></i>รายชื่อกลุ่มงาน</h5>
                        <button type="button" class="btn btn-success shadow-sm" data-bs-toggle="modal"
                            data-bs-target="#addworkgroupModal">
                            <i class="fas fa-plus-circle me-1"></i> เพิ่มกลุ่มงานใหม่
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-custom table-hover table-bordered align-middle">
                            <thead class="text-center">
                                <tr>
                                    <th scope="col" style="width: 80px;">ลำดับ</th>
                                    <th scope="col" style="width: 150px;">รหัส</th>
                                    <th scope="col">กลุ่มงาน</th>
                                    <th scope="col" style="width: 200px;">จัดการ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $workgroups = include __DIR__ . '/db/db-workgroup-select.php';
                                if (count($workgroups) > 0) {
                                    $i=1;
                                    foreach ($workgroups as $workgroup): ?>
                                <tr>
                                    <td class="text-center fw-bold text-muted"><?= $i++ ?></td>
                                    <td class="text-center"><span class="badge bg-light text-dark border"><?= htmlspecialchars($workgroup['id']) ?></span></td>
                                    <td class="fw-bold text-primary"><?= htmlspecialchars($workgroup['name']) ?></td>
                                    <td class="text-center">
                                        <button class="btn btn-navy btn-sm me-1 shadow-sm" data-bs-toggle="modal"
                                            data-bs-target="#editworkgroupModal"
                                            data-id="<?= htmlspecialchars($workgroup['id']) ?>"
                                            data-name="<?= htmlspecialchars($workgroup['name']) ?>">
                                            <i class="fas fa-edit"></i> แก้ไข
                                        </button>

                                        <a href="db/db-workgroup-delete.php?id=<?= urlencode($workgroup['id']) ?>"
                                            class="btn btn-danger btn-sm shadow-sm"
                                            onclick="return confirm('คุณแน่ใจหรือไม่ว่าต้องการลบ <?= addslashes($workgroup['name']) ?> ??');">
                                            <i class="fas fa-trash-alt"></i> ลบ
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach;
                                } else {
                                    echo "<tr><td colspan='4' class='text-center py-4 text-muted'>ไม่พบข้อมูลแผนก</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                    </div>

                <div class="modal fade" id="addworkgroupModal" tabindex="-1" aria-labelledby="addworkgroupModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content border-0 shadow">
                            <div class="modal-header modal-header-custom">
                                <h5 class="modal-title fw-bold"><i class="fas fa-plus-circle me-2"></i>เพิ่มกลุ่มงานใหม่</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="ปิด"></button>
                            </div>
                            <div class="modal-body p-4">
                                <form id="addWorkGroupForm" method="POST" action="db/db-workgroup-insert.php">

                                    <div class="mb-3">
                                        <label for="workgroupCode" class="form-label fw-bold">รหัสกลุ่มงาน</label>
                                        <input type="text" class="form-control" id="workgroupCode" name="workgroupCode"
                                            placeholder="กรอกรหัสกลุ่มงาน เช่น G001" required>
                                    </div>

                                    <div class="mb-4">
                                        <label for="workgroupName" class="form-label fw-bold">ชื่อกลุ่มงาน</label>
                                        <input type="text" class="form-control" id="workgroupName" name="workgroupName"
                                            placeholder="กรอกชื่อกลุ่มงาน" required>
                                    </div>

                                    <button type="submit" class="btn btn-success w-100 py-2"><i class="fas fa-save me-2"></i>บันทึกข้อมูล</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="editworkgroupModal" tabindex="-1" aria-labelledby="editworkgroupModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content border-0 shadow">
                            <form id="editWorkGroupForm" method="POST" action="db/db-workgroup-update.php">
                                <div class="modal-header modal-header-custom">
                                    <h5 class="modal-title fw-bold"><i class="fas fa-edit me-2"></i>แก้ไขข้อมูลกลุ่มงาน</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="ปิด"></button>
                                </div>
                                <div class="modal-body p-4">
                                    <input type="hidden" name="old_id" id="editOldId">

                                    <div class="mb-3">
                                        <label for="editWorkgroupId" class="form-label fw-bold">รหัสกลุ่มงาน</label>
                                        <input type="text" class="form-control" id="editWorkgroupId" name="id" required>
                                    </div>

                                    <div class="mb-4">
                                        <label for="editWorkgroupName" class="form-label fw-bold">ชื่อกลุ่มงาน</label>
                                        <input type="text" class="form-control" id="editWorkgroupName" name="name" required>
                                    </div>
                                    
                                    <div class="d-grid">
                                        <button type="submit" class="btn btn-navy py-2"><i class="fas fa-save me-2"></i>บันทึกการแก้ไข</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <script>
                document.addEventListener('DOMContentLoaded', function() {
                    var editModal = document.getElementById('editworkgroupModal');
                    editModal.addEventListener('show.bs.modal', function(event) {
                        var button = event.relatedTarget;
                        var id = button.getAttribute('data-id');
                        var name = button.getAttribute('data-name');

                        // ใส่ค่าลงในฟอร์ม
                        document.getElementById('editOldId').value = id; // รหัสเดิม
                        document.getElementById('editWorkgroupId').value = id;
                        document.getElementById('editWorkgroupName').value = name;
                    });
                });
                </script>

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
</body>

</html>