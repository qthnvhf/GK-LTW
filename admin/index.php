<?php
require_once __DIR__ . '/../includes/bootstrap.php';
require_admin();

$search = trim((string) ($_GET['q'] ?? ''));
$search = substr($search, 0, 100);
$connection = db();
$total = (int) $connection->query('SELECT COUNT(*) AS total FROM Users')->fetch_assoc()['total'];

if ($search !== '') {
    $stmt = $connection->prepare('SELECT admin_id, admin_email, images FROM Users WHERE admin_email LIKE ? ORDER BY admin_id DESC');
    $keyword = '%' . $search . '%';
    $stmt->bind_param('s', $keyword);
    $stmt->execute();
    $users = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
} else {
    $users = $connection->query('SELECT admin_id, admin_email, images FROM Users ORDER BY admin_id DESC')->fetch_all(MYSQLI_ASSOC);
}
$notice = pull_flash();
render_header('Trang quản trị', 'admin', '../');
?>
<section class="container dashboard-container">
    <div class="dashboard-header">
        <div><div class="eyebrow"><span class="eyebrow-dot"></span> QUẢN TRỊ HỆ THỐNG</div><h1>Quản lý người dùng<span class="heading-dot">.</span></h1><p class="muted">Thêm mới, chỉnh sửa và xóa dữ liệu trực tiếp trong MySQL.</p></div>
        <div class="header-actions"><a href="logout.php" class="btn btn-outline">Đăng xuất</a><a href="create.php" class="btn btn-primary"><span aria-hidden="true">＋</span> Thêm người dùng</a></div>
    </div>
    <?php if ($notice): ?><div class="alert alert-<?= h($notice['type']) ?>"><?= h($notice['message']) ?></div><?php endif; ?>
    <div class="dashboard-stats">
        <div class="dash-stat"><span class="dash-stat-text">TỔNG NGƯỜI DÙNG</span><span class="dash-stat-value"><?= $total ?></span><span class="dash-stat-caption">Bản ghi trong bảng Users</span></div>
        <div class="dash-stat"><span class="dash-stat-text">CƠ SỞ DỮ LIỆU</span><span class="dash-stat-value small-value">QLNhanSu</span><span class="dash-stat-caption">MySQL · đang kết nối</span></div>
        <div class="dash-stat"><span class="dash-stat-text">TÁC VỤ</span><span class="dash-stat-value small-value">CRUD</span><span class="dash-stat-caption">Create · Read · Update · Delete</span></div>
    </div>
    <div class="table-panel">
        <div class="table-panel-heading"><div><h2>Danh sách tài khoản</h2><p class="muted"><?= count($users) ?> kết quả hiển thị</p></div><form method="get" class="search-form compact" role="search"><label class="visually-hidden" for="search-admin">Tìm email</label><span class="search-icon" aria-hidden="true">⌕</span><input name="q" id="search-admin" type="search" placeholder="Tìm email..." value="<?= h($search) ?>"><button class="btn btn-dark btn-search" type="submit">Tìm</button></form></div>
        <div class="table-scroll"><table class="data-table"><thead><tr><th>ID</th><th>NGƯỜI DÙNG</th><th>HÌNH ẢNH</th><th>MẬT KHẨU</th><th class="align-right">THAO TÁC</th></tr></thead><tbody>
        <?php foreach ($users as $user): ?>
            <tr>
                <td><span class="id-small">#<?= (int) $user['admin_id'] ?></span></td>
                <td><span class="table-email"><?= h($user['admin_email']) ?></span></td>
                <td><img class="table-avatar" src="<?= h(image_src($user['images'], '../')) ?>" alt="Ảnh đại diện" loading="lazy"></td>
                <td><span class="secure-pill">● Đã mã hóa</span></td>
                <td class="align-right"><div class="action-group"><a href="edit.php?id=<?= (int) $user['admin_id'] ?>" class="btn-action edit-action">Sửa</a><form action="delete.php" method="post" class="inline-form" data-confirm="Xóa người dùng <?= h($user['admin_email']) ?>? Hành động này không thể hoàn tác."><input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>"><input type="hidden" name="id" value="<?= (int) $user['admin_id'] ?>"><button class="btn-action delete-action" type="submit">Xóa</button></form></div></td>
            </tr>
        <?php endforeach; ?>
        <?php if (!$users): ?><tr><td colspan="5" class="table-empty">Không tìm thấy tài khoản phù hợp. <a href="index.php">Hiển thị tất cả</a></td></tr><?php endif; ?>
        </tbody></table></div>
        <div class="table-panel-footer"><span>Dữ liệu được cập nhật trực tiếp trong MySQL.</span><a href="../index.php">Xem frontend →</a></div>
    </div>
</section>
<?php render_footer('../'); ?>
