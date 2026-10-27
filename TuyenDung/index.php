<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: dangnhap.php");
    exit();
}

$sql = "SELECT * FROM tintuyendung ORDER BY ID DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Trang Chủ - Cổng Tuyển Dụng</title>
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
            
            <!-- Nếu là ứng viên: hiện nút Lịch sử nộp CV -->
            <?php if(isset($_SESSION['vai_tro']) && $_SESSION['vai_tro'] == 'ung_vien'): ?>
                <a href="lichsu_ungtuyen.php" class="btn btn-secondary btn-sm me-2">Lịch sử nộp CV</a>
            <?php endif; ?>

            <!-- Nếu là nhà tuyển dụng: hiện nút đi đến trang Quản lý tin -->
            <?php if(isset($_SESSION['vai_tro']) && $_SESSION['vai_tro'] == 'nha_tuyen_dung'): ?>
                <a href="quanly_tin.php" class="btn btn-primary btn-sm me-2">Quản lý tin tuyển dụng</a>
            <?php endif; ?>
            
            <a href="dangxuat.php" class="btn btn-outline-danger btn-sm">Đăng xuất</a>
        </div>
    </div>
</nav>

<div class="container">
    <h2 class="text-center mb-4 fw-bold" style="color: var(--text-dark);">Việc Làm Mới Nhất</h2>
    
    <div class="row">
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="col-md-4 mb-4">
                    <div class="card shadow-sm h-100 border-0">
                        <div class="card-body">
                            <h5 class="card-title fw-bold" style="color: var(--primary-orange);"><?= htmlspecialchars($row['Tieu_de']) ?></h5>
                            <p class="card-text text-muted small mb-2">
                                Địa điểm: <?= htmlspecialchars($row['Dia_diem']) ?> <br>
                                Hạn nộp: <?= date('d/m/Y', strtotime($row['Han_nop'])) ?>
                            </p>
                            <p class="card-text">
                                <?= mb_substr(htmlspecialchars($row['Mo_ta']), 0, 100) ?>...
                            </p>
                        </div>
                        <div class="card-footer bg-white border-0 pb-3 text-center">
                            <?php if(isset($_SESSION['vai_tro']) && $_SESSION['vai_tro'] == 'ung_vien'): ?>
                                <a href="nop_cv.php?id_tin=<?= $row['ID'] ?>" class="btn btn-primary w-100">Xem & Nộp CV</a>
                            <?php else: ?>
                                <a href="quanly_tin.php" class="btn btn-secondary w-100">Đến trang quản lý</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="alert alert-warning text-center shadow-sm">
                    Hiện tại chưa có công việc nào được đăng.
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

</body>
</html>