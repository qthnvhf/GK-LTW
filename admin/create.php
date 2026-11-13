<?php
require_once __DIR__ . '/../includes/bootstrap.php';
require_admin();

// CAU 1: Them du lieu vao bang Users (INSERT).
$email = '';
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    $email = trim((string) ($_POST['email'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');
    $errors = validate_user($email, $password, true);

    if (!$errors) {
        $newImage = '';
        try {
            $newImage = upload_image($_FILES['image'] ?? []);
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = db()->prepare('INSERT INTO Users (admin_email, admin_password, images) VALUES (?, ?, ?)');
            $stmt->bind_param('sss', $email, $hashedPassword, $newImage);
            $stmt->execute();
            $stmt->close();
            flash('Thêm người dùng thành công! Dữ liệu đã lưu vào MySQL.');
            redirect('index.php');
        } catch (mysqli_sql_exception $error) {
            remove_uploaded_image($newImage);
            $errors[] = $error->getCode() === 1062 ? 'Email này đã tồn tại, vui lòng nhập email khác.' : 'Lỗi lưu dữ liệu MySQL. Vui lòng kiểm tra lại.';
        } catch (RuntimeException $error) {
            $errors[] = $error->getMessage();
        }
    }
}
render_header('Thêm người dùng', 'admin', '../');
?>
<section class="container form-page">
    <a class="back-link" href="index.php">← Trở về trang quản trị</a>
    <div class="form-layout"><div class="form-card">
        <div class="form-header"><span class="form-badge">THÊM MỚI · CREATE</span><h1>Thêm người dùng</h1><p class="muted">Nhập thông tin bên dưới để tạo bản ghi mới trong bảng Users.</p></div>
        <?php if ($errors): ?><div class="alert alert-danger"><strong>Vui lòng kiểm tra:</strong><ul><?php foreach ($errors as $error): ?><li><?= h($error) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
        <form method="post" enctype="multipart/form-data" class="editor-form">
            <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
            <div class="field-group"><label for="email">Địa chỉ email <span class="required">*</span></label><input id="email" name="email" type="email" maxlength="255" placeholder="vidu@gmail.com" value="<?= h($email) ?>" required autocomplete="email"><small>Địa chỉ email không được trùng với tài khoản đã có.</small></div>
            <div class="field-group"><label for="password">Mật khẩu <span class="required">*</span></label><input id="password" name="password" type="password" minlength="6" maxlength="72" placeholder="Ít nhất 6 ký tự" required autocomplete="new-password"><small>Mật khẩu được băm an toàn bằng password_hash().</small></div>
            <div class="field-group"><label for="image">Ảnh đại diện</label><div class="file-drop"><img src="../assets/img/placeholder.svg" class="image-preview" data-image-preview alt="Xem trước ảnh đại diện"><div><strong>Chọn ảnh từ máy</strong><span>JPG, PNG, WEBP, GIF · tối đa 2 MB</span><input id="image" name="image" type="file" accept="image/jpeg,image/png,image/webp,image/gif" data-image-input></div></div></div>
            <div class="form-actions"><a class="btn btn-outline" href="index.php">Hủy</a><button class="btn btn-primary" type="submit">＋ Thêm người dùng</button></div>
        </form>
    </div><aside class="form-side"><div class="side-insight"><span class="side-insight-icon">✳</span><h3>Thao tác INSERT</h3><p>Khi nhấn “Thêm người dùng”, PHP kiểm tra dữ liệu, lưu ảnh và thực hiện câu lệnh INSERT bằng prepared statement.</p><code>INSERT INTO Users (...)</code></div></aside></div>
</section>
<?php render_footer('../'); ?>
