<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: index.php");
    exit;
}
include 'koneksi.php';

$success_msg = "";
$error_msg = "";
$last_id = null;

// Proses form saat disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_buku = intval($_POST['id_buku']);
    $nama_siswa = trim($_POST['nama_siswa']);
    $nis = trim($_POST['nis']);
    $tanggal_pengajuan = date('Y-m-d'); // Tanggal hari ini
    $jenis_transaksi = 'Pinjam';
    $status = 'Disetujui'; // Admin langsung menyetujui peminjaman

    if (!empty($id_buku) && !empty($nama_siswa) && !empty($nis)) {
        $stmt = $conn->prepare("INSERT INTO transaksi (id_buku, nama_siswa, nis, tanggal_pengajuan, jenis_transaksi, status) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssss", $id_buku, $nama_siswa, $nis, $tanggal_pengajuan, $jenis_transaksi, $status);
        
        if ($stmt->execute()) {
            $last_id = $conn->insert_id;
            $success_msg = "Peminjaman berhasil dicatat dan disetujui!";
        } else {
            $error_msg = "Gagal menyimpan transaksi: " . $conn->error;
        }
        $stmt->close();
    } else {
        $error_msg = "Semua kolom wajib diisi!";
    }
}

// Ambil daftar buku untuk dropdown
$q_books = $conn->query("SELECT id, kode_buku, judul FROM books ORDER BY judul ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Transaksi & Struk Peminjaman</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-soft: #f4f6f9;
            --primary-navy: #1a365d;
            --accent-gold: #c5a059;
            --border-soft: #e2e8f0;
        }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: var(--bg-soft); color: #2d3748; }
        .card-custom { background: #fff; border: 1px solid var(--border-soft); border-radius: 14px; box-shadow: 0 4px 20px rgba(0,0,0,0.02); }
        .btn-soft-gold { background-color: var(--accent-gold); color: #fff; font-weight: 600; border: none; }
        .btn-soft-gold:hover { background-color: #b08d47; color: #fff; }
        
        /* Gaya Khusus Struk / Karcis Cetak */
        @media print {
            body * { visibility: hidden; }
            #printable-receipt, #printable-receipt * { visibility: visible; }
            #printable-receipt { position: absolute; left: 0; top: 0; width: 100%; box-shadow: none !important; border: none !important; }
            .no-print { display: none !important; }
        }
        .receipt-box { max-width: 400px; margin: auto; background: #fff; border: 2px dashed #cbd5e0; padding: 20px; border-radius: 12px; }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg bg-white shadow-sm sticky-top py-3 no-print">
        <div class="container">
            <a class="navbar-brand fw-bold" href="dashboard.php" style="color: var(--primary-navy);">
                <i class="bi bi-book text-warning"></i> PERPUS API
            </a>
            <div class="ms-auto">
                <a href="dashboard.php" class="btn btn-outline-secondary btn-sm rounded-pill px-3">Kembali ke Dashboard</a>
            </div>
        </div>
    </nav>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-7">
                
                <?php if (!empty($success_msg)): ?>
                    <div class="alert alert-success alert-dismissible fade show rounded-4 mb-4 no-print" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i> <?= $success_msg; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if (!empty($error_msg)): ?>
                    <div class="alert alert-danger alert-dismissible fade show rounded-4 mb-4 no-print" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= $error_msg; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Jika Transaksi Berhasil, Tampilkan Struk & QR Code -->
                <?php if ($last_id): 
                    // Ambil detail transaksi untuk struk
                    $q_trx = $conn->query("SELECT t.*, b.judul, b.kode_buku FROM transaksi t JOIN books b ON t.id_buku = b.id WHERE t.id = $last_id");
                    $trx = $q_trx->fetch_assoc();
                    
                    // Data teks untuk QR Code (bisa dibaca scanner perpustakaan)
                    $qr_data = "ID TRX: {$trx['id']} | SISWA: {$trx['nama_siswa']} ({$trx['nis']}) | BUKU: {$trx['judul']} | TGL: {$trx['tanggal_pengajuan']}";
                    $qr_api_url = "https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=" . urlencode($qr_data);
                ?>
                    <div class="card card-custom p-4 mb-4 text-center" id="printable-receipt">
                        <div class="receipt-box">
                            <h5 class="fw-bold mb-1" style="color: var(--primary-navy);">PERPUSTAKAAN PERPUS API</h5>
                            <p class="text-muted small mb-3">Struk Bukti Peminjaman Buku</p>
                            <hr class="border-secondary opacity-25">
                            <div class="text-start small mb-3">
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="text-muted">ID Transaksi:</span>
                                    <span class="fw-bold">#<?= $trx['id']; ?></span>
                                </div>
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="text-muted">Tanggal:</span>
                                    <span><?= $trx['tanggal_pengajuan']; ?></span>
                                </div>
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="text-muted">Nama Siswa:</span>
                                    <span class="fw-bold"><?= htmlspecialchars($trx['nama_siswa']); ?></span>
                                </div>
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="text-muted">NIS:</span>
                                    <span><?= htmlspecialchars($trx['nis']); ?></span>
                                </div>
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="text-muted">Kode Buku:</span>
                                    <span class="badge bg-light text-dark border"><?= $trx['kode_buku']; ?></span>
                                </div>
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="text-muted">Judul Buku:</span>
                                    <span class="fw-bold text-end" style="max-width: 180px;"><?= htmlspecialchars($trx['judul']); ?></span>
                                </div>
                            </div>
                            <hr class="border-secondary opacity-25">
                            <!-- QR Code Integrasi API Luar -->
                            <div class="my-3">
                                <img src="<?= $qr_api_url; ?>" alt="QR Code Peminjaman" class="img-fluid border p-1 bg-white" style="width: 130px; height: 130px;">
                            </div>
                            <p class="text-muted" style="font-size: 0.7rem;">Simpan atau tunjukkan struk/QR code ini saat pengembalian buku.</p>
                        </div>

                        <div class="mt-4 no-print d-flex justify-content-center gap-2">
                            <button onclick="window.print()" class="btn btn-dark btn-sm rounded-pill px-4">
                                <i class="bi bi-printer me-1"></i> Cetak Struk
                            </button>
                            <a href="admin_pinjam.php" class="btn btn-outline-secondary btn-sm rounded-pill px-4">
                                <i class="bi bi-plus-circle me-1"></i> Transaksi Baru
                            </a>
                        </div>
                    </div>
                <?php else: ?>

                    <!-- Form Input Peminjaman oleh Admin -->
                    <div class="card card-custom p-4">
                        <h4 class="fw-bold mb-1" style="color: var(--primary-navy);"><i class="bi bi-journal-check me-2 text-warning"></i> Form Peminjaman Buku</h4>
                        <p class="text-muted small mb-4">Catat peminjaman buku untuk siswa langsung melalui panel admin.</p>
                        
                        <form action="" method="POST">
                            <div class="mb-3">
                                <label for="id_buku" class="form-label small fw-semibold">Pilih Buku Katalog</label>
                                <select name="id_buku" id="id_buku" class="form-select form-control-soft" required>
                                    <option value="">-- Cari atau Pilih Buku --</option>
                                    <?php while ($buku = $q_books->fetch_assoc()): ?>
                                        <option value="<?= $buku['id']; ?>">[<?= $buku['kode_buku']; ?>] <?= htmlspecialchars($buku['judul']); ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label for="nama_siswa" class="form-label small fw-semibold">Nama Lengkap Siswa</label>
                                <input type="text" name="nama_siswa" id="nama_siswa" class="form-control" placeholder="Contoh: Budi Santoso" required>
                            </div>

                            <div class="mb-4">
                                <label for="nis" class="form-label small fw-semibold">Nomor Induk Siswa (NIS)</label>
                                <input type="text" name="nis" id="nis" class="form-control" placeholder="Contoh: 12345/001" required>
                            </div>

                            <button type="submit" class="btn btn-soft-gold w-100 py-2 rounded-pill shadow-sm">
                                <i class="bi bi-check-circle me-2"></i> Proses Peminjaman & Buat Struk
                            </button>
                        </form>
                    </div>

                <?php endif; ?>

            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>