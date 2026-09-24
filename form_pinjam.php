<?php
include 'koneksi.php';
$sukses = "";

if (isset($_POST['ajukan'])) {
    $id_buku = intval($_POST['id_buku']);
    $nama = $conn->real_escape_string($_POST['nama_siswa']);
    $nis = $conn->real_escape_string($_POST['nis']);
    $jenis = $_POST['jenis_transaksi'];
    $tanggal = date('Y-m-d');

    $query = "INSERT INTO transaksi (id_buku, nama_siswa, nis, tanggal_pengajuan, jenis_transaksi, status) 
              VALUES ($id_buku, '$nama', '$nis', '$tanggal', '$jenis', 'Pending')";
    
    if ($conn->query($query)) {
        $sukses = "Pengajuan berhasil dikirim! Silakan tunggu konfirmasi dari Admin perpustakaan.";
    }
}

$buku_list = $conn->query("SELECT * FROM books ORDER BY judul ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Peminjaman Buku - Siswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f4f6f9; }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                    <h3 class="fw-bold text-center mb-3" style="color: #1a365d;">Formulir Peminjaman / Pengembalian</h3>
                    <p class="text-muted text-center small mb-4">Isi data di bawah ini untuk mengajukan permohonan ke admin.</p>

                    <?php if ($sukses): ?>
                        <div class="alert alert-success"><?= $sukses; ?></div>
                    <?php endif; ?>

                    <form action="" method="POST">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nama Lengkap Siswa</label>
                            <input type="text" name="nama_siswa" class="form-control" required placeholder="Contoh: Ahmad Fauzi">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">NIS (Nomor Induk Siswa)</label>
                            <input type="text" name="nis" class="form-control" required placeholder="Contoh: 123456">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Pilih Buku</label>
                            <select name="id_buku" class="form-select" required>
                                <option value="">-- Pilih Buku --</option>
                                <?php while($b = $buku_list->fetch_assoc()): ?>
                                    <option value="<?= $b['id']; ?>"><?= htmlspecialchars($b['judul']); ?> (Kode: <?= $b['kode_buku']; ?>)</option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Jenis Transaksi</label>
                            <select name="jenis_transaksi" class="form-select" required>
                                <option value="Pinjam">Peminjaman Buku</option>
                                <option value="Kembali">Pengembalian Buku</option>
                            </select>
                        </div>
                        <button type="submit" name="ajukan" class="btn btn-warning w-100 fw-bold py-2 text-white" style="background-color: #c5a059;">Kirim Pengajuan</button>
                    </form>
                    <div class="text-center mt-3">
                        <a href="dashboard.php" class="text-decoration-none small text-muted">← Kembali ke Dashboard Admin</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>