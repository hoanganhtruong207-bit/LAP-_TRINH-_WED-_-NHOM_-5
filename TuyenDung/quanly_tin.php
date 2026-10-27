<?php
session_start();
require_once 'config.php';

// Kiểm tra quyền đăng nhập và phải là nhà tuyển dụng
if (!isset($_SESSION['user_id']) || $_SESSION['vai_tro'] != 'nha_tuyen_dung') {
    header("Location: dangnhap.php");
    exit();
}

$id_ntd = $_SESSION['user_id'];

// Lấy danh sách tin tuyển dụng do chính nhà tuyển dụng này đăng
$sql = "SELECT * FROM tintuyendung WHERE ID_NhaTuyenDung = ? ORDER BY ID DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_ntd);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản Lý Tin Tuyển Dụng</title>
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
                Xin chào, <strong><?= htmlspecialchars($_SESSION['ho_ten']) ?></strong> (Nhà tuyển dụng)
            </span>
            <a href="dangxuat.php" class="btn btn-outline-danger btn-sm">Đăng xuất</a>
        </div>
    </div>
</nav>

<div class="container mb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold" style="color: var(--text-dark);">Danh Sách Tin Tuyển Dụng Của Bạn</h2>
        <a href="them_tin.php" class="btn btn-primary">Đăng Tin Mới</a>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="py-3 ps-3">ID</th>
                            <th class="py-3">Tiêu đề công việc</th>
                            <th class="py-3">Địa điểm</th>
                            <th class="py-3">Hạn nộp</th>
                            <th class="py-3 text-center">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td class="ps-3 fw-semibold">#<?= $row['ID'] ?></td>
                                    <td class="fw-semibold" style="color: var(--primary-orange);"><?= htmlspecialchars($row['Tieu_de']) ?></td>
                                    <td><?= htmlspecialchars($row['Dia_diem']) ?></td>
                                    <td><?= date('d/m/Y', strtotime($row['Han_nop'])) ?></td>
                                    <td class="text-center">
                                        <a href="xem_hoso.php?id_tin=<?= $row['ID'] ?>" class="btn btn-sm btn-outline-primary me-1">Xem hồ sơ</a>
                                        <a href="sua_tin.php?id=<?= $row['ID'] ?>" class="btn btn-sm btn-outline-secondary me-1">Sửa</a>
                                        <a href="xoa_tin.php?id=<?= $row['ID'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Bạn có chắc chắn muốn xóa tin này không?');">Xóa</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center py-4 text-secondary">
                                    Bạn chưa đăng tin tuyển dụng nào. Hãy bấm "Đăng Tin Mới" để bắt đầu.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

</body>
</html>