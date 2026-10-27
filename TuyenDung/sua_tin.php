<?php
session_start();
require_once 'config.php';

// Kiểm tra quyền: Phải là nhà tuyển dụng
if (!isset($_SESSION['user_id']) || $_SESSION['vai_tro'] != 'nha_tuyen_dung') {
    header("Location: dangnhap.php");
    exit();
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$error = '';
$success = '';

// Lấy thông tin tin tuyển dụng cần sửa
$stmt = $conn->prepare("SELECT * FROM tintuyendung WHERE ID = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$tin = $stmt->get_result()->fetch_assoc();

if (!$tin) {
    header("Location: quanly_tin.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tieu_de = trim($_POST['tieu_de']);
    $mo_ta = trim($_POST['mo_ta']);
    $yeu_cau = trim($_POST['yeu_cau']);
    $dia_diem = trim($_POST['dia_diem']);
    $han_nop = trim($_POST['han_nop']);

    if (empty($tieu_de) || empty($mo_ta) || empty($yeu_cau) || empty($dia_diem) || empty($han_nop)) {
        $error = "Vui lòng nhập đầy đủ tất cả các trường!";
    } else {
        $update_stmt = $conn->prepare("UPDATE tintuyendung SET Tieu_de = ?, Mo_ta = ?, Yeu_cau = ?, Dia_diem = ?, Han_nop = ? WHERE ID = ?");
        $update_stmt->bind_param("sssssi", $tieu_de, $mo_ta, $yeu_cau, $dia_diem, $han_nop, $id);
        
        if ($update_stmt->execute()) {
            $success = "Cập nhật tin tuyển dụng thành công!";
            // Cập nhật lại dữ liệu hiển thị trên form
            $tin['Tieu_de'] = $tieu_de;
            $tin['Mo_ta'] = $mo_ta;
            $tin['Yeu_cau'] = $yeu_cau;
            $tin['Dia_diem'] = $dia_diem;
            $tin['Han_nop'] = $han_nop;
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
    <title>Chỉnh Sửa Tin Tuyển Dụng</title>
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
                <h3 class="fw-bold mb-4" style="color: var(--text-dark);">Chỉnh Sửa Tin Tuyển Dụng</h3>

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

                <form action="sua_tin.php?id=<?= $id ?>" method="POST">
                    <div class="mb-3">
                        <label for="tieu_de" class="form-label fw-semibold">Tiêu đề công việc</label>
                        <input type="text" class="form-control" id="tieu_de" name="tieu_de" value="<?= htmlspecialchars($tin['Tieu_de']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="dia_diem" class="form-label fw-semibold">Địa điểm làm việc</label>
                        <input type="text" class="form-control" id="dia_diem" name="dia_diem" value="<?= htmlspecialchars($tin['Dia_diem']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="han_nop" class="form-label fw-semibold">Hạn nộp hồ sơ</label>
                        <input type="date" class="form-control" id="han_nop" name="han_nop" value="<?= htmlspecialchars($tin['Han_nop']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="mo_ta" class="form-label fw-semibold">Mô tả công việc</label>
                        <textarea class="form-control" id="mo_ta" name="mo_ta" rows="4" required><?= htmlspecialchars($tin['Mo_ta']) ?></textarea>
                    </div>

                    <div class="mb-4">
                        <label for="yeu_cau" class="form-label fw-semibold">Yêu cầu ứng viên</label>
                        <textarea class="form-control" id="yeu_cau" name="yeu_cau" rows="4" required><?= htmlspecialchars($tin['Yeu_cau']) ?></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-2">Lưu Thay Đổi</button>
                </form>
            </div>
        </div>
    </div>
</div>

</body>
</html>