<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['vai_tro'] != 'ung_vien') {
    header("Location: dangnhap.php");
    exit();
}

$id_tin = isset($_GET['id_tin']) ? intval($_GET['id_tin']) : 0;
$error = '';
$success = '';

$stmt = $conn->prepare("SELECT * FROM tintuyendung WHERE ID = ?");
$stmt->bind_param("i", $id_tin);
$stmt->execute();
$tin = $stmt->get_result()->fetch_assoc();

if (!$tin) {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_FILES['file_cv']) && $_FILES['file_cv']['error'] == 0) {
        $file = $_FILES['file_cv'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        
        $allowed = ['pdf', 'doc', 'docx'];
        if (in_array($ext, $allowed)) {
            $filename = 'cv_' . $_SESSION['user_id'] . '_' . time() . '.' . $ext;
            $upload_dir = 'uploads/';
            
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            if (move_uploaded_file($file['tmp_name'], $upload_dir . $filename)) {
                $file_path = $upload_dir . $filename;
                $ung_vien_id = $_SESSION['user_id'];
                
                $stmt = $conn->prepare("INSERT INTO hosoungtuyen (ID_UngVien, ID_TinTuyenDung, Duong_dan_CV, Trang_thai) VALUES (?, ?, ?, 'Đang chờ duyệt')");
                $stmt->bind_param("iis", $ung_vien_id, $id_tin, $file_path);
                
                if ($stmt->execute()) {
                    $success = "Nộp hồ sơ thành công!";
                } else {
                    $error = "Đã xảy ra lỗi khi lưu vào cơ sở dữ liệu!";
                }
            } else {
                $error = "Không thể tải file lên thư mục!";
            }
        } else {
            $error = "Chỉ chấp nhận file định dạng PDF, DOC hoặc DOCX!";
        }
    } else {
        $error = "Vui lòng chọn file CV của bạn!";
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Nộp Hồ Sơ Ứng Tuyển</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-custom mb-4 py-3">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php" style="color: var(--primary-orange);">
            Tuyển Dụng CT428
        </a>
        <div class="d-flex align-items-center">
            <span class="navbar-text me-3 text-secondary">
                Xin chào, <strong><?= htmlspecialchars($_SESSION['ho_ten']) ?></strong>!
            </span>
            <a href="index.php" class="btn btn-secondary btn-sm me-2">Quay lại trang chủ</a>
            <a href="dangxuat.php" class="btn btn-outline-danger btn-sm">Đăng xuất</a>
        </div>
    </div>
</nav>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0 p-4 mb-4">
                <h3 class="fw-bold mb-3" style="color: var(--primary-orange);"><?= htmlspecialchars($tin['Tieu_de']) ?></h3>
                <p class="text-secondary small mb-3">
                    Địa điểm: <?= htmlspecialchars($tin['Dia_diem']) ?> | 
                    Hạn nộp: <?= date('d/m/Y', strtotime($tin['Han_nop'])) ?>
                </p>
                <div class="border-top pt-3">
                    <h5 class="fw-semibold mb-2 text-dark">Mô tả công việc:</h5>
                    <p class="text-secondary" style="white-space: pre-line;"><?= htmlspecialchars($tin['Mo_ta']) ?></p>
                </div>
            </div>

            <div class="card shadow-sm border-0 p-4">
                <h4 class="fw-bold mb-3 text-dark">Gửi Hồ Sơ Ứng Tuyển</h4>

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

                <form action="nop_cv.php?id_tin=<?= $id_tin ?>" method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="file_cv" class="form-label fw-semibold">Tải lên CV của bạn (PDF, DOC, DOCX)</label>
                        <input type="file" class="form-control" id="file_cv" name="file_cv" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 py-2">Xác Nhận Nộp CV</button>
                </form>
            </div>
        </div>
    </div>
</div>

</body>
</html>