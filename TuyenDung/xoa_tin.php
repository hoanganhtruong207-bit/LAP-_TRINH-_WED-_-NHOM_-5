<?php
session_start();
require_once 'config.php';

// Kiểm tra quyền: Phải là nhà tuyển dụng
if (!isset($_SESSION['user_id']) || $_SESSION['vai_tro'] != 'nha_tuyen_dung') {
    header("Location: dangnhap.php");
    exit();
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id > 0) {
    // Xóa tin tuyển dụng (các hồ sơ ứng tuyển liên quan sẽ tự động xóa nhờ khóa ngoại ON DELETE CASCADE trong CSDL)
    $stmt = $conn->prepare("DELETE FROM tintuyendung WHERE ID = ? AND ID_NhaTuyenDung = ?");
    $stmt->bind_param("ii", $id, $_SESSION['user_id']);
    $stmt->execute();
}

header("Location: quanly_tin.php");
exit();
?>