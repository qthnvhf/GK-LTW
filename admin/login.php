<?php
require_once __DIR__ . '/../includes/bootstrap.php';

if (is_admin()) {
    redirect('index.php');
}
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    $username = trim((string) ($_POST['username'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');

    if (hash_equals(ADMIN_USERNAME, $username) && password_verify($password, ADMIN_PASSWORD_HASH)) {
        session_regenerate_id(true);
        $_SESSION['admin_logged_in'] = true;
        flash('Đăng nhập thành công. Chào mừng bạn trở lại!');
        redirect('index.php');
    }
    $error = 'Tên đăng nhập hoặc mật khẩu không đúng.';
}
render_header('Đăng nhập quản trị', 'admin', '../');
$notice = pull_flash();
?>
<section class="container auth-layout">
    <div class="auth-card">
        <div class="auth-symbol">U<span>.</span></div>
        <p class="section-overline">KHU VỰC QUẢN TRỊ</p>
        <h1>Chào mừng trở lại</h1>
        <p class="muted">Đăng nhập để thêm, sửa và xóa dữ liệu trong bảng Users.</p>
        <?php if ($notice): ?><div class="alert alert-<?= h($notice['type']) ?>"><?= h($notice['message']) ?></div><?php endif; ?>
        <?php if ($error): ?><div class="alert alert-danger"><?= h($error) ?></div><?php endif; ?>
        <form method="post" class="editor-form">
            <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
            <div class="field-group"><label for="username">Tên đăng nhập</label><input id="username" name="username" type="text" autocomplete="username" placeholder="Nhập tài khoản admin" required autofocus></div>
            <div class="field-group"><label for="password">Mật khẩu</label><input id="password" name="password" type="password" autocomplete="current-password" placeholder="Nhập mật khẩu" required></div>
            <button class="btn btn-primary btn-full" type="submit">Đăng nhập <span aria-hidden="true">→</span></button>
        </form>
        <div class="demo-hint"><span class="info-bubble">i</span><span><strong>Tài khoản demo:</strong> admin &nbsp;·&nbsp; <strong>Mật khẩu:</strong> Admin@123</span></div>
    </div>
    <div class="auth-side"><span class="auth-side-kicker">ADMIN DASHBOARD</span><h2>Mọi thông tin,<br>trong tầm tay.</h2><p>Quản lý danh sách người dùng bằng các thao tác CRUD cơ bản của PHP và MySQL.</p><div class="auth-side-figure">✳</div></div>
</section>
<?php render_footer('../'); ?>
