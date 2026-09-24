<?php
session_start();
include 'koneksi.php';

$error = '';
if (isset($_POST['login'])) {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $conn->prepare("SELECT * FROM admins WHERE username = ? LIMIT 1");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        // Untuk praktik (jika password belum di-hash, bisa langsung dicocokkan atau pakai password_verify)
        if ($password == $row['password'] || password_verify($password, $row['password'])) {
            $_SESSION['admin'] = $row['username'];
            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Password salah!";
        }
    } else {
        $error = "Username tidak ditemukan!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin Perpustakaan</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body class="login-page">
    <div class="login-orbit orbit-one"></div>
    <div class="login-orbit orbit-two"></div>
    <main class="login-layout">
        <section class="login-intro">
            <a class="brand" href="index.php"><span class="brand-mark">P</span><span>PERPUS <em>API</em></span></a>
            <div class="intro-copy">
                <p class="eyebrow">Ruang kerja pustaka</p>
                <h1>Rawat koleksi.<br><span>Temukan cerita.</span></h1>
                <p class="intro-text">Satu ruang untuk mengelola katalog perpustakaan dengan lebih tertata, cepat, dan menyenangkan.</p>
            </div>
            <div class="intro-meta"><span class="meta-line"></span><span>ADMIN CONSOLE / 01</span></div>
        </section>
        <section class="login-card">
            <div class="card-heading"><div class="eyebrow">Akses terbatas</div><h2>Selamat datang.</h2><p>Masuk untuk melanjutkan ke dashboard perpustakaan.</p></div>
            <?php if ($error): ?><div class="login-error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div><?php endif; ?>
            <form method="POST" class="login-form">
                <div class="field"><label for="username">Username</label><input type="text" name="username" id="username" placeholder="Masukkan username" autocomplete="username" required></div>
                <div class="field"><label for="password">Password</label><input type="password" name="password" id="password" placeholder="Masukkan password" autocomplete="current-password" required></div>
                <button type="submit" name="login">Masuk ke dashboard <span aria-hidden="true">→</span></button>
            </form>
            <p class="card-footer">Sistem informasi koleksi buku <span>•</span> 2026</p>
        </section>
    </main>
</body>
</html>