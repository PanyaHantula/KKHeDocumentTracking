<?php
    session_start();
    include __DIR__ . '/db/db-login.php'; // check over deu date
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>ระบบติดตามงานเอกสารโรงพยาบาล</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <?php include __DIR__ . '/css-link-library.php'; ?>
    <?php include __DIR__ . '/db/db-config.php'; ?>

    <style>
        /* --- Deep Blue (Navy) Theme Setup --- */
        :root {
            --primary-navy: #001f3f;       /* น้ำเงินเข้มหลัก */
            --primary-blue: #003366;       /* น้ำเงินรอง */
            --accent-light: #e7f1ff;       /* สีพื้นหลังอ่อนๆ สำหรับ Card */
            --text-dark: #2c3e50;          /* สีตัวอักษรเข้ม */
            --input-focus: #004085;        /* สีขอบตอนกด Input */
        }

        body {
            /* พื้นหลังไล่เฉดสีน้ำเงินเข้ม */
            background: linear-gradient(135deg, var(--primary-navy) 0%, var(--primary-blue) 100%);
            font-family: 'Sarabun', sans-serif;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* --- Login Card Design --- */
        .login-card {
            background: #ffffff;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3); /* เงาฟุ้งๆ */
            border: none;
            overflow: hidden;
            width: 100%;
            max-width: 450px; /* ความกว้างสูงสุดของการ์ด */
            padding: 2rem;
            position: relative;
        }

        /* Decoration Stripe on Top (แถบสีด้านบนการ์ด) */
        .login-card::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 6px;
            background: linear-gradient(to right, #001f3f, #007bff);
        }

        .logo-container img {
            max-width: 100%;
            height: auto;
            max-height: 100px;
            object-fit: contain;
        }

        /* --- Form Elements --- */
        .form-floating > .form-control:focus {
            border-color: var(--input-focus);
            box-shadow: 0 0 0 0.25rem rgba(0, 31, 63, 0.25);
        }

        .form-floating > label {
            color: #6c757d;
        }

        .btn-navy {
            background-color: var(--primary-navy);
            border-color: var(--primary-navy);
            color: white;
            font-weight: 600;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
        }

        .btn-navy:hover {
            background-color: #003366;
            border-color: #003366;
            transform: translateY(-1px);
            box-shadow: 0 4px 6px rgba(0,0,0,0.2);
        }

        .contact-text {
            font-size: 0.85rem;
            color: #6c757d;
        }
    </style>
</head>

<body>
    
    <div class="container d-flex justify-content-center align-items-center h-100">
        <div class="login-card fade-in-up">
            
            <div class="text-center mb-4">
                <div class="logo-container mb-3">
                    <img src="img/KhonKaenHospital.png" alt="Logo">
                </div>
                <h4 class="fw-bold" style="color: var(--primary-navy);">ระบบติดตามงานเอกสาร</h4>
                <p class="text-muted mb-0">โรงพยาบาลขอนแก่น</p>
            </div>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger alert-dismissible fade show text-center py-2" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i> <?= htmlspecialchars($error) ?>
                    <button type="button" class="btn-close py-2" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-floating mb-3">
                    <input type="text" class="form-control" id="UsernameInput" name="username"
                        placeholder="User Name" required>
                    <label for="UsernameInput"><i class="fas fa-user me-2"></i>User Name</label>
                </div>
                
                <div class="form-floating mb-4">
                    <input type="password" class="form-control" id="PWD_Input" name="password"
                        placeholder="Password" required>
                    <label for="PWD_Input"><i class="fas fa-lock me-2"></i>Password</label>
                </div>
                
                <button type="submit" class="btn btn-navy py-3 w-100 mb-4 shadow-sm">
                    เข้าสู่ระบบ (Sign In)
                </button>
            </form>

            <div class="text-center border-top pt-3">
                <p class="contact-text mb-0">หากพบปัญหาการเข้าใช้งานระบบ ติดต่อเจ้าหน้าที่</p>
                <p class="contact-text fw-bold mb-0" style="color: var(--primary-blue);">
                    <i class="fas fa-phone-alt me-1"></i> 044-000-000
                </p>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        $(document).ready(function() {
            $('.login-card').css({opacity: 0, marginTop: '20px'}).animate({opacity: 1, marginTop: '0'}, 600);
        });
    </script>

    <script src="js/main.js"></script>
</body>

</html>