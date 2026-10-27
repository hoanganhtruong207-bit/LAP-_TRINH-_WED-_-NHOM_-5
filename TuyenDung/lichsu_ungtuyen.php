<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['vai_tro'] != 'ung_vien') {
    header("Location: dangnhap.php");
    exit();
}

$ung_vien_id = $_SESSION['user_id'];

$sql = "SELECT hosoungtuyen.*, tintuyendung.Tieu_de, tintuyendung.Dia_diem, tintuyendung.Han_nop 
        FROM hosoungtuyen 
        JOIN tintuyendung ON hosoungtuyen.ID_TinTuyenDung = tintuyendung.ID 
        WHERE hosoungtuyen.ID_UngVien = ? 
        ORDER BY hosoungtuyen.ID DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $ung_vien_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Lịch Sử Nộp Hồ Sơ</title>
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
    <h2 class="fw-bold mb-4" style="color: var(--text-dark);">Lịch Sử Nộp Hồ Sơ Của Bạn</h2>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="py-3 ps-3">STT</th>
                            <th class="py-3">Vị trí ứng tuyển</th>
                            <th class="py-3">Địa điểm</th>
                            <th class="py-3">File CV</th>
                            <th class="py-3 text-center">Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php $stt = 1; while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td class="ps-3 fw-semibold"><?= $stt++ ?></td>
                                    <td class="fw-semibold" style="color: var(--primary-orange);"><?= htmlspecialchars($row['Tieu_de']) ?></td>
                                    <td><?= htmlspecialchars($row['Dia_diem']) ?></td>
                                    <td>
                                        <a href="<?= htmlspecialchars($row['Duong_dan_CV']) ?>" target="_blank" class="text-decoration-none fw-semibold" style="color: var(--primary-orange);">Xem CV</a>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($row['Trang_thai'] == 'Đang chờ duyệt'): ?>
                                            <span class="badge bg-warning text-dark px-3 py-2">Đang chờ duyệt</span>
                                        <?php elseif ($row['Trang_thai'] == 'Trúng tuyển' || $row['Trang_thai'] == 'Trung tuyen'): ?>
                                            <span class="badge bg-success px-3 py-2">Đã trúng tuyển</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger px-3 py-2">Từ chối</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center py-4 text-secondary">
                                    Bạn chưa nộp hồ sơ vào công việc nào.
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