<?php
require_once __DIR__ . '/../includes/bootstrap.php';
require_admin();

// CAU 4: Cap nhat thong tin mot nguoi dung (UPDATE).
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id || $id < 1) {
    flash('ID người dùng không hợp lệ.', 'danger');
    redirect('index.php');
}
$stmt = db()->prepare('SELECT admin_id, admin_email, images FROM Users WHERE admin_id = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();
if (!$user) {
    flash('Không tìm thấy người dùng cần sửa.', 'danger');
    redirect('index.php');
}
$email = $user['admin_email'];
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    $email = trim((string) ($_POST['email'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');
    $removeImage = isset($_POST['remove_image']);
    $errors = validate_user($email, $password, false);

    if (!$errors) {
        $newImage = '';
        try {
            $newImage = upload_image($_FILES['image'] ?? []);
            $oldImage = $user['images'];
            $image = $newImage !== '' ? $newImage : ($removeImage ? '' : $oldImage);

            // De trong password = giu nguyen mat khau cu.
            if ($password !== '') {
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $stmt = db()->prepare('UPDATE Users SET admin_email = ?, admin_password = ?, images = ? WHERE admin_id = ?');
                $stmt->bind_param('sssi', $email, $hashedPassword, $image, $id);
            } else {
                $stmt = db()->prepare('UPDATE Users SET admin_email = ?, images = ? WHERE admin_id = ?');
                $stmt->bind_param('ssi', $email, $image, $id);
            }
            $stmt->execute();
            $stmt->close();
            if ($oldImage !== $image) {
                remove_uploaded_image($oldImage);
            }
            flash('Cập nhật người dùng #' . $id . ' thành công!');
            redirect('index.php');
        } catch (mysqli_sql_exception $error) {
            remove_uploaded_image($newImage);
            $errors[] = $error->getCode() === 1062 ? 'Email này đã được sử dụng.' : 'Không thể cập nhật dữ liệu MySQL.';
        } catch (RuntimeException $error) {
            $errors[] = $error->getMessage();
        }
    }
}
render_header('Chỉnh sửa người dùng', 'admin', '../');
?>
<section class="container form-page">
    <a class="back-link" href="index.php">← Trở về trang quản trị</a>
    <div class="form-layout"><div class="form-card">
        <div class="form-header"><span class="form-badge">CHỈNH SỬA · UPDATE</span><h1>Chỉnh sửa người dùng</h1><p class="muted">Đang chỉnh sửa bản ghi ID #<?= (int) $id ?>.</p></div>
        <?php if ($errors): ?><div class="alert alert-danger"><strong>Vui lòng kiểm tra:</strong><ul><?php foreach ($errors as $error): ?><li><?= h($error) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
        <form method="post" enctype="multipart/form-data" class="editor-form">
            <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
            <div class="field-group"><label for="email">Địa chỉ email <span class="required">*</span></label><input id="email" name="email" type="email" maxlength="255" value="<?= h($email) ?>" required></div>
            <div class="field-group"><label for="password">Mật khẩu mới</label><input id="password" name="password" type="password" minlength="6" maxlength="72" placeholder="Để trống nếu không muốn đổi" autocomplete="new-password"><small>Không nhập gì sẽ giữ nguyên mật khẩu hiện tại.</small></div>
            <div class="field-group"><label for="image">Thay ảnh đại diện</label><div class="file-drop"><img src="<?= h(image_src($user['images'], '../')) ?>" class="image-preview" data-image-preview alt="Ảnh đại diện hiện tại"><div><strong>Chọn ảnh mới</strong><span>JPG, PNG, WEBP, GIF · tối đa 2 MB</span><input id="image" name="image" type="file" accept="image/jpeg,image/png,image/webp,image/gif" data-image-input></div></div>
            <?php if ($user['images'] !== ''): ?><label class="checkbox-label"><input type="checkbox" name="remove_image" value="1"> Xóa ảnh hiện tại (nếu không chọn ảnh mới)</label><?php endif; ?>
            </div>
            <div class="form-actions"><a class="btn btn-outline" href="index.php">Hủy</a><button class="btn btn-primary" type="submit">✓ Lưu thay đổi</button></div>
        </form>
    </div><aside class="form-side"><div class="side-insight"><span class="side-insight-icon">↗</span><h3>Thao tác UPDATE</h3><p>Chỉnh sửa email, mật khẩu hoặc ảnh đại diện. Sau khi lưu, dữ liệu tại frontend sẽ được cập nhật theo MySQL.</p><code>UPDATE Users SET ...</code></div></aside></div>
</section>
<?php render_footer('../'); ?>
