<?php
session_start();
require_once 'config.php';

// Kiểm tra quyền: Phải là nhà tuyển dụng
if (!isset($_SESSION['user_id']) || $_SESSION['vai_tro'] != 'nha_tuyen_dung') {
    header("Location: dangnhap.php");
    exit();
}

$id_tin = isset($_GET['id_tin']) ? intval($_GET['id_tin']) : 0;

if ($id_tin <= 0) {
    header("Location: quanly_tin.php");
    exit();
}

// Lấy thông tin tin tuyển dụng
$stmt_tin = $conn->prepare("SELECT * FROM tintuyendung WHERE ID = ?");
$stmt_tin->bind_param("i", $id_tin);
$stmt_tin->execute();
$tin = $stmt_tin->get_result()->fetch_assoc();

if (!$tin) {
    header("Location: quanly_tin.php");
    exit();
}

// Lấy danh sách hồ sơ ứng tuyển của tin này, kết hợp với bảng nguoidung để lấy thông tin ứng viên
$sql = "SELECT hosoungtuyen.*, nguoidung.Ho_ten, nguoidung.Email 
        FROM hosoungtuyen 
        JOIN nguoidung ON hosoungtuyen.ID_UngVien = nguoidung.ID 
        WHERE hosoungtuyen.ID_TinTuyenDung = ? 
        ORDER BY hosoungtuyen.ID DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_tin);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Xem Hồ Sơ Ứng Viên</title>
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
    <div class="card shadow-sm border-0 p-4 mb-4">
        <h3 class="fw-bold mb-2" style="color: var(--primary-orange);">Hồ sơ ứng tuyển cho vị trí: <?= htmlspecialchars($tin['Tieu_de']) ?></h3>
        <p class="text-secondary small mb-0">Địa điểm: <?= htmlspecialchars($tin['Dia_diem']) ?> | Hạn nộp: <?= date('d/m/Y', strtotime($tin['Han_nop'])) ?></p>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="py-3 ps-3">STT</th>
                            <th class="py-3">Họ tên ứng viên</th>
                            <th class="py-3">Email</th>
                            <th class="py-3">Ngày nộp</th>
                            <th class="py-3">File CV</th>
                            <th class="py-3 text-center">Trạng thái</th>
                            <th class="py-3 text-center">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php $stt = 1; while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td class="ps-3 fw-semibold"><?= $stt++ ?></td>
                                    <td class="fw-semibold text-dark"><?= htmlspecialchars($row['Ho_ten']) ?></td>
                                    <td><?= htmlspecialchars($row['Email']) ?></td>
                                    <td><?= date('H:i d/m/Y', strtotime($row['Ngay_nop'])) ?></td>
                                    <td>
                                        <a href="<?= htmlspecialchars($row['Duong_dan_CV']) ?>" target="_blank" class="text-decoration-none fw-semibold" style="color: var(--primary-orange);">Xem CV</a>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($row['Trang_thai'] == 'Đang chờ duyệt' || $row['Trang_thai'] == 'Dang cho duyet'): ?>
                                            <span class="badge bg-warning text-dark px-3 py-2">Đang chờ duyệt</span>
                                        <?php elseif ($row['Trang_thai'] == 'Trúng tuyển' || $row['Trang_thai'] == 'Trung tuyen'): ?>
                                            <span class="badge bg-success px-3 py-2">Đã trúng tuyển</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger px-3 py-2">Từ chối</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <a href="capnhat_trangthai.php?id_hoso=<?= $row['ID'] ?>&trang_thai=Trúng tuyển&id_tin=<?= $id_tin ?>" class="btn btn-sm btn-success me-1">Duyệt</a>
                                        <a href="capnhat_trangthai.php?id_hoso=<?= $row['ID'] ?>&trang_thai=Từ chối&id_tin=<?= $id_tin ?>" class="btn btn-sm btn-outline-danger">Từ chối</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center py-4 text-secondary">
                                    Chưa có ứng viên nào nộp hồ sơ vào vị trí này.
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