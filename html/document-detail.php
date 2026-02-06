<?php
    session_start();

    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }

    // Header caching control
    header("Cache-Control: no-cache, no-store, must-revalidate");
    header("Pragma: no-cache");
    header("Expires: 0");

    $name = $_SESSION['name'];
    // include __DIR__ . '/auth.php'; // (Uncomment ถ้ามีไฟล์นี้)

    $isLogin  = isset($_SESSION['user_id']);
    $roleId   = $isLogin ? $_SESSION['role'] : '-';

    // โหลดข้อมูล
    $records = include __DIR__ . '/db/db-document-detail.php';
    $docId = isset($_GET['doc_id']) ? $_GET['doc_id'] : null;

    // Handle return data
    if(isset($records) && !isset($record)) {
        // ถ้า $records เป็น array หลายบรรทัด ให้เอาบรรทัดแรก (ถ้าดึงตาม ID ควรมีแค่ 1)
        $record = (isset($records[0])) ? $records[0] : $records;
    }

    // ถ้าไม่มีข้อมูล ให้หยุดการทำงานหรือแจ้งเตือน
    if (empty($record)) {
        die("ไม่พบข้อมูลเอกสาร");
    }

    // function แปลงวันที่ 
    function formatDate($date) {
            if (empty($date) || $date == '-' || $date == '0000-00-00' || $date == '0000-00-00 00:00:00') {
                return '-';
            }
            $timestamp = strtotime($date);
            if (!$timestamp) return '-'; 
            // return date('d m Y', $timestamp);

            // ปี พ.ศ. (ไทย) 
            return date('d/m/', $timestamp) . (date('Y', $timestamp) + 543);
        }
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="utf-8">
    <title>รายละเอียดเอกสาร</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include __DIR__ . '/css-link-library.php'; ?>

    <style>
        /* Theme & Layout CSS */
        :root {
            --primary-navy: #001f3f;
            --primary-blue: #003366;
            --accent-light: #f0f4f8;
        }

        body {
            background-color: var(--accent-light);
            font-family: 'Sarabun', sans-serif;
        }

        .page-header {
            background: linear-gradient(135deg, #001f3f 0%, #004085 100%);
            color: white;
            padding: 2rem;
            border-radius: 0 0 20px 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }

        .card-custom {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 51, 102, 0.08);
            border: none;
            overflow: hidden;
        }

        .table-nowrap th,
        .table-nowrap td {
            white-space: nowrap !important;
            vertical-align: middle;
        }

        .table-custom th {
            background-color: #f8f9fa;
            color: var(--primary-blue);
            font-weight: 600;
        }

        .table-expanded th, 
        .table-expanded td {
            padding: 1rem !important; 
        }
        .card-wide {
            width: 100%;
        }
        .btn-navy-outline {
            color: var(--primary-blue);
            border-color: var(--primary-blue);
        }

        .btn-navy-outline:hover {
            background-color: var(--primary-blue);
            color: white;
        }

        .badge-status {
            padding: 0.5em 0.8em;
            border-radius: 20px;
            font-weight: 500;
            font-size: 0.85rem;
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
                        <h2 class="mb-1 fw-bold text-white">รายละเอียดเอกสาร</h2>
                    </div>
                </div>
            </div>

            <div class="container-fluid px-4 pb-5">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <a href="document-list.php" class="btn btn-secondary shadow-sm">
                        <i class="fas fa-arrow-left me-1"></i> ย้อนกลับ
                    </a>
                    <?php if ($roleId == 1 || $roleId == 2): ?>
                    <div>
                        <a href="document-edit.php?doc_id=<?= urlencode($record['id']) ?>"
                            class="btn btn-navy-outline me-2 shadow-sm">
                            <i class="far fa-edit me-1"></i> แก้ไข
                        </a>
                        <a href="/db/db-document-delete.php?doc_id=<?= urlencode($record['id']) ?>"
                            class="btn btn-danger shadow-sm"
                            onclick="return confirm('คุณแน่ใจหรือไม่ที่จะลบเอกสารนี้?');">
                            <i class="far fa-trash-alt me-1"></i> ลบ
                        </a>
                    </div>
                    <?php endif; ?>
                </div>

                <div class="row justify-content-center mb-4">
                    <div class="col-xl-10">
                        <div class="card-custom p-4 shadow-sm">
                            <div class="row">
                                <div class="col-lg-8">
                                    <div class="mb-3">
                                        <h5 class="fw-bold text-dark border-bottom pb-2">ข้อมูลเอกสาร: <span class="text-primary"><?= htmlspecialchars($record['id']) ?></span></h5>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover table-custom mb-0">
                                            <tbody>
                                                <tr>
                                                    <th style="width: 30%;">ชื่อผู้ป่วย</th>
                                                    <td><?= htmlspecialchars($record['customer_name'] ?? '-') ?></td>
                                                </tr>
                                                <tr>
                                                    <th>กลุ่มงาน</th>
                                                    <td><?= htmlspecialchars($record['workgroup_name'] ?? '-') ?></td>
                                                </tr>
                                                <tr>
                                                    <th>หอผู้ป่วย</th>
                                                    <td><?= htmlspecialchars($record['department_name'] ?? '-') ?></td>
                                                </tr>
                                                <tr>
                                                    <th>แพทย์เจ้าของไข้</th>
                                                    <td><?= htmlspecialchars($record['staff_name'] ?? '-') ?></td>
                                                </tr>
                                                <tr>
                                                    <th>วันที่รับเข้า</th>
                                                    <td><?= htmlspecialchars(formatDate($record['create_at'] ?? '-')) ?></td>
                                                </tr>
                                                <tr>
                                                    <th>หมายเหตุ</th>
                                                    <td><?= htmlspecialchars($record['remark'] ?? '-') ?></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="col-lg-4 mt-3 mt-lg-0">
                                    <div class="card bg-light border-0 h-100">
                                        <div class="card-body d-flex flex-column justify-content-center align-items-center text-center">
                                            <h6 class="text-uppercase text-muted fw-bold mb-3">สถานะปัจจุบัน</h6>
                                            <?php 
                                                $status = $record['status_name'] ?? '-';
                                                $statusColor = 'text-secondary';
                                                $statusIcon = 'fa-question-circle';

                                                if ($status == 'เสร็จสิ้น') { $statusColor = 'text-success'; $statusIcon = 'fa-check-circle'; }
                                                elseif ($status == 'ล่าช้า') { $statusColor = 'text-danger'; $statusIcon = 'fa-exclamation-triangle'; }
                                                elseif ($status == 'รอสรุป') { $statusColor = 'text-warning'; $statusIcon = 'fa-clock'; }
                                            ?>
                                            <i class="fas <?= $statusIcon ?> fa-4x mb-3 <?= $statusColor ?>"></i>
                                            <h2 class="fw-bold <?= $statusColor ?>"><?= htmlspecialchars($status) ?></h2>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="card-custom p-4 shadow-sm">
                            <div class="d-flex align-items-center mb-4">
                                <h4 class="fw-bold text-dark m-0">ประวัติการดำเนินการ</h4>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-striped table-hover table-bordered table-expanded table-nowrap mb-0 align-middle">
                                    <thead class="table-dark text-center">
                                        <tr>
                                            <th>ลำดับการตรวจสอบ</th>
                                            <th>ผู้รับผิดชอบ</th>
                                            <th>สถานะ</th>
                                            <th>วันที่บันทึก</th>
                                            <th>กำหนดส่ง(วัน)</th>
                                            <th>วันที่เสร็จสิ้น</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
    
                                        $workflow_steps = [
                                            [
                                                'step_name' => '1. แพทย์ Resident',
                                                'person'    => $record['resident_name'],
                                                'status'    => $record['resident_status'],  
                                                'date'      => formatDate($record['resident_create_at'] ?? '-'), 
                                                'duedate'   => $record['resident_duedate'], 
                                                'date_complete' => formatDate($record['resident_complete_at'] ?? '-')
                                            ],
                                            [
                                                'step_name' => '2. แพทย์เจ้าของไข้ (Staff)',
                                                'person'    => $record['staff_name'],
                                                'status'    => $record['staff_status'],  
                                                'date'      => formatDate($record['staff_create_at'] ?? '-'),
                                                'duedate'   => $record['staff_duedate'], 
                                                'date_complete' => formatDate($record['staff_complete_at'] ?? '-')
                                            ],
                                            [
                                                'step_name' => '3. ส่งเวชระเบียน',
                                                'person'    => "-", 
                                                'status'    => $record['medical_records_status'],  
                                                'date'      => formatDate($record['medical_records_create_at'] ?? '-'),
                                                'duedate'   => $record['medical_records_duedate'], 
                                                'date_complete' => formatDate($record['medical_records_complete_at'] ?? '-')
                                            ],
                                            [
                                                'step_name' => '4. แพทย์ผู้ตรวจสอบ (Auditor)',
                                                'person'    => $record['auditor_name'],
                                                'status'    => $record['auditor_status'],  
                                                'date'      => formatDate($record['auditor_create_at'] ?? '-'),
                                                'duedate'   => $record['auditor_duedate'], 
                                                'date_complete' => formatDate($record['auditor_complete_at'] ?? '-')
                                            ],
                                        ];

                                        foreach ($workflow_steps as $step): 
                                            $currentStatus = $step['status']; 
                                            $badgeClass = 'bg-secondary'; 
                                            if (strpos($currentStatus, 'เสร็จสิ้น') !== false) {
                                                $badgeClass = 'bg-success';
                                            } elseif (strpos($currentStatus, 'ล่าช้า') !== false) {
                                                $badgeClass = 'bg-danger';
                                            } elseif (strpos($currentStatus, 'รอ') !== false) {
                                                $badgeClass = 'bg-warning text-dark';
                                            }
                                        ?>
                                        <tr>
                                            <td class="fw-bold text-primary"><?= htmlspecialchars($step['step_name']) ?></td>
                                            <td><?= htmlspecialchars($step['person'] ?? '-') ?></td>
                                            <td class="text-center">
                                                <?php if($step['status']): ?>
                                                    <span class="badge <?= $badgeClass ?> rounded-pill px-3 py-2"> 
                                                        <?= htmlspecialchars($step['status']) ?>
                                                    </span>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center"><?= htmlspecialchars($step['date']) ?></td>
                                            <td class="text-center fw-bold"><?= htmlspecialchars($step['duedate'] ?? '-') ?></td>
                                            <td class="text-center fw-bold text-success">
                                                <?= htmlspecialchars($step['date_complete'] ?? '-') ?>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
        <script src="js/main.js"></script>
</body>

</html>