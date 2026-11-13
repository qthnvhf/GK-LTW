<?php
require_once __DIR__ . '/../includes/bootstrap.php';
require_admin();

// CAU 4: Xoa nguoi dung (DELETE), chi nhan request POST.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Phuong thuc khong duoc ho tro.');
}
check_csrf();
$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
if (!$id || $id < 1) {
    flash('ID người dùng không hợp lệ.', 'danger');
    redirect('index.php');
}
try {
    $stmt = db()->prepare('SELECT images FROM Users WHERE admin_id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    if (!$row) {
        flash('Người dùng không còn tồn tại.', 'warning');
        redirect('index.php');
    }
    $stmt = db()->prepare('DELETE FROM Users WHERE admin_id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->close();
    remove_uploaded_image($row['images']);
    flash('Đã xóa người dùng #' . $id . ' khỏi bảng Users.');
} catch (mysqli_sql_exception $error) {
    flash('Không thể xóa người dùng. Vui lòng thử lại.', 'danger');
}
redirect('index.php');
