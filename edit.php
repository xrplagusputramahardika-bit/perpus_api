<?php
session_start();
if (!isset($_SESSION['admin'])) { 
    header("Location: login.php"); 
    exit; 
}
include 'koneksi.php';

$id = intval($_GET['id']);
$data = $conn->query("SELECT * FROM books WHERE id = $id")->fetch_assoc();

if (!$data) {
    header("Location: dashboard.php");
    exit;
}

$error = "";
if (isset($_POST['update'])) {
    $kode_buku    = $_POST['kode_buku'];
    $judul        = $_POST['judul'];
    $penulis      = $_POST['penulis'];
    $kategori     = $_POST['kategori'];
    $tahun_terbit = $_POST['tahun_terbit'];
    $penerbit     = $_POST['penerbit'];
    $nama_cover   = $data['cover']; // Default gunakan cover lama

    // Penanganan Upload Cover Baru (jika ada)
    if (isset($_FILES['cover']) && $_FILES['cover']['error'] === 0) {
        $allowed_ext = ['jpg', 'jpeg', 'png', 'webp'];
        $file_name   = $_FILES['cover']['name'];
        $file_tmp    = $_FILES['cover']['tmp_name'];
        $file_ext    = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        if (in_array($file_ext, $allowed_ext)) {
            $nama_cover_baru = uniqid() . '.' . $file_ext;
            
            if (!is_dir('uploads')) {
                mkdir('uploads', 0777, true);
            }
            
            if (move_uploaded_file($file_tmp, 'uploads/' . $nama_cover_baru)) {
                // Hapus file cover lama jika ada di folder
                if (!empty($data['cover']) && file_exists('uploads/' . $data['cover'])) {
                    unlink('uploads/' . $data['cover']);
                }
                $nama_cover = $nama_cover_baru;
            }
        } else {
            $error = "Format gambar tidak didukung (Gunakan JPG, JPEG, PNG, atau WEBP).";
        }
    }

    if (empty($error)) {
        $stmt = $conn->prepare("UPDATE books SET kode_buku=?, judul=?, penulis=?, kategori=?, tahun_terbit=?, penerbit=?, cover=? WHERE id=?");
        $stmt->bind_param("ssssissi", $kode_buku, $judul, $penulis, $kategori, $tahun_terbit, $penerbit, $nama_cover, $id);
        
        if ($stmt->execute()) {
            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Gagal memperbarui data ke database.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Executive Library - Edit Buku</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Google Fonts: Plus Jakarta Sans -->
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

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-soft);
            color: var(--text-main);
        }

        /* Navbar Lembut & Elegan */
        .navbar-soft {
            background-color: #ffffff;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.04);
            border-bottom: 1px solid var(--border-soft);
        }

        .navbar-brand {
            color: var(--primary-navy) !important;
            font-weight: 700;
            letter-spacing: 0.5px;
        }

        .nav-link {
            color: var(--text-muted) !important;
            font-weight: 500;
            transition: color 0.2s ease;
        }

        .nav-link:hover, .nav-link.active {
            color: var(--accent-gold) !important;
        }

        /* Kartu Form Soft */
        .card-form {
            background-color: var(--card-bg);
            border: 1px solid var(--border-soft);
            border-radius: 14px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02);
        }

        .card-header-soft {
            background-color: #f8fafc;
            border-bottom: 1px solid var(--border-soft);
            color: var(--primary-navy);
            border-top-left-radius: 14px !important;
            border-top-right-radius: 14px !important;
            padding: 1.25rem 1.5rem;
        }

        /* Form Control Soft */
        .form-control-soft {
            background-color: #ffffff;
            border: 1px solid var(--border-soft);
            color: var(--text-main);
            border-radius: 10px;
            padding: 0.65rem 1rem;
        }

        .form-control-soft:focus {
            background-color: #ffffff;
            border-color: var(--accent-gold);
            box-shadow: 0 0 0 0.2rem rgba(197, 160, 89, 0.15);
            color: var(--text-main);
        }

        /* Tombol Soft Gold */
        .btn-soft-gold {
            background-color: var(--accent-gold);
            color: #ffffff;
            font-weight: 600;
            border: none;
            transition: all 0.2s ease;
        }

        .btn-soft-gold:hover {
            background-color: #b08d47;
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(197, 160, 89, 0.3);
        }

        .current-cover {
            width: 70px;
            height: 95px;
            object-fit: cover;
            border-radius: 6px;
            border: 1px solid var(--border-soft);
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-soft sticky-top py-3">
        <div class="container">
            <a class="navbar-brand fs-4 d-flex align-items-center gap-2" href="dashboard.php">
                <i class="bi bi-book" style="color: var(--accent-gold);"></i> PERPUS API
            </a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav ms-auto align-items-lg-center gap-3">
                    <li class="nav-item"><a class="nav-link px-3" href="dashboard.php">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link px-3" href="tambah.php">Tambah Buku</a></li>
                    <li class="nav-item"><a class="nav-link px-3" href="laporan.php">Laporan</a></li>
                    <li class="nav-item ms-lg-2">
                        <a class="btn btn-outline-danger btn-sm px-3 py-2 rounded-pill" href="logout.php">
                            <i class="bi bi-box-arrow-right me-1"></i> Logout
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container py-5" style="max-width: 700px;">
        <div class="card card-form">
            <div class="card-header-soft">
                <h5 class="mb-0 fw-bold"><i class="bi bi-pencil-square me-2 text-warning"></i> Form Edit Buku</h5>
            </div>
            <div class="card-body p-4">
                
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?= $error; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <form method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-muted">KODE BUKU</label>
                        <input type="text" class="form-control form-control-soft" name="kode_buku" value="<?= htmlspecialchars($data['kode_buku']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-muted">JUDUL BUKU</label>
                        <input type="text" class="form-control form-control-soft" name="judul" value="<?= htmlspecialchars($data['judul']); ?>" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold small text-muted">PENULIS</label>
                            <input type="text" class="form-control form-control-soft" name="penulis" value="<?= htmlspecialchars($data['penulis']); ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold small text-muted">KATEGORI</label>
                            <input type="text" class="form-control form-control-soft" name="kategori" value="<?= htmlspecialchars($data['kategori']); ?>" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold small text-muted">TAHUN TERBIT</label>
                            <input type="number" class="form-control form-control-soft" name="tahun_terbit" value="<?= htmlspecialchars($data['tahun_terbit']); ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold small text-muted">PENERBIT</label>
                            <input type="text" class="form-control form-control-soft" name="penerbit" value="<?= htmlspecialchars($data['penerbit']); ?>" required>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-semibold small text-muted">COVER BUKU</label>
                        <div class="d-flex align-items-center gap-3 mb-2">
                            <?php if (!empty($data['cover']) && file_exists('uploads/' . $data['cover'])): ?>
                                <img src="uploads/<?= $data['cover']; ?>" alt="Cover Saat Ini" class="current-cover">
                            <?php else: ?>
                                <img src="https://via.placeholder.com/70x95?text=No+Cover" alt="No Cover" class="current-cover">
                            <?php endif; ?>
                            <div>
                                <input type="file" class="form-control form-control-soft" name="cover" accept="image/*">
                                <div class="form-text small mt-1">Biarkan kosong jika tidak ingin mengubah cover.</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                        <a href="dashboard.php" class="btn btn-outline-secondary px-4 py-2 rounded-pill">
                            <i class="bi bi-arrow-left me-1"></i> Kembali
                        </a>
                        <button type="submit" name="update" class="btn btn-soft-gold px-4 py-2 rounded-pill shadow-sm">
                            <i class="bi bi-check-lg me-1"></i> Perbarui Buku
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>