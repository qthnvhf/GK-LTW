<?php
require_once __DIR__ . '/includes/bootstrap.php';

// CAU 2: Lay du lieu Users tu MySQL de hien thi o frontend.
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

render_header('Danh sách người dùng', 'home');
?>
<section class="container page-top">
    <div class="eyebrow"><span class="eyebrow-dot"></span> PHP + MySQL · Frontend</div>
    <div class="hero-card">
        <div class="hero-copy">
            <p class="hero-caption">DANH SÁCH THÀNH VIÊN</p>
            <h1>Quản lý người dùng <span class="gradient-text">đơn giản &amp; trực quan.</span></h1>
            <p class="hero-description">Dữ liệu được lấy trực tiếp từ bảng <strong>Users</strong> trong cơ sở dữ liệu <strong>QLNhanSu</strong>. Tìm kiếm và theo dõi danh sách ngay tại đây.</p>
            <div class="hero-buttons">
                <a class="btn btn-white" href="#users-list">Khám phá danh sách <span aria-hidden="true">↗</span></a>
                <a class="btn btn-ghost" href="admin/login.php">Quản lý dữ liệu <span aria-hidden="true">→</span></a>
            </div>
        </div>
        <div class="hero-art" aria-hidden="true">
            <div class="hero-orbit orbit-one"></div>
            <div class="hero-orbit orbit-two"></div>
            <div class="floating-card floating-top"><span class="float-symbol">✳</span><span>Smart management</span></div>
            <div class="large-icon">U<span class="large-dot">.</span></div>
            <div class="floating-card floating-bottom"><span class="green-check">✓</span><span>Connected to MySQL</span></div>
        </div>
    </div>

    <div class="metrics-row">
        <div class="metric"><span class="metric-icon lavender">♙</span><div><span class="metric-number"><?= $total ?></span><span class="metric-label">Tổng người dùng</span></div></div>
        <div class="metric"><span class="metric-icon mint">✓</span><div><span class="metric-number">MySQL</span><span class="metric-label">Nguồn dữ liệu</span></div></div>
        <div class="metric"><span class="metric-icon peach">◈</span><div><span class="metric-number">An toàn</span><span class="metric-label">Mật khẩu được mã hóa</span></div></div>
    </div>
</section>

<section class="container listing-section" id="users-list">
    <div class="section-heading">
        <div>
            <p class="section-overline">DỮ LIỆU TRỰC TIẾP</p>
            <h2>Danh sách người dùng</h2>
            <p class="muted">Hiển thị <?= count($users) ?> / <?= $total ?> người dùng</p>
        </div>
        <form method="get" action="index.php" class="search-form" role="search">
            <label class="visually-hidden" for="search">Tìm theo email</label>
            <span class="search-icon" aria-hidden="true">⌕</span>
            <input id="search" name="q" type="search" placeholder="Tìm kiếm theo email..." value="<?= h($search) ?>">
            <button type="submit" class="btn btn-dark btn-search">Tìm kiếm</button>
        </form>
    </div>

    <?php if (!$users): ?>
        <div class="empty-state">
            <div class="empty-icon">⌕</div>
            <h3>Chưa tìm thấy người dùng</h3>
            <p><?= $search !== '' ? 'Không có email nào khớp với từ khóa bạn nhập.' : 'Cơ sở dữ liệu hiện chưa có người dùng.' ?></p>
            <?php if ($search !== ''): ?><a class="btn btn-dark" href="index.php">Xóa bộ lọc</a><?php endif; ?>
        </div>
    <?php else: ?>
        <div class="user-grid">
            <?php foreach ($users as $user): ?>
                <article class="user-card">
                    <div class="card-topline"><span class="id-pill">ID #<?= (int) $user['admin_id'] ?></span><span class="online-dot" title="Dữ liệu đã lưu">● <span>Đã lưu</span></span></div>
                    <div class="user-photo-wrap"><img class="user-photo" src="<?= h(image_src($user['images'])) ?>" alt="Ảnh đại diện của <?= h($user['admin_email']) ?>" loading="lazy"></div>
                    <h3 class="user-title"><?= h(explode('@', $user['admin_email'])[0]) ?></h3>
                    <p class="user-email" title="<?= h($user['admin_email']) ?>"><?= h($user['admin_email']) ?></p>
                    <div class="user-divider"></div>
                    <div class="user-card-foot"><span class="field-label">Mật khẩu</span><span class="safe-label">♢ Đã mã hóa</span></div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    <p class="privacy-note">🔒 Vì lý do bảo mật, cột <code>admin_password</code> được lưu dưới dạng mã băm và không hiển thị ở frontend.</p>
</section>
<?php render_footer(); ?>
