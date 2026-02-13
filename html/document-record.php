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
include __DIR__ . '/db/db-record-search-update.php';
include __DIR__ . '/service/update-overdue.php'; 

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>ระบบติดตามงานเอกสารโรงพยาบาล</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <?php include __DIR__ . '/css-link-library.php'; ?>

    <style>
        /* --- Theme Colors Setup --- */
        :root {
            --primary-deep: #003366;
            /* น้ำเงินเข้มมาก */
            --primary-main: #0056b3;
            /* น้ำเงินหลัก */
            --accent-blue: #e7f1ff;
            /* ฟ้าอ่อนสำหรับพื้นหลังบางส่วน */
            --success-green: #198754;
            /* เขียว */
        }

        body {
            background-color: #f0f4f8;
            font-family: 'Sarabun', sans-serif;
        }

        /* Card Styling */
        .card-box {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 51, 102, 0.08);
            transition: all 0.3s ease;
            background: white;
        }

        .card-box:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0, 51, 102, 0.15);
        }

        /* Step Header Styling */
        .step-header {
            border-left: 5px solid var(--primary-deep);
            padding-left: 15px;
            margin-bottom: 20px;
            background: linear-gradient(90deg, rgba(0, 51, 102, 0.05) 0%, rgba(255, 255, 255, 0) 100%);
            padding-top: 5px;
            padding-bottom: 5px;
            border-radius: 0 8px 8px 0;
        }

        /* Form Controls */
        .input-group-text {
            background-color: #f8f9fa;
            border-color: #dee2e6;
            color: #495057;
        }

        .form-control,
        .form-select {
            border-color: #dee2e6;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--primary-main);
            box-shadow: 0 0 0 0.25rem rgba(0, 86, 179, 0.25);
        }

        .form-control:read-only {
            background-color: #f8f9fa;
            color: #6c757d;
        }

        /* Table Styling */
        .table-custom th {
            background-color: var(--accent-blue);
            width: 35%;
            color: var(--primary-deep);
            font-weight: 600;
            border-color: #dee2e6;
        }

        .fixed-label {
            width: 140px;
            text-align: center;
            justify-content: center;
            background-color: var(--primary-deep);
            color: white;
            border: none;
        }

        /* Page Header */
        .page-header {
            background: linear-gradient(135deg, #001f3f 0%, #004085 100%);
            /* ไล่สีน้ำเงินเข้ม */
            color: white;
            border-radius: 0 0 25px 25px;
            margin-bottom: 35px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        /* Custom Buttons */
        .btn-deep-blue {
            background-color: var(--primary-deep);
            border-color: var(--primary-deep);
            color: white;
            font-weight: 500;
        }

        .btn-deep-blue:hover {
            background-color: #002244;
            border-color: #002244;
            color: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .btn-success-custom {
            background-color: var(--success-green);
            border-color: var(--success-green);
            color: white;
        }

        .btn-success-custom:hover {
            background-color: #146c43;
            box-shadow: 0 4px 8px rgba(25, 135, 84, 0.3);
        }

        /* Custom Borders for Cards */
        .border-blue-theme {
            border-left: 4px solid #0056b3 !important;
        }

        .border-green-theme {
            border-left: 4px solid #02770c !important;
        }

        .border-yellow-theme {
            border-left: 4px solid #c7b705 !important;
        }

        .border-red-theme {
            border-left: 4px solid #af3508 !important;
        }
    </style>
</head>

<body class="d-flex flex-column min-vh-100">
    <div class="container-fluid flex-fill position-relative d-flex p-0">
        <?php include __DIR__ . '/menu-bar/sidebar.php'; ?>
        <div class="content w-100">
            <?php include __DIR__ . '/menu-bar/navbar.php'; ?>

            <!-- <div class="container-fluid pt-0 px-0">
                <div class="page-header d-flex align-items-center justify-content-center p-5">
                    <div class="text-center">
                        <h2 class="mb-1 fw-bold text-white" style="text-shadow: 0 2px 4px rgba(0,0,0,0.5);">
                            <i class="fas fa-file-medical-alt me-2"></i>บันทึกงานติดตามเอกสาร
                        </h2>
                    </div>
                </div>
            </div> -->

            <div class="container-fluid px-4 pb-5">
                <!-- ค้นหาเอกสาร -->
                <div class="row justify-content-center mb-4">
                    <div class="col-lg-8">
                        <div class="card card-box p-4">
                            <form method="GET" action="" class="row g-2 align-items-center justify-content-center">
                                <div class="col-auto">
                                    <h5 class="mb-0 fw-bold" style="color: var(--primary-deep);"><i
                                            class="fas fa-search me-2"></i>ค้นหาเอกสาร</h5>
                                </div>
                                <div class="col-md-7">
                                    <div class="input-group">
                                        <input type="text" name="doc_id"
                                            class="form-control form-control-lg border shadow-none"
                                            placeholder="ระบุเลขที่เอกสาร (AN/HN)" autofocus>
                                        <button type="submit" class="btn btn-deep-blue px-4"><i
                                                class="fas fa-search"></i> ค้นหา</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <form method="POST">
                    <div class="row g-4">
                        <!-- ข้อมูลผู้ป่วยและเอกสาร -->
                        <div class="col-md-5">
                            <div class="card card-box mb-4">
                                <div class="card-header bg-white border-0 pt-3 pb-0">
                                    <h5 class="fw-bold" style="color: var(--primary-deep);"><i
                                            class="fas fa-info-circle me-2"></i>ข้อมูลผู้ป่วยและเอกสาร</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-custom mb-0">
                                            <tr>
                                                <th>เลขที่เอกสาร</th>
                                                <td class="fw-bold text-primary"><?= htmlspecialchars($record['id']) ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>ชื่อผู้ป่วย</th>
                                                <td><?= htmlspecialchars($record['customer_name']) ?></td>
                                            </tr>
                                            <tr>
                                                <th>กลุ่มงาน</th>
                                                <td><?= htmlspecialchars($record['workgroup_name']) ?></td>
                                            </tr>
                                            <tr>
                                                <th>หอผู้ป่วย</th>
                                                <td>
                                                    <?= htmlspecialchars($record['department_name']) ?>
                                                    <input type="hidden" id="departmentSelect"
                                                        value="<?= $record['department_id'] ?>">
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>แพทย์เจ้าของไข้</th>
                                                <td><?= htmlspecialchars($record['staff_name']) ?></td>
                                            </tr>
                                            <tr>
                                                <th>วันที่รับเข้า</th>
                                                <td><?= htmlspecialchars($record['create_at']) ?></td>
                                            </tr>
                                            <tr>
                                                <th>หมายเหตุ</th>
                                                <td class="text-danger"><?= htmlspecialchars($record['remark']) ?></td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- สรุปสถานะเอกสาร -->
                            <div class="card card-box bg-white">
                                <div class="card-header bg-white border-0 pt-3 pb-0">
                                    <h5 class="fw-bold" style="color: var(--primary-deep);">สรุปสถานะการดำเนินการ</h5>
                                </div>
                                <div class="card-body">

                                    <?php 
                                    // --- PHP Logic: กำหนดสีและไอคอนตามสถานะ ---
                                    $status = $record['status_name'] ?? '-';
                                    
                                    // ค่าเริ่มต้น (Default)
                                    $statusClass = 'bg-light text-secondary border-secondary'; 
                                    $statusIcon = 'fa-question-circle';

                                    if ($status == 'เสร็จสิ้น') {
                                        $statusClass = 'bg-success text-white '; 
                                        $statusIcon = 'fa-check-circle';
                                    } elseif ($status == 'ล่าช้า') {
                                        $statusClass = 'bg-danger text-white '; 
                                        $statusIcon = 'fa-exclamation-triangle';
                                    } elseif ($status == 'รอสรุป') {
                                        $statusClass = 'bg-warning text-white'; 
                                        $statusIcon = 'fa-clock';
                                    } elseif ($status == 'ยังไม่ส่งสรุป') {
                                        $statusClass = 'bg-secondary text-white'; 
                                        $statusIcon = 'fa-hourglass-start';
                                    }
                                ?>

                                    <div class="p-4 rounded-3 text-center mb-3 border <?= $statusClass ?>"
                                        style="transition: all 0.3s ease;">

                                        <h6 class="mb-2 opacity-75 small text-uppercase" style="letter-spacing: 1px;">
                                            สถานะปัจจุบัน
                                        </h6>

                                        <h2 class="m-0 fw-bold d-flex align-items-center justify-content-center gap-2">
                                            <i class="fas <?= $statusIcon ?>"></i>
                                            <?= htmlspecialchars($status) ?>
                                        </h2>
                                    </div>

                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            1. แพทย์ Resident
                                            <?php 
                                            $status = $record['resident_status'];
                                            $badgeColor = 'badge bg-secondary text-white rounded-pill';
                                            if($status == 'เสร็จสิ้น') $badgeColor = 'badge bg-success rounded-pill text-white';
                                            elseif($status == 'ล่าช้า') $badgeColor = 'badge bg-danger rounded-pill text-white';
                                            elseif($status == 'รอสรุป') $badgeColor = 'badge bg-warning rounded-pill text-dark ';
                                            elseif($status == 'ยังไม่ส่งสรุป') $badgeColor = 'badge bg-secondary rounded-pill text-white';
                                        ?>
                                            <span
                                                class="<?= $badgeColor ?>"><?= htmlspecialchars($record['resident_status']) ?></span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            2. แพทย์เจ้าของไข้ (Staff)
                                            <?php 
                                            $status = $record['staff_status'];
                                            $badgeColor = 'badge bg-secondary text-white rounded-pill';
                                            if($status == 'เสร็จสิ้น') $badgeColor = 'badge bg-success rounded-pill text-white';
                                            elseif($status == 'ล่าช้า') $badgeColor = 'badge bg-danger rounded-pill text-white';
                                            elseif($status == 'รอสรุป') $badgeColor = 'badge bg-warning rounded-pill text-dark ';
                                            elseif($status == 'ยังไม่ส่งสรุป') $badgeColor = 'badge bg-secondary rounded-pill text-white';
                                        ?>
                                            <span
                                                class="<?= $badgeColor ?>"><?= htmlspecialchars($record['staff_status']) ?></span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            3. ส่งเวชระเบียน
                                            <?php 
                                            $status = $record['medical_records_status'];
                                            $badgeColor = 'badge bg-secondary text-white rounded-pill';
                                            if($status == 'เสร็จสิ้น') $badgeColor = 'badge bg-success rounded-pill text-white';
                                            elseif($status == 'ล่าช้า') $badgeColor = 'badge bg-danger rounded-pill text-white';
                                            elseif($status == 'รอสรุป') $badgeColor = 'badge bg-warning rounded-pill text-dark ';
                                            elseif($status == 'ยังไม่ส่งสรุป') $badgeColor = 'badge bg-secondary rounded-pill text-white';
                                        ?>
                                            <span
                                                class="<?= $badgeColor ?>"><?= htmlspecialchars($record['medical_records_status']) ?></span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            4. แพทย์ผู้ตรวจสอบ (Auditor)
                                            <?php 
                                            $status = $record['auditor_status'];
                                            $badgeColor = 'badge bg-secondary text-white rounded-pill';
                                            if($status == 'เสร็จสิ้น') $badgeColor = 'badge bg-success rounded-pill text-white';
                                            elseif($status == 'ล่าช้า') $badgeColor = 'badge bg-danger rounded-pill text-white';
                                            elseif($status == 'รอสรุป') $badgeColor = 'badge bg-warning rounded-pill text-dark ';
                                            elseif($status == 'ยังไม่ส่งสรุป') $badgeColor = 'badge bg-secondary rounded-pill text-white';
                                        ?>
                                            <span
                                                class="<?= $badgeColor ?>"><?= htmlspecialchars($record['auditor_status']) ?></span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- ลำดับขั้นการตรวจสอบเอกสาร -->
                        <div class="col-md-7">
                            <h4 class="mb-4 fw-bold border-bottom pb-2" style="color: var(--primary-deep);">
                                ลำดับขั้นการตรวจสอบเอกสาร</h4>

                            <!-- 1. แพทย์ Resident -->
                            <div class="card card-box mb-4 border-blue-theme">
                                <div class="card-body">
                                    <div class="step-header d-flex justify-content-between align-items-center"
                                        style="cursor: pointer;" data-bs-toggle="collapse"
                                        data-bs-target="#collapseResident" aria-expanded="true"
                                        aria-controls="collapseResident">

                                        <h5 class="mb-0 fw-bold" style="color: var(--primary-deep);">1. แพทย์ Resident
                                        </h5>
                                        <i class="fas fa-chevron-down text-muted"></i>
                                    </div>

                                    <hr class="text-muted my-2">
                                    <!-- <div class="collapse" id="collapseResident"> -->
                                    <?php 
                                        $collapse = ($record['resident_complete'] == 'เสร็จสิ้น') ? 'collapse' : '';
                                    ?>

                                    <div class= "<?= $collapse ?>" id="collapseResident">
                                        <div class="pt-2">
                                            <div class="input-group mb-3">
                                                <span class="input-group-text fixed-label"><i
                                                        class="fas fa-user-md me-2"></i>แพทย์</span>
                                                <select class="form-select" name="resident_id"
                                                    id="resident_DropDownAuditor"
                                                    data-selected="<?= $record['resident_id'] ?>">
                                                    <option value="<?= $record['resident_id'] ?>" selected>
                                                        <?= $record['resident_name'] ?></option>
                                                </select>
                                            </div>

                                            <div class="row g-2 mb-3">
                                                <div class="col-md-4">
                                                    <div class="input-group">
                                                        <span
                                                            class="input-group-text w-100 justify-content-center">วันที่ส่งสรุปผล</span>
                                                    </div>
                                                    <input type="date" class="form-control text-center"
                                                        id="resident_create_at" name="resident_create_at"
                                                        value="<?= ($record && $record['resident_create_at'] != '-' && !empty($record['resident_create_at'])) ? htmlspecialchars($record['resident_create_at']) : date('Y-m-d') ?>">
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="input-group">
                                                        <span
                                                            class="input-group-text w-100 justify-content-center">ระยะเวลา
                                                            (วัน)</span>
                                                    </div>
                                                    <input type="number" class="form-control text-center fw-bold"
                                                        style="color: var(--primary-main);" id="resident_duration"
                                                        name="resident_duedate"
                                                        value="<?= $record ? htmlspecialchars($record['resident_duedate']) : '' ?>">
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="input-group">
                                                        <span
                                                            class="input-group-text w-100 justify-content-center bg-light">กำหนดส่งคืน</span>
                                                    </div>
                                                    <input type="date" class="form-control text-center bg-light"
                                                        id="resident_return_date" readonly>
                                                </div>
                                            </div>

                                            <div class="d-flex justify-content-center">
                                                <?php 
                                                    $resStatus = $record['id'] ?? '-';
                                                    $disabled = ($resStatus == '-' || empty($resStatus)) ? 'disabled' : '';
                                                ?>
                                                <button type="submit" name="action" value="resident_start"
                                                    class="btn btn-deep-blue shadow" <?= $disabled ?> >
                                                    <i class="fas fa-paper-plane me-2"></i> บันทึกส่งสรุปผล (Resident)
                                                </button>
                                            </div>

                                            <hr class="text-muted my-3">

                                            <div class="row g-2 align-items-end">
                                                <div class="col-md-6">
                                                    <label class="small text-muted mb-1">สถานะการดำเนินการ</label>
                                                    <div class="input-group">
                                                        <input type="text" class="form-control" name="resident_status_name"
                                                            value="<?= $record ? htmlspecialchars($record['resident_status'] ?? '-') : '' ?>"
                                                            readonly>

                                                        <button type="button" class="btn btn-outline-secondary"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#editResidentStatusModal">
                                                            <i class="fas fa-edit"></i> แก้ไข
                                                        </button>
                                                    </div>
                                                </div>

                                                <div class="col-md-3">
                                                    <label class="small text-muted mb-1">วันที่ส่งคืน</label>
                                                    <input type="date" class="form-control" name="date_resident_complete"
                                                        value="<?= $record ? htmlspecialchars($record['resident_complete_at']) : '' ?>">
                                                </div>
                                                <div class="col-md-3">
                                                    <?php 
                                                        $resStatus = $record['resident_status'] ?? ''; 
                                                        $disabled = ($resStatus == 'ยังไม่ส่งสรุป' || $resStatus == '-') ? 'disabled' : '';
                                                    ?>
                                                    <button type="submit" name="action" value="resident_finish"
                                                        class="btn btn-success-custom w-100" <?= $disabled ?>>
                                                        <i class="fas fa-check-circle"></i> เสร็จสิ้น
                                                    </button>
                                                </div>
                                            </div>

                                            <div class="mt-2 text-end">
                                                
                                                <small class="text-muted">หมายเหตุ: <span
                                                        class="fw-bold text-primary"><?= ($record['resident_complete'] ?? '-') ?></span></small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- 2. แพทย์เจ้าของไข้ (Staff) -->
                            <div class="card card-box mb-4 border-green-theme">
                                <div class="card-body">

                                    <div class="step-header d-flex justify-content-between align-items-center"
                                        style="cursor: pointer;" data-bs-toggle="collapse"
                                        data-bs-target="#collapseStaff" aria-expanded="true"
                                        aria-controls="collapseStaff">

                                        <h5 class="mb-0 fw-bold" style="color: var(--primary-deep);">
                                            2. แพทย์เจ้าของไข้ (Staff)
                                        </h5>
                                        <i class="fas fa-chevron-down text-muted"></i>
                                    </div>

                                    <hr class="text-muted my-2"><?php 
                                        $collapse = ($record['staff_complete'] == 'เสร็จสิ้น') ? 'collapse' : '';
                                    ?>
                                    <div class= "<?= $collapse ?>" id="collapseStaff">
                                        <div class="pt-3">
                                            <div class="input-group mb-3">
                                                <span class="input-group-text fixed-label"><i
                                                        class="fas fa-user-md me-2"></i>แพทย์</span>
                                                <select class="form-select" name="staff_id" id="staff_DropDownAuditor"
                                                    data-selected="<?= $record['staff_id'] ?>">
                                                    <option value="<?= $record['staff_id'] ?>" selected>
                                                        <?= $record['staff_name'] ?></option>
                                                </select>
                                            </div>

                                            <div class="row g-2 mb-3">
                                                <div class="col-md-4">
                                                    <div class="input-group"><span
                                                            class="input-group-text w-100 justify-content-center">วันที่ส่งสรุปผล</span>
                                                    </div>
                                                    <input type="date" class="form-control text-center"
                                                        id="staff_create_at" name="staff_create_at"
                                                        value="<?= ($record && $record['staff_create_at'] != '-' && !empty($record['staff_create_at'])) ? htmlspecialchars($record['staff_create_at']) : date('Y-m-d') ?>">
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="input-group"><span
                                                            class="input-group-text w-100 justify-content-center">ระยะเวลา
                                                            (วัน)</span></div>
                                                    <input type="number" class="form-control text-center fw-bold"
                                                        style="color: var(--primary-main);" id="staff_duration"
                                                        name="staff_duedate"
                                                        value="<?= $record ? htmlspecialchars($record['staff_duedate']) : '' ?>">
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="input-group"><span
                                                            class="input-group-text w-100 justify-content-center bg-light">กำหนดส่งคืน</span>
                                                    </div>
                                                    <input type="date" class="form-control text-center bg-light"
                                                        id="staff_return_date" readonly>
                                                </div>
                                            </div>

                                            <div class="d-flex justify-content-center">
                                                <?php 
                                                    $resStatus = $record['resident_complete'];
                                                    $disabled = ($resStatus == 'เสร็จสิ้น' || empty($resStatus)) ? '' : 'disabled';
                                                ?>
                                                <button type="submit" name="action" value="staff_start"
                                                    class="btn btn-deep-blue shadow" <?= $disabled ?>>
                                                    <i class="fas fa-paper-plane me-2"></i> บันทึกส่งสรุปผล (Staff)
                                                </button>
                                            </div>

                                            <hr class="text-muted my-3">

                                            <div class="row g-2 align-items-end">
                                                <div class="col-md-6">
                                                    <label class="small text-muted mb-1">สถานะการดำเนินการ</label>
                                                    <div class="input-group">
                                                        <input type="text" class="form-control" name = "staff_status_name"
                                                            value="<?= $record ? htmlspecialchars($record['staff_status']) : '' ?>"
                                                            readonly>

                                                        <button type="button" class="btn btn-outline-secondary"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#editStaffStatusModal">
                                                            <i class="fas fa-edit"></i> แก้ไข
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="small text-muted mb-1">วันที่ส่งคืน</label>
                                                    <input type="date" class="form-control" name="date_staff_complete"
                                                        value="<?= $record ? htmlspecialchars($record['staff_complete_at']) : '' ?>">
                                                </div>
                                                <div class="col-md-3">
                                                    <?php 
                                                        $resStatus = $record['staff_status'] ?? 'ยังไม่ส่งสรุป';
                                                        $disabled = ($resStatus == 'ยังไม่ส่งสรุป' || $resStatus == '-') ? 'disabled' : '';
                                                    ?>
                                                    <button type="submit" name="action" value="staff_finish"
                                                        class="btn btn-success-custom w-100" <?= $disabled ?>>
                                                        <i class="fas fa-check-circle"></i> เสร็จสิ้น
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="mt-2 text-end">
                                                <small class="text-muted">หมายเหตุ: 
                                                    <span class="fw-bold text-primary">
                                                        <?= $record ? htmlspecialchars($record['staff_complete']) : '-' ?>
                                                    </span>
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- 3. ส่งเวชระเบียน -->
                            <div class="card card-box mb-4 border-yellow-theme">
                                <div class="card-body">

                                    <div class="step-header d-flex justify-content-between align-items-center"
                                        style="cursor: pointer;" data-bs-toggle="collapse"
                                        data-bs-target="#collapseMedical" aria-expanded="true"
                                        aria-controls="collapseMedical">

                                        <h5 class="mb-0 fw-bold" style="color: var(--primary-deep);">
                                            3. ส่งเวชระเบียน
                                        </h5>
                                        <i class="fas fa-chevron-down text-muted"></i>
                                    </div>

                                    <hr class="text-muted my-2">
                                    <?php 
                                        $collapse = ($record['medical_records_complete'] == 'เสร็จสิ้น') ? 'collapse' : '';
                                    ?>
                                    <div class= "<?= $collapse ?>" id="collapseMedical">
                                        <div class="pt-3">
                                            <div class="row g-2 mb-3">
                                                <div class="col-md-4">
                                                    <div class="input-group"><span
                                                            class="input-group-text w-100 justify-content-center">วันที่ส่งสรุปผล</span>
                                                    </div>
                                                    <input type="date" class="form-control text-center"
                                                        id="medical_records_create_at" name="medical_records_create_at"
                                                        value="<?= ($record && $record['medical_records_create_at'] != '-' && !empty($record['medical_records_create_at'])) ? htmlspecialchars($record['medical_records_create_at']) : date('Y-m-d') ?>">
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="input-group"><span
                                                            class="input-group-text w-100 justify-content-center">ระยะเวลา
                                                            (วัน)</span></div>
                                                    <input type="number" class="form-control text-center fw-bold"
                                                        style="color: var(--primary-main);"
                                                        id="medical_records_duration" name="medical_records_duedate"
                                                        value="<?= $record ? htmlspecialchars($record['medical_records_duedate']) : '' ?>">
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="input-group"><span
                                                            class="input-group-text w-100 justify-content-center bg-light">กำหนดส่งคืน</span>
                                                    </div>
                                                    <input type="date" class="form-control text-center bg-light"
                                                        id="medical_records_return_date" readonly>
                                                </div>
                                            </div>

                                            <div class="d-flex justify-content-center">
                                                <?php 
                                                    $resStatus = $record['staff_complete'] ?? 'เสร็จสิ้น';
                                                    $disabled = ($resStatus == 'เสร็จสิ้น' || empty($resStatus)) ? '' : 'disabled';
                                                ?>
                                                <button type="submit" name="action" value="medical_records_start"
                                                    class="btn btn-deep-blue shadow" <?= $disabled ?>>
                                                    <i class="fas fa-paper-plane me-2"></i> บันทึกส่งเวชระเบียน
                                                </button>
                                            </div>

                                            <hr class="text-muted my-3">

                                            <div class="row g-2 align-items-end">
                                                <div class="col-md-6">
                                                    <label class="small text-muted mb-1">สถานะการดำเนินการ</label>
                                                    <div class="input-group">
                                                        <input type="text" class="form-control" name = "medical_records_status_name"
                                                            value="<?= $record ? htmlspecialchars($record['medical_records_status']) : '' ?>"
                                                            readonly>

                                                        <button type="button" class="btn btn-outline-secondary"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#editMedicalRecordsStatusModal">
                                                            <i class="fas fa-edit"></i> แก้ไข
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="small text-muted mb-1">วันที่ส่งคืน</label>
                                                    <input type="date" class="form-control" name="date_medical_records_complete"
                                                        value="<?= $record ? htmlspecialchars($record['medical_records_complete_at']) : '' ?>">
                                                </div>
                                                <div class="col-md-3">
                                                    <?php 
                                                        $resStatus = $record['medical_records_status'] ?? 'ยังไม่ส่งสรุป';
                                                        $disabled = ($resStatus == 'ยังไม่ส่งสรุป' || $resStatus == '-') ? 'disabled' : '';
                                                    ?>
                                                    <button type="submit" name="action" value="medical_records_finish"
                                                        class="btn btn-success-custom w-100" <?= $disabled ?>>
                                                        <i class="fas fa-check-circle"></i> เสร็จสิ้น
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="mt-2 text-end">
                                                <small class="text-muted">หมายเหตุ: <span
                                                        class="fw-bold text-primary"><?= $record ? htmlspecialchars($record['medical_records_complete']) : '-' ?></span></small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- 4. แพทย์ผู้ตรวจสอบ (Auditor)-->
                            <div class="card card-box mb-4 border-red-theme">
                                <div class="card-body">

                                    <div class="step-header d-flex justify-content-between align-items-center"
                                        style="cursor: pointer;" data-bs-toggle="collapse"
                                        data-bs-target="#collapseAuditor" aria-expanded="true"
                                        aria-controls="collapseAuditor">

                                        <h5 class="mb-0 fw-bold" style="color: var(--primary-deep);"> 4. แพทย์ผู้ตรวจสอบ (Auditor) </h5>
                                        <i class="fas fa-chevron-down text-muted"></i>
                                    </div>

                                    <hr class="text-muted my-2">
                                    <?php 
                                        $collapse = ($record['auditor_complete'] == 'เสร็จสิ้น') ? 'collapse' : '';
                                    ?>
                                    <div class= "<?= $collapse ?>" id="collapseAuditor">
                                        <div class="pt-3">
                                            <div class="input-group mb-3">
                                                <span class="input-group-text fixed-label"><i
                                                        class="fas fa-user-check me-2"></i>แพทย์</span>
                                                <select class="form-select" name="auditor_id"
                                                    id="auditor_DropDownAuditor"
                                                    data-selected="<?= $record['auditor_id'] ?>">
                                                    <option value="<?= $record['auditor_id'] ?>" selected>
                                                        <?= $record['auditor_name'] ?></option>
                                                </select>
                                            </div>

                                            <div class="row g-2 mb-3">
                                                <div class="col-md-4">
                                                    <div class="input-group"><span
                                                            class="input-group-text w-100 justify-content-center">วันที่ส่งสรุปผล</span>
                                                    </div>
                                                    <input type="date" class="form-control text-center"
                                                        id="auditor_create_at" name="auditor_create_at"
                                                        value="<?= ($record && $record['auditor_create_at'] != '-' && !empty($record['auditor_create_at'])) ? htmlspecialchars($record['auditor_create_at']) : date('Y-m-d') ?>">
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="input-group"><span
                                                            class="input-group-text w-100 justify-content-center">ระยะเวลา
                                                            (วัน)</span></div>
                                                    <input type="number" class="form-control text-center fw-bold"
                                                        style="color: var(--primary-main);" id="auditor_duration"
                                                        name="auditor_duedate"
                                                        value="<?= $record ? htmlspecialchars($record['auditor_duedate']) : '' ?>">
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="input-group"><span
                                                            class="input-group-text w-100 justify-content-center bg-light">กำหนดส่งคืน</span>
                                                    </div>
                                                    <input type="date" class="form-control text-center bg-light"
                                                        id="auditor_return_date" readonly>
                                                </div>
                                            </div>

                                            <div class="d-flex justify-content-center">
                                                <?php 
                                                    $resStatus = $record['medical_records_complete'] ?? 'เสร็จสิ้น';
                                                    $disabled = ($resStatus == 'เสร็จสิ้น' || empty($resStatus)) ? '' : 'disabled';
                                                ?>
                                                <button type="submit" name="action" value="auditor_start"
                                                    class="btn btn-deep-blue shadow" <?= $disabled ?>>
                                                    <i class="fas fa-paper-plane me-2"></i> บันทึกส่ง Auditor
                                                </button>
                                            </div>

                                            <hr class="text-muted my-3">

                                            <div class="row g-2 align-items-end">
                                                <div class="col-md-6">
                                                    <label class="small text-muted mb-1">สถานะการดำเนินการ</label>
                                                    <div class="input-group">
                                                        <input type="text" class="form-control" name="auditor_status_name"
                                                            value="<?= $record ? htmlspecialchars($record['auditor_status']) : '' ?>"
                                                            readonly>

                                                        <button type="button" class="btn btn-outline-secondary"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#editAuditorStatusModal">
                                                            <i class="fas fa-edit"></i> แก้ไข
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="small text-muted mb-1">วันที่ส่งคืน</label>
                                                    <input type="date" class="form-control" name="date_auditor_complete"
                                                        value="<?= $record ? htmlspecialchars($record['auditor_complete_at']) : '' ?>">
                                                </div>
                                                <div class="col-md-3">
                                                    <?php 
                                                        $resStatus = $record['auditor_status'] ?? 'ยังไม่ส่งสรุป';
                                                        $disabled = ($resStatus == 'ยังไม่ส่งสรุป' || $resStatus == '-') ? 'disabled' : '';
                                                    ?>
                                                    <button type="submit" name="action" value="auditor_finish"
                                                        class="btn btn-success-custom w-100" <?= $disabled ?>>
                                                        <i class="fas fa-check-circle"></i> เสร็จสิ้น
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="mt-2 text-end">
                                                <small class="text-muted">สถานะเอกสาร: <span
                                                        class="fw-bold text-primary"><?= $record ? htmlspecialchars($record['auditor_complete']) : '-' ?></span></small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editResidentStatusModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-light">
                    <h5 class="modal-title"><i class="fas fa-edit me-2"></i>แก้ไขสถานะ (Resident)</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form method="POST">
                    <div class="modal-body">
                        <p class="text-muted small">โปรดเลือกสถานะที่ต้องการแก้ไข (กรณีบันทึกผิดพลาด)</p>

                        <div class="mb-3">
                            <label class="form-label fw-bold">เลือกสถานะใหม่</label>
                            <select class="form-select" name="new_status">
                                <option value="1">ยังไม่ส่งสรุป</option>
                                <!-- <option value="2">รอสรุป</option>
                                <option value="3">ล่าช้า</option>
                                <option value="4">เสร็จสิ้น</option> -->
                            </select>
                        </div>

                        <input type="hidden" name="action" value="resident_manual_update">
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                        <button type="submit" class="btn btn-primary">บันทึกการแก้ไข</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- staff status modal -->
    <div class="modal fade" id="editStaffStatusModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-light">
                    <h5 class="modal-title"><i class="fas fa-edit me-2"></i>แก้ไขสถานะ (Resident)</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form method="POST">
                    <div class="modal-body">
                        <p class="text-muted small">โปรดเลือกสถานะที่ต้องการแก้ไข (กรณีบันทึกผิดพลาด)</p>

                        <div class="mb-3">
                            <label class="form-label fw-bold">เลือกสถานะใหม่</label>
                            <select class="form-select" name="staff_new_status">
                                <option value="1">ยังไม่ส่งสรุป</option>
                                <!-- <option value="2">รอสรุป</option>
                                <option value="3">ล่าช้า</option>
                                <option value="4">เสร็จสิ้น</option> -->
                            </select>
                        </div>

                        <input type="hidden" name="action" value="staff_manual_update">
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                        <button type="submit" class="btn btn-primary">บันทึกการแก้ไข</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- medical_records status modal -->
    <div class="modal fade" id="editMedicalRecordsStatusModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-light">
                    <h5 class="modal-title"><i class="fas fa-edit me-2"></i>แก้ไขสถานะ (Resident)</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form method="POST">
                    <div class="modal-body">
                        <p class="text-muted small">โปรดเลือกสถานะที่ต้องการแก้ไข (กรณีบันทึกผิดพลาด)</p>

                        <div class="mb-3">
                            <label class="form-label fw-bold">เลือกสถานะใหม่</label>
                            <select class="form-select" name="medical_records_new_status">
                                <option value="1">ยังไม่ส่งสรุป</option>
                                <!-- <option value="2">รอสรุป</option>
                                <option value="3">ล่าช้า</option>
                                <option value="4">เสร็จสิ้น</option> -->
                            </select>
                        </div>

                        <input type="hidden" name="action" value="medical_records_manual_update">
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                        <button type="submit" class="btn btn-primary">บันทึกการแก้ไข</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- auditor status modal -->
    <div class="modal fade" id="editAuditorStatusModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-light">
                    <h5 class="modal-title"><i class="fas fa-edit me-2"></i>แก้ไขสถานะ (Resident)</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form method="POST">
                    <div class="modal-body">
                        <p class="text-muted small">โปรดเลือกสถานะที่ต้องการแก้ไข (กรณีบันทึกผิดพลาด)</p>

                        <div class="mb-3">
                            <label class="form-label fw-bold">เลือกสถานะใหม่</label>
                            <select class="form-select" name="auditor_new_status">
                                <option value="1">ยังไม่ส่งสรุป</option>
                                <!-- <option value="2">รอสรุป</option>
                                <option value="3">ล่าช้า</option>
                                <option value="4">เสร็จสิ้น</option> -->
                            </select>
                        </div>

                        <input type="hidden" name="action" value="auditor_manual_update">
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                        <button type="submit" class="btn btn-primary">บันทึกการแก้ไข</button>
                    </div>
                </form>
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
    $(document).ready(function() {
        function setupAutoDateCalculation(startId, durationId, returnId) {

            function calculate() {
                var startDateStr = $(startId).val();
                var durationStr = $(durationId).val();

                // 1. เช็คเงื่อนไข: ถ้าเป็นค่าว่าง หรือ เป็นเครื่องหมาย '-' ให้เคลียร์ค่าวันส่งคืน แล้วจบ
                if (durationStr === '' || durationStr === '-') {
                    $(returnId).val('');
                    return;
                }
                var duration = parseInt(durationStr);
                if (startDateStr && !isNaN(duration)) {
                    var startDate = new Date(startDateStr);
                    startDate.setDate(startDate.getDate() + duration);

                    var day = ("0" + startDate.getDate()).slice(-2);
                    var month = ("0" + (startDate.getMonth() + 1)).slice(-2);
                    var year = startDate.getFullYear();

                    var returnDate = year + "-" + month + "-" + day;
                    $(returnId).val(returnDate);
                } else {
                    $(returnId).val('');
                }
            }

            $(startId + ', ' + durationId).on('change keyup input', function() {
                calculate();
            });

            // Run on init
            calculate();
        }

        setupAutoDateCalculation('#resident_create_at', '#resident_duration', '#resident_return_date');
        setupAutoDateCalculation('#staff_create_at', '#staff_duration', '#staff_return_date');
        setupAutoDateCalculation('#medical_records_create_at', '#medical_records_duration',
            '#medical_records_return_date');
        setupAutoDateCalculation('#auditor_create_at', '#auditor_duration', '#auditor_return_date');

    });

    // Script สำหรับดึงข้อมูล Auditor ตามหอผู้ป่วย
    document.addEventListener("DOMContentLoaded", function() {

        function fetchAuditors(deptId) {
            const dropdowns = [
                document.getElementById('resident_DropDownAuditor'),
                document.getElementById('staff_DropDownAuditor'),
                document.getElementById('auditor_DropDownAuditor')
            ];

            const validDropdowns = dropdowns.filter(d => d !== null);
            if (validDropdowns.length === 0) return;

            validDropdowns.forEach(dropdown => {
                const currentSelected = dropdown.getAttribute('data-selected') || dropdown.value;
                dropdown.setAttribute('data-temp-selected', currentSelected);
                dropdown.innerHTML = '<option value="">กำลังโหลด...</option>';
                dropdown.disabled = true;
            });

            fetch('db/db-auditor-by-department.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: `dept_id=${deptId}`
                })
                .then(response => response.json())
                .then(data => {
                    validDropdowns.forEach(dropdown => {
                        dropdown.innerHTML = '<option value="">- เลือกแพทย์ -</option>';
                        const savedValue = dropdown.getAttribute('data-temp-selected');

                        data.forEach(item => {
                            const opt = document.createElement('option');
                            opt.value = item.auditor_id;
                            opt.textContent = item.auditor_name;
                            if (String(item.auditor_id) === String(savedValue)) {
                                opt.selected = true;
                            }
                            dropdown.appendChild(opt);
                        });
                        dropdown.disabled = false;
                    });
                })
                .catch(error => {
                    console.error('Error fetching auditors:', error);
                    validDropdowns.forEach(dropdown => {
                        dropdown.innerHTML = '<option>ไม่พบข้อมูล</option>';
                        dropdown.disabled = false;
                    });
                });
        }

        const initialDeptId = document.getElementById('departmentSelect') ? document.getElementById(
            'departmentSelect').value : null;
        if (initialDeptId) {
            fetchAuditors(initialDeptId);
        }
    });
    </script>
</body>

</html>