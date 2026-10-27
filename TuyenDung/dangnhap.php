<?php
session_start();
require_once 'config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $mat_khau = trim($_POST['mat_khau']);

    if (empty($email) || empty($mat_khau)) {
        $error = "Vui lòng nhập đầy đủ thông tin!";
    } else {
        $stmt = $conn->prepare("SELECT * FROM NguoiDung WHERE Email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();
            if ($mat_khau === $user['Mat_khau'] || password_verify($mat_khau, $user['Mat_khau'])) {
                $_SESSION['user_id'] = $user['ID'];
                $_SESSION['ho_ten'] = $user['Ho_ten'];
                $_SESSION['vai_tro'] = $user['Vai_tro'];
                
                header("Location: index.php");
                exit();
            } else {
                $error = "Mật khẩu không chính xác!";
            }
        } else {
            $error = "Email không tồn tại trong hệ thống!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng Nhập - Cổng Tuyển Dụng</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body class="bg-light d-flex align-items-center justify-content-center" style="min-height: 100vh;">

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow-sm border-0 p-4">
                <div class="text-center mb-4">
                    <h3 class="fw-bold" style="color: var(--primary-orange);">Tuyển Dụng CT428</h3>
                    <p class="text-secondary small">Đăng nhập vào hệ thống quản lý tuyển dụng</p>
                </div>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger text-center py-2 small" role="alert">
                        <?= $error ?>
                    </div>
                <?php endif; ?>

                <form action="dangnhap.php" method="POST">
                    <div class="mb-3">
                        <label for="email" class="form-label fw-semibold">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required placeholder="Nhập email của bạn">
                    </div>
                    
                    <div class="mb-4">
                        <label for="mat_khau" class="form-label fw-semibold">Mật khẩu</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="mat_khau" name="mat_khau" required placeholder="Nhập mật khẩu">
                            <button class="btn btn-outline-secondary" type="button" id="togglePassword">Hiện</button>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-2 mb-3">Đăng Nhập</button>
                </form>

                <div class="text-center small text-secondary">
                    Chưa có tài khoản? <a href="dangky.php" class="text-decoration-none fw-semibold" style="color: var(--primary-orange);">Đăng ký ngay</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Script xử lý ẩn/hiện mật khẩu -->
<script>
const togglePassword = document.querySelector('#togglePassword');
const password = document.querySelector('#mat_khau');

togglePassword.addEventListener('click', function () {
    const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
    password.setAttribute('type', type);
    this.textContent = type === 'password' ? 'Hiện' : 'Ẩn';
});
</script>

</body>
</html>