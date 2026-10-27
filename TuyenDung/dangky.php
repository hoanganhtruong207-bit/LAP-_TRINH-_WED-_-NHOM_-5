<?php
session_start();
require_once 'config.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $ho_ten = trim($_POST['ho_ten']);
    $email = trim($_POST['email']);
    $mat_khau = trim($_POST['mat_khau']);
    $vai_tro = trim($_POST['vai_tro']);

    if (empty($ho_ten) || empty($email) || empty($mat_khau) || empty($vai_tro)) {
        $error = "Vui lòng nhập đầy đủ thông tin!";
    } else {
        $stmt = $conn->prepare("SELECT ID FROM NguoiDung WHERE Email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $error = "Email này đã được sử dụng trong hệ thống!";
        } else {
            $hashed_password = password_hash($mat_khau, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO NguoiDung (Ho_ten, Email, Mat_khau, Vai_tro) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $ho_ten, $email, $hashed_password, $vai_tro);
            
            if ($stmt->execute()) {
                $success = "Đăng ký tài khoản thành công! Bạn có thể đăng nhập.";
            } else {
                $error = "Đã xảy ra lỗi, vui lòng thử lại!";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng Ký - Cổng Tuyển Dụng</title>
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
                    <p class="text-secondary small">Tạo tài khoản mới trên hệ thống</p>
                </div>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger text-center py-2 small" role="alert">
                        <?= $error ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($success)): ?>
                    <div class="alert alert-success text-center py-2 small" role="alert">
                        <?= $success ?>
                    </div>
                <?php endif; ?>

                <form action="dangky.php" method="POST">
                    <div class="mb-3">
                        <label for="ho_ten" class="form-label fw-semibold">Họ và tên</label>
                        <input type="text" class="form-control" id="ho_ten" name="ho_ten" required placeholder="Nhập họ và tên của bạn">
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label fw-semibold">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required placeholder="Nhập địa chỉ email">
                    </div>
                    
                    <div class="mb-3">
                        <label for="mat_khau" class="form-label fw-semibold">Mật khẩu</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="mat_khau" name="mat_khau" required placeholder="Nhập mật khẩu">
                            <button class="btn btn-outline-secondary" type="button" id="togglePassword">Hiện</button>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="vai_tro" class="form-label fw-semibold">Vai trò</label>
                        <select class="form-select" id="vai_tro" name="vai_tro" required>
                            <option value="ung_vien">Ứng viên tìm việc</option>
                            <option value="nha_tuyen_dung">Nhà tuyển dụng</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-2 mb-3">Đăng Ký</button>
                </form>

                <div class="text-center small text-secondary">
                    Đã có tài khoản? <a href="dangnhap.php" class="text-decoration-none fw-semibold" style="color: var(--primary-orange);">Đăng nhập</a>
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