<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: index.php");
    exit;
}
include 'koneksi.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Executive Library - Laporan & Export</title>
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

        .navbar-soft {
            background-color: #ffffff;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.04);
            border-bottom: 1px solid var(--border-soft);
        }

        .navbar-brand {
            color: var(--primary-navy) !important;
            font-weight: 700;
        }

        .nav-link {
            color: var(--text-muted) !important;
            font-weight: 500;
        }

        .nav-link:hover, .nav-link.active {
            color: var(--accent-gold) !important;
        }

        .card-report {
            background-color: var(--card-bg);
            border: 1px solid var(--border-soft);
            border-radius: 14px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02);
        }

        .btn-soft-gold {
            background-color: var(--accent-gold);
            color: #ffffff;
            font-weight: 600;
            border: none;
        }

        .btn-soft-gold:hover {
            background-color: #b08d47;
            color: #ffffff;
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
                    <li class="nav-item"><a class="nav-link active px-3" href="laporan.php">Laporan</a></li>
                    <li class="nav-item ms-lg-2">
                        <a class="btn btn-outline-danger btn-sm px-3 py-2 rounded-pill" href="logout.php">
                            <i class="bi bi-box-arrow-right me-1"></i> Logout
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container py-5">
        <!-- Header -->
        <div class="mb-5">
            <h2 class="fw-bold mb-1" style="color: var(--primary-navy);">Pusat Laporan & Ekspor Data</h2>
            <p class="text-muted mb-0">Unduh rekapitulasi data literatur perpustakaan dalam format Excel atau PDF.</p>
        </div>

        <!-- Kartu Aksi Ekspor -->
        <div class="row g-4">
            <!-- Card Excel -->
            <div class="col-md-6">
                <div class="card card-report p-4 h-100">
                    <div class="d-flex align-items-center mb-3">
                        <div class="fs-1 text-success me-3"><i class="bi bi-file-earmark-excel-fill"></i></div>
                        <div>
                            <h5 class="fw-bold mb-1">Ekspor ke Excel</h5>
                            <p class="text-muted small mb-0">Unduh seluruh data buku ke dalam format spreadsheet Excel (.xlsx / .xls).</p>
                        </div>
                    </div>
                    <div class="mt-auto pt-3 border-top">
                        <a href="export_excel.php" class="btn btn-success px-4 py-2 w-100">
                            <i class="bi bi-download me-2"></i> Unduh File Excel
                        </a>
                    </div>
                </div>
            </div>

            <!-- Card PDF -->
            <div class="col-md-6">
                <div class="card card-report p-4 h-100">
                    <div class="d-flex align-items-center mb-3">
                        <div class="fs-1 text-danger me-3"><i class="bi bi-file-earmark-pdf-fill"></i></div>
                        <div>
                            <h5 class="fw-bold mb-1">Ekspor ke PDF</h5>
                            <p class="text-muted small mb-0">Buka dan cetak rekapitulasi literatur perpustakaan dalam bentuk dokumen PDF.</p>
                        </div>
                    </div>
                    <div class="mt-auto pt-3 border-top">
                        <a href="export_pdf.php" target="_blank" class="btn btn-danger px-4 py-2 w-100">
                            <i class="bi bi-printer-fill me-2"></i> Cetak / Unduh PDF
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>