<?php
// Chuyen ky tu dac biet thanh an toan khi in ra HTML (chong XSS).
function h($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function redirect(string $path): void
{
    header('Location: ' . $path);
    exit;
}

// Token CSRF cho cac form co thay doi du lieu.
function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function check_csrf(): void
{
    if (!hash_equals(csrf_token(), (string) ($_POST['csrf_token'] ?? ''))) {
        http_response_code(403);
        exit('Yeu cau khong hop le (CSRF). Vui long tai lai trang.');
    }
}

function flash(string $message, string $type = 'success'): void
{
    $_SESSION['flash'] = ['message' => $message, 'type' => $type];
}

function pull_flash(): ?array
{
    $item = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $item;
}

function is_admin(): bool
{
    return !empty($_SESSION['admin_logged_in']);
}

function require_admin(): void
{
    if (!is_admin()) {
        flash('Vui lòng đăng nhập để quản lý dữ liệu.', 'warning');
        redirect('login.php');
    }
}

// Kiem tra email va mat khau truoc khi INSERT / UPDATE.
function validate_user(string $email, string $password, bool $mustHavePassword): array
{
    $errors = [];
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 255) {
        $errors[] = 'Email không hợp lệ (tối đa 255 ký tự).';
    }
    if (($mustHavePassword || $password !== '') && strlen($password) < 6) {
        $errors[] = 'Mật khẩu phải có ít nhất 6 ký tự.';
    }
    if (strlen($password) > 72) {
        $errors[] = 'Mật khẩu tối đa 72 ký tự trong bản demo này.';
    }
    return $errors;
}

// Chi chap nhan anh JPG/PNG/WEBP/GIF, dung luong toi da 2 MB.
// Tra ve duong dan de luu trong cot images (vi du: uploads/abc123.jpg).
function upload_image(array $file): string
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return '';
    }
    if (($file['error'] ?? 1) !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Tải ảnh thất bại. Hãy thử lại.');
    }
    if ($file['size'] > 2 * 1024 * 1024) {
        throw new RuntimeException('Ảnh không được vượt quá 2 MB.');
    }
    $tmp = $file['tmp_name'] ?? '';
    if (!is_uploaded_file($tmp)) {
        throw new RuntimeException('Tệp tải lên không hợp lệ.');
    }

    $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'image/gif' => 'gif'];
    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($tmp);
    if (!isset($allowed[$mime]) || @getimagesize($tmp) === false) {
        throw new RuntimeException('Chỉ chấp nhận ảnh JPG, PNG, WEBP hoặc GIF.');
    }

    $filename = bin2hex(random_bytes(12)) . '.' . $allowed[$mime];
    $folder = dirname(__DIR__) . '/uploads';
    if (!is_dir($folder) && !mkdir($folder, 0755, true)) {
        throw new RuntimeException('Không tạo được thư mục uploads.');
    }
    if (!move_uploaded_file($tmp, $folder . '/' . $filename)) {
        throw new RuntimeException('Không lưu được ảnh. Kiểm tra quyền thư mục uploads.');
    }
    return 'uploads/' . $filename;
}

// Chi xoa file tu thu muc uploads (khong xoa anh minh hoa mac dinh).
function remove_uploaded_image(string $path): void
{
    if (preg_match('~^uploads/[a-f0-9]{24}\.(jpg|png|webp|gif)$~', $path)) {
        $file = dirname(__DIR__) . '/' . $path;
        if (is_file($file)) {
            @unlink($file);
        }
    }
}

// Anh mac dinh hoac anh trong cot images, khong cho phep URL/duong dan tuy y.
function image_src(?string $path, string $prefix = './'): string
{
    if ($path && (preg_match('~^uploads/[a-f0-9]{24}\.(jpg|png|webp|gif)$~', $path)
        || preg_match('~^assets/img/avatar_[1-4]\.svg$~', $path))) {
        return $prefix . $path;
    }
    return $prefix . 'assets/img/placeholder.svg';
}

function render_header(string $title, string $current = 'home', string $prefix = './'): void
{
    $fullTitle = h($title) . ' | QLNhanSu';
    $homeUrl = h($prefix . 'index.php');
    $adminUrl = h($prefix . 'admin/index.php');
    $loginUrl = h($prefix . 'admin/login.php');
    $styleUrl = h($prefix . 'assets/css/style.css');
    $scriptUrl = h($prefix . 'assets/js/main.js');
    $activeHome = $current === 'home' ? 'active' : '';
    $activeAdmin = $current === 'admin' ? 'active' : '';
    $adminText = is_admin() ? 'Trang quản trị' : 'Đăng nhập quản trị';
    $rightUrl = is_admin() ? $adminUrl : $loginUrl;
    echo <<<HTML
<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Demo quản lý nhân sự bằng PHP thuần và MySQL">
    <title>{$fullTitle}</title>
    <link rel="stylesheet" href="{$styleUrl}">
    <script src="{$scriptUrl}" defer></script>
</head>
<body>
<div class="site-shell">
<header class="topbar">
    <div class="container topbar-inner">
        <a href="{$homeUrl}" class="brand" aria-label="Về trang chủ">
            <span class="brand-mark">U</span><span>user<span class="brand-accent">space.</span></span>
        </a>
        <nav class="nav-links" aria-label="Điều hướng chính">
            <a class="nav-link {$activeHome}" href="{$homeUrl}">Danh sách người dùng</a>
            <a class="nav-link {$activeAdmin}" href="{$rightUrl}">{$adminText}</a>
        </nav>
    </div>
</header>
<main class="main-content">
HTML;
}

function render_footer(string $prefix = './'): void
{
    $home = h($prefix . 'index.php');
    echo <<<HTML
</main>
<footer class="footer">
    <div class="container footer-inner">
        <span>© UserSpace · Bài tập PHP &amp; MySQL</span>
        <a href="{$home}">Trang chủ</a>
    </div>
</footer>
</div>
</body>
</html>
HTML;
}
