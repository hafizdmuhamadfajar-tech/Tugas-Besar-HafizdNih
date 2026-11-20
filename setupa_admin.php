<?php
// Pembuat admin PERTAMA. Otomatis terkunci begitu sudah ada 1 admin di tabel users.
// Setelah dipakai, HAPUS file ini dari server.
include "koneksi.php";
include "auth.php";
mysqli_report(MYSQLI_REPORT_OFF);

$r   = rows(dbq($koneksi, "SELECT COUNT(*) AS c FROM users WHERE role = 'admin'"));
$ada = $r ? (int)$r[0]['c'] : 0;
if ($ada > 0) {
    http_response_code(403);
    die("Admin sudah ada, halaman ini dikunci. Hapus file setup_admin.php dari server.");
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $u  = trim($_POST['username'] ?? '');
    $p  = $_POST['password'] ?? '';
    $p2 = $_POST['konfirmasi'] ?? '';

    if ($u === '' || $p === '') {
        $error = 'Username dan password wajib diisi.';
    } elseif ($p !== $p2) {
        $error = 'Konfirmasi password tidak sama.';
    } elseif (strlen($p) < 6) {
        $error = 'Password minimal 6 karakter.';
    } elseif (dbq($koneksi, "INSERT INTO users (username, password, role) VALUES (?, ?, 'admin')", 'ss', [$u, hash_pw($p)])) {
        flash_set('success', 'Admin berhasil dibuat. Sekarang login, lalu hapus setup_admin.php dari server.');
        header("Location: login.php"); exit;
    } else {
        $error = 'Gagal membuat admin: ' . mysqli_error($koneksi);
    }
}

$page_judul = 'Buat Admin';
include "navbar.php";
?>
<h1 class="h4 mb-1">Buat admin pertama</h1>
<p class="text-muted mb-4">Halaman ini terkunci otomatis setelah ada satu admin. Hapus file <code>setup_admin.php</code> setelah dipakai.</p>

<?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>

<form method="POST">
  <?= csrf_field() ?>
  <div class="mb-3"><label class="form-label" for="username">Username</label>
    <input type="text" class="form-control" id="username" name="username" maxlength="50" required autofocus></div>
  <div class="mb-3"><label class="form-label" for="password">Password</label>
    <input type="password" class="form-control" id="password" name="password" minlength="6" required></div>
  <div class="mb-4"><label class="form-label" for="konfirmasi">Konfirmasi password</label>
    <input type="password" class="form-control" id="konfirmasi" name="konfirmasi" minlength="6" required></div>
  <button type="submit" class="btn btn-primary w-100">Buat admin</button>
</form>

<?php include "script.php"; ?>
