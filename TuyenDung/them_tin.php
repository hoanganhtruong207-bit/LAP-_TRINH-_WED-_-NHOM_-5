<?php
session_start();
require_once 'config.php';

// Kiểm tra quyền: Phải là nhà tuyển dụng
if (!isset($_SESSION['user_id']) || $_SESSION['vai_tro'] != 'nha_tuyen_dung') {
    header("Location: dangnhap.php");
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tieu_de = trim($_POST['tieu_de']);
    $mo_ta = trim($_POST['mo_ta']);
    $yeu_cau = trim($_POST['yeu_cau']);
    $dia_diem = trim($_POST['dia_diem']);
    $han_nop = trim($_POST['han_nop']);
    $id_nha_tuyen_dung = $_SESSION['user_id'];

    if (empty($tieu_de) || empty($mo_ta) || empty($yeu_cau) || empty($dia_diem) || empty($han_nop)) {
        $error = "Vui lòng nhập đầy đủ tất cả các trường!";
    } else {
        $stmt = $conn->prepare("INSERT INTO tintuyendung (Tieu_de, Mo_ta, Yeu_cau, Dia_diem, Han_nop, ID_NhaTuyenDung) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssi", $tieu_de, $mo_ta, $yeu_cau, $dia_diem, $han_nop, $id_nha_tuyen_dung);
        
        if ($stmt->execute()) {
            $success = "Đăng tin tuyển dụng thành công!";
        } else {
            $error = "Đã xảy ra lỗi, vui lòng thử lại!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng Tin Tuyển Dụng Mới</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body class="bg-light">

<!-- Thanh menu tông Cam - Trắng tối giản -->
<nav class="navbar navbar-expand-lg navbar-custom mb-4 py-3">
    <div class="container">
        <a class="navbar-brand fw-bold" href="quanly_tin.php" style="color: var(--primary-orange);">
            Quản Lý Tuyển Dụng
        </a>
        <div class="d-flex align-items-center">
            <span class="navbar-text me-3 text-secondary">
                Xin chào, <strong><?= htmlspecialchars($_SESSION['ho_ten']) ?></strong>!
            </span>
            <a href="quanly_tin.php" class="btn btn-secondary btn-sm me-2">Quay lại quản lý tin</a>
            <a href="dangxuat.php" class="btn btn-outline-danger btn-sm">Đăng xuất</a>
        </div>
    </div>
</nav>

<div class="container mb-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0 p-4">
                <h3 class="fw-bold mb-4" style="color: var(--text-dark);">Đăng Tin Tuyển Dụng Mới</h3>

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

                <form action="them_tin.php" method="POST">
                    <div class="mb-3">
                        <label for="tieu_de" class="form-label fw-semibold">Tiêu đề công việc</label>
                        <input type="text" class="form-control" id="tieu_de" name="tieu_de" required placeholder="Nhập tiêu đề công việc">
                    </div>

                    <div class="mb-3">
                        <label for="dia_diem" class="form-label fw-semibold">Địa điểm làm việc</label>
                        <input type="text" class="form-control" id="dia_diem" name="dia_diem" required placeholder="Nhập địa điểm làm việc">
                    </div>

                    <div class="mb-3">
                        <label for="han_nop" class="form-label fw-semibold">Hạn nộp hồ sơ</label>
                        <input type="date" class="form-control" id="han_nop" name="han_nop" required>
                    </div>

                    <div class="mb-3">
                        <label for="mo_ta" class="form-label fw-semibold">Mô tả công việc</label>
                        <textarea class="form-control" id="mo_ta" name="mo_ta" rows="4" required placeholder="Nhập chi tiết mô tả công việc"></textarea>
                    </div>

                    <div class="mb-4">
                        <label for="yeu_cau" class="form-label fw-semibold">Yêu cầu ứng viên</label>
                        <textarea class="form-control" id="yeu_cau" name="yeu_cau" rows="4" required placeholder="Nhập các yêu cầu đối với ứng viên"></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-2">Đăng Tin</button>
                </form>
            </div>
        </div>
    </div>
</div>

</body>
</html>