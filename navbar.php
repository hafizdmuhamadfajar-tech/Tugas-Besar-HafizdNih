<?php
include_once "auth.php";
$cur       = basename($_SERVER['PHP_SELF']);
$role      = isset($_SESSION['role']) ? $_SESSION['role'] : '';
$logged    = isset($_SESSION['user_id']);
$nama_user = isset($_SESSION['username']) ? $_SESSION['username'] : '';
$aksi_q    = isset($_GET['aksi']) ? $_GET['aksi'] : '';

// Judul dan keterangan tiap halaman. Halaman boleh menimpa lewat $page_judul / $page_sub sebelum include.
$label_role = array('admin' => 'Administrator', 'guru' => 'Guru', 'siswa' => 'Siswa');
$halaman = array(
    'dashboard.php' => array('Beranda', '', ''),
    'user.php'      => array('Kelola User', 'Akun login: admin, guru, dan siswa.', 'Data master'),
    'guru.php'      => array('Kelola Guru', 'Guru pengampu mata pelajaran.', 'Data master'),
    'siswa.php'     => array('Kelola Siswa', 'Siswa terdaftar beserta kelasnya.', 'Data master'),
    'mapel.php'     => array('Kelola Mapel', 'Mata pelajaran dan guru pengampunya.', 'Data master'),
    'mengajar.php'  => array('Kelola Mengajar', 'Siswa yang mengikuti tiap mata pelajaran.', 'Data master'),
    'nilai.php'     => array('Nilai Siswa', 'Input dan ubah nilai rapor untuk mapel yang Anda ampu.', 'Penilaian'),
    'nilai_saya.php'=> array('Rapor Saya', '', 'Akademik'),
    'profil.php'    => array('Profil', '', 'Akun'),
    'cek_db.php'    => array('Cek Struktur Database', 'Membandingkan database dengan skema di soal.', 'Peralatan'),
);
$info  = isset($halaman[$cur]) ? $halaman[$cur] : array('SIPN', '', '');
$judul = isset($page_judul) ? $page_judul : $info[0];
$sub   = isset($page_sub)   ? $page_sub   : $info[1];
$eyebrow = $info[2] !== '' ? $info[2] : (isset($label_role[$role]) ? $label_role[$role] : '');
$kelas_sheet = isset($sheet_kelas) ? $sheet_kelas : '';
$ver = @filemtime(__DIR__ . '/style.css');

// Menu admin: tiap section satu file halaman, isinya "Daftar X" dan "+ Tambah X"
$grup_admin = array(
    array('User',     'user.php'),
    array('Guru',     'guru.php'),
    array('Siswa',    'siswa.php'),
    array('Mapel',    'mapel.php'),
    array('Mengajar', 'mengajar.php'),
);

if (!function_exists('menu_grup')) {
    function menu_grup($label, $file, $daftar, $tambah, $cur) {
        $aktif = ($cur === $file) ? ' active' : '';
        return '<li class="nav-item dropdown">'
             . '<a class="nav-link dropdown-toggle' . $aktif . '" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">' . e($label) . '</a>'
             . '<ul class="dropdown-menu">'
             . '<li><a class="dropdown-item" href="' . $file . '">' . e($daftar) . '</a></li>'
             . '<li><a class="dropdown-item tambah" href="' . $file . '?aksi=tambah">+ ' . e($tambah) . '</a></li>'
             . '</ul></li>';
    }
}
?>
<!doctype html>
<html lang="id">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($judul) ?> &middot; SIPN</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Mono:wght@500;600&family=IBM+Plex+Sans:wght@400;500;600&family=IBM+Plex+Serif:wght@500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link href="style.css?v=<?= (int)$ver ?>" rel="stylesheet">
  </head>

  <body class="<?= $logged ? 'app' : 'guest' ?>">
  <div class="sheet <?= e($kelas_sheet) ?><?= $logged ? '' : ' sheet-kecil' ?>">

<?php if ($logged): ?>
    <header class="kop">
      <div>
        <div class="kop-inst">SMK Sangkuriang 1 Cimahi</div>
        <div class="kop-app">Sistem Informasi Pengelolaan Nilai</div>
      </div>
      <div class="kop-user">
        <b><?= e($nama_user) ?></b>
        <span><?= e(isset($label_role[$role]) ? $label_role[$role] : $role) ?></span>
      </div>
    </header>

    <!-- start navbar -->
    <nav class="menu navbar navbar-expand-lg">
      <div class="container-fluid">
        <button class="menu-btn navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menuUtama" aria-controls="menuUtama" aria-expanded="false" aria-label="Buka menu">Menu</button>
        <div class="collapse navbar-collapse" id="menuUtama">
          <ul class="navbar-nav me-auto">
            <li class="nav-item"><a class="nav-link<?= $cur === 'dashboard.php' ? ' active' : '' ?>" href="dashboard.php">Beranda</a></li>

            <?php if ($role === 'admin'): foreach ($grup_admin as $g):
                echo menu_grup($g[0], $g[1], 'Daftar ' . $g[0], 'Tambah ' . $g[0], $cur);
            endforeach; endif; ?>

            <?php if ($role === 'guru'): echo menu_grup('Nilai', 'nilai.php', 'Daftar Nilai', 'Input Nilai', $cur); endif; ?>

            <?php if ($role === 'siswa'): ?>
              <li class="nav-item"><a class="nav-link<?= $cur === 'nilai_saya.php' ? ' active' : '' ?>" href="nilai_saya.php">Rapor Saya</a></li>
            <?php endif; ?>
          </ul>

          <ul class="navbar-nav menu-right">
            <li class="nav-item"><a class="nav-link<?= ($cur === 'profil.php' && $aksi_q !== 'edit') ? ' active' : '' ?>" href="profil.php">Profil</a></li>
            <li class="nav-item"><a class="nav-link<?= ($cur === 'profil.php' && $aksi_q === 'edit') ? ' active' : '' ?>" href="profil.php?aksi=edit">Ubah Profil</a></li>
            <li class="nav-item"><a class="nav-link" href="logout.php">Keluar</a></li>
          </ul>
        </div>
      </div>
    </nav>
    <!-- end navbar -->

    <main class="page">
      <div class="head">
        <div class="eyebrow"><?= e($eyebrow) ?></div>
        <h1><?= e($judul) ?></h1>
        <?php if ($sub !== ''): ?><p><?= e($sub) ?></p><?php endif; ?>
      </div>
<?php else: ?>
    <header class="kop kop-tengah">
      <div class="kop-inst">SMK Sangkuriang 1 Cimahi</div>
      <div class="kop-app">Sistem Informasi Pengelolaan Nilai</div>
    </header>
    <main class="page">
<?php endif; ?>
