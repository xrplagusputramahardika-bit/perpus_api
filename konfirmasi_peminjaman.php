<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: index.php");
    exit;
}
include 'koneksi.php';

// Proses Aksi Setujui atau Tolak
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $action = $_GET['action'];

    if ($action == 'approve') {
        $conn->query("UPDATE transaksi SET status = 'Disetujui' WHERE id = $id");
        $_SESSION['success'] = "Transaksi berhasil disetujui!";
    } elseif ($action == 'reject') {
        $conn->query("UPDATE transaksi SET status = 'Ditolak' WHERE id = $id");
        $_SESSION['success'] = "Transaksi ditolak.";
    }
    header("Location: konfirmasi_peminjaman.php");
    exit;
}

// Ambil semua data transaksi yang masih Pending
$query = "SELECT transaksi.*, books.judul, books.kode_buku FROM transaksi 
          JOIN books ON transaksi.id_buku = books.id 
          ORDER BY transaksi.id DESC";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konfirmasi Peminjaman - Perpus API</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-soft: #f4f6f9;
            --card-bg: #ffffff;
            --text-main: #2d3748;
            --text-muted: #718096;
            --primary-navy: #1a365d;
            --accent-gold: #c5a059;
            --border-soft: #e2e8f0;
        }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: var(--bg-soft); color: var(--text-main); }
        .navbar-soft { background-color: #ffffff; box-shadow: 0 2px 15px rgba(0, 0, 0, 0.04); border-bottom: 1px solid var(--border-soft); }
        .navbar-brand { color: var(--primary-navy) !important; font-weight: 700; }
        .nav-link { color: var(--text-muted) !important; font-weight: 500; }
        .nav-link:hover, .nav-link.active { color: var(--accent-gold) !important; }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-soft sticky-top py-3">
        <div class="container">
            <a class="navbar-brand fs-4" href="dashboard.php"><i class="bi bi-book text-warning"></i> PERPUS API</a>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-lg-center gap-3">
                    <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link active" href="konfirmasi_peminjaman.php">Konfirmasi Peminjaman</a></li>
                    <li class="nav-item"><a class="nav-link" href="laporan.php">Laporan</a></li>
                    <li class="nav-item ms-lg-2"><a class="btn btn-outline-danger btn-sm px-3 py-2 rounded-pill" href="logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container py-5">
        <h2 class="fw-bold mb-3" style="color: var(--primary-navy);">Konfirmasi Transaksi Buku</h2>
        <p class="text-muted mb-4">Setujui atau tolak pengajuan peminjaman dan pengembalian buku dari siswa.</p>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= $_SESSION['success']; unset($_SESSION['success']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Nama Siswa (NIS)</th>
                            <th>Buku</th>
                            <th>Jenis</th>
                            <th>Tanggal</th>
                            <th>Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $no = 1;
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $badge_status = 'bg-warning text-dark';
                                if ($row['status'] == 'Disetujui') $badge_status = 'bg-success';
                                if ($row['status'] == 'Ditolak') $badge_status = 'bg-danger';

                                $badge_jenis = $row['jenis_transaksi'] == 'Pinjam' ? 'bg-primary' : 'bg-info text-dark';
                        ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td>
                                <strong><?= htmlspecialchars($row['nama_siswa']); ?></strong><br>
                                <small class="text-muted">NIS: <?= htmlspecialchars($row['nis']); ?></small>
                            </td>
                            <td>
                                <strong><?= htmlspecialchars($row['judul']); ?></strong><br>
                                <small class="text-muted">Kode: <?= htmlspecialchars($row['kode_buku']); ?></small>
                            </td>
                            <td><span class="badge <?= $badge_jenis; ?>"><?= $row['jenis_transaksi']; ?></span></td>
                            <td><?= $row['tanggal_pengajuan']; ?></td>
                            <td><span class="badge <?= $badge_status; ?>"><?= $row['status']; ?></span></td>
                            <td class="text-center">
                                <?php if ($row['status'] == 'Pending'): ?>
                                    <a href="konfirmasi_peminjaman.php?action=approve&id=<?= $row['id']; ?>" class="btn btn-sm btn-success px-2 py-1" onclick="return confirm('Setujui transaksi ini?')"><i class="bi bi-check-lg"></i> Setuju</a>
                                    <a href="konfirmasi_peminjaman.php?action=reject&id=<?= $row['id']; ?>" class="btn btn-sm btn-danger px-2 py-1" onclick="return confirm('Tolak transaksi ini?')"><i class="bi bi-x-lg"></i> Tolak</a>
                                <?php else: ?>
                                    <span class="text-muted small fst-italic">Selesai</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php 
                            }
                        } else {
                            echo '<tr><td colspan="7" class="text-center py-4 text-muted">Belum ada pengajuan transaksi dari siswa.</td></tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>