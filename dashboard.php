<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: index.php");
    exit;
}
include 'koneksi.php';

// Mengambil Statistik dari Database Buku
$q_total =$conn->query("SELECT COUNT(*) as total FROM books")->fetch_assoc()['total'];
$q_kat =$conn->query("SELECT COUNT(DISTINCT kategori) as total FROM books")->fetch_assoc()['total'];
$q_penulis =$conn->query("SELECT COUNT(DISTINCT penulis) as total FROM books")->fetch_assoc()['total'];
$q_tahun =$conn->query("SELECT MAX(tahun_terbit) as tahun FROM books")->fetch_assoc()['tahun'];

// Mengambil Statistik Transaksi (Opsional aman jika tabel transaksi sudah ada)
$q_pending = 0;
$q_pinjam_aktif = 0;
$check_transaksi =$conn->query("SHOW TABLES LIKE 'transaksi'");
if ($check_transaksi->num_rows > 0) {
    $q_pending =$conn->query("SELECT COUNT(*) as total FROM transaksi WHERE status = 'Pending'")->fetch_assoc()['total'];
    $q_pinjam_aktif =$conn->query("SELECT COUNT(*) as total FROM transaksi WHERE status = 'Disetujui' AND jenis_transaksi = 'Pinjam'")->fetch_assoc()['total'];
}

// Mengambil daftar kategori unik untuk dropdown filter
$q_list_kat =$conn->query("SELECT DISTINCT kategori FROM books WHERE kategori IS NOT NULL AND kategori != '' ORDER BY kategori ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Executive Library - Dashboard</title>
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
            --icon-bg: #fdfbf7;
            --icon-border: #f3ebd8;
            --input-bg: #ffffff;
        }

        /* Variabel Warna Tema Gelap (Dark Mode) */
        [data-bs-theme="dark"] {
            --bg-soft: #121824;
            --card-bg: #1e293b;
            --text-main: #f1f5f9;
            --text-muted: #94a3b8;
            --primary-navy: #60a5fa;
            --accent-gold: #d4af37;
            --border-soft: #334155;
            --icon-bg: #273548;
            --icon-border: #384b63;
            --input-bg: #1e293b;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-soft);
            color: var(--text-main);
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .navbar-soft {
            background-color: var(--card-bg) !important;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.04);
            border-bottom: 1px solid var(--border-soft);
            transition: background-color 0.3s ease, border-color 0.3s ease;
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

        .card-stat-soft {
            background-color: var(--card-bg);
            border: 1px solid var(--border-soft);
            border-radius: 14px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02);
            transition: transform 0.2s ease, box-shadow 0.2s ease, background-color 0.3s ease, border-color 0.3s ease;
        }

        .card-stat-soft:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 25px rgba(197, 160, 89, 0.15);
            border-color: var(--accent-gold);
        }

        .icon-box-soft {
            width: 48px;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: var(--icon-bg);
            border: 1px solid var(--icon-border);
            border-radius: 12px;
            color: var(--accent-gold);
            font-size: 1.3rem;
        }

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

        .book-card {
            background-color: var(--card-bg);
            border: 1px solid var(--border-soft);
            border-radius: 14px;
            transition: transform 0.2s ease, box-shadow 0.2s ease, background-color 0.3s ease, border-color 0.3s ease;
            height: 100%;
            overflow: hidden;
        }

        .book-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
            border-color: var(--accent-gold);
        }

        .book-cover-wrapper {
            height: 200px;
            background-color: var(--border-soft);
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            border-bottom: 1px solid var(--border-soft);
            cursor: pointer;
        }

        .book-cover-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .book-card:hover .book-cover-img {
            transform: scale(1.05);
        }

        .form-control-soft {
            background-color: var(--input-bg);
            border: 1px solid var(--border-soft);
            color: var(--text-main);
            border-radius: 10px;
            transition: background-color 0.3s ease, border-color 0.3s ease, color 0.3s ease;
        }

        .form-control-soft:focus {
            background-color: var(--input-bg);
            border-color: var(--accent-gold);
            box-shadow: 0 0 0 0.2rem rgba(197, 160, 89, 0.15);
            color: var(--text-main);
        }

        .badge-soft {
            background-color: var(--icon-bg);
            color: var(--accent-gold);
            border: 1px solid var(--icon-border);
            font-weight: 600;
        }

        .pagination .page-item .page-link {
            background-color: var(--card-bg);
            color: var(--text-main);
            border: 1px solid var(--border-soft);
            border-radius: 8px;
            margin: 0 3px;
            padding: 0.5rem 0.8rem;
        }
        .pagination .page-item.active .page-link {
            background-color: var(--accent-gold);
            border-color: var(--accent-gold);
            color: #fff;
        }

        /* Penyesuaian Komponen Dropdown/Modal untuk Dark Mode */
        .dropdown-menu {
            background-color: var(--card-bg);
            border: 1px solid var(--border-soft);
        }
        .dropdown-item {
            color: var(--text-main);
        }
        .dropdown-item:hover {
            background-color: var(--border-soft);
            color: var(--text-main);
        }
        .modal-content {
            background-color: var(--card-bg);
            color: var(--text-main);
        }
    </style>
</head>
<body>

    <!-- Navbar Soft Luxury -->
    <nav class="navbar navbar-expand-lg navbar-soft sticky-top py-3">
        <div class="container">
            <a class="navbar-brand fs-4 d-flex align-items-center gap-2" href="dashboard.php">
                <i class="bi bi-book" style="color: var(--accent-gold);"></i> PERPUS API
            </a>
            <div class="d-flex align-items-center gap-2">
                <!-- Tombol Toggle Dark Mode (Mobile & Desktop) -->
                <button id="darkModeToggle" class="btn btn-outline-secondary btn-sm rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;" title="Ganti Tema">
                    <i class="bi bi-moon-fill" id="darkModeIcon"></i>
                </button>
                <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <i class="bi bi-list fs-2 text-secondary"></i>
                </button>
            </div>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-lg-center gap-3">
                    <li class="nav-item"><a class="nav-link active px-3" href="dashboard.php">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link px-3" href="tambah.php">Tambah Buku</a></li>
                    <li class="nav-item"><a class="nav-link px-3" href="konfirmasi_peminjaman.php">Peminjaman <span class="badge bg-danger rounded-pill"><?= $q_pending; ?></span></a></li>
                    <li class="nav-item"><a class="nav-link px-3" href="laporan.php">Laporan</a></li>
                    <li class="nav-item"><a class="nav-link px-3" href="admin_pinjam.php">Pinjamkan Buku</a></li>
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
        <!-- Header Section -->
        <div class="row align-items-center mb-5">
            <div class="col-md-7">
                <h2 class="fw-bold mb-1" style="color: var(--primary-navy) !important;">Dashboard Admin</h2>
                <p class="text-muted mb-0">Kelola katalog literatur dan verifikasi peminjaman buku perpustakaan.</p>
            </div>
            <div class="col-md-5 text-md-end mt-3 mt-md-0 d-flex justify-content-md-end gap-2 flex-wrap">
                <a href="konfirmasi_peminjaman.php" class="btn btn-outline-warning text-dark px-3 py-2 rounded-pill shadow-sm border-warning">
                    <i class="bi bi-bell me-1"></i> Verifikasi (<span class="fw-bold"><?= $q_pending; ?></span>)
                </a>
                <a href="tambah.php" class="btn btn-soft-gold px-4 py-2 rounded-pill shadow-sm">
                    <i class="bi bi-plus-lg me-2"></i> Tambah Buku
                </a>
            </div>
        </div>

        <!-- Statistik Cards Soft -->
        <div class="row g-4 mb-5">
            <div class="col-md-3 col-sm-6">
                <div class="card card-stat-soft p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted text-uppercase fw-semibold" style="font-size: 0.75rem;">Total Buku</span>
                            <h2 class="fw-bold mb-0 mt-1" style="color: var(--primary-navy);"><?= $q_total; ?></h2>
                        </div>
                        <div class="icon-box-soft"><i class="bi bi-journal-text"></i></div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="card card-stat-soft p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted text-uppercase fw-semibold" style="font-size: 0.75rem;">Pinjaman Aktif</span>
                            <h2 class="fw-bold mb-0 mt-1" style="color: var(--primary-navy);"><?= $q_pinjam_aktif; ?></h2>
                        </div>
                        <div class="icon-box-soft"><i class="bi bi-arrow-left-right"></i></div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="card card-stat-soft p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted text-uppercase fw-semibold" style="font-size: 0.75rem;">Pending Verifikasi</span>
                            <h2 class="fw-bold mb-0 mt-1 text-warning"><?= $q_pending; ?></h2>
                        </div>
                        <div class="icon-box-soft"><i class="bi bi-clock-history"></i></div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="card card-stat-soft p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted text-uppercase fw-semibold" style="font-size: 0.75rem;">Kategori Buku</span>
                            <h2 class="fw-bold mb-0 mt-1" style="color: var(--primary-navy);"><?= $q_kat; ?></h2>
                        </div>
                        <div class="icon-box-soft"><i class="bi bi-tags"></i></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bagian Katalog Buku, Pencarian & Filter Kategori (Dropdown) -->
        <div class="card border-0 shadow-sm p-4 mb-4 rounded-4 card-stat-soft">
            <div class="row align-items-center g-3">
                <div class="col-lg-4">
                    <h4 class="fw-bold mb-0" style="color: var(--primary-navy);"><i class="bi bi-grid-fill me-2 text-warning"></i> Katalog Literatur</h4>
                </div>
                <div class="col-lg-8">
                    <div class="row g-2">
                        <!-- Dropdown Filter Kategori -->
                        <div class="col-md-4">
                            <div class="dropdown w-100">
                                <button class="btn form-control-soft border text-start d-flex justify-content-between align-items-center w-100 py-2" type="button" id="dropdownCategoryButton" data-bs-toggle="dropdown" aria-expanded="false">
                                    <span id="selectedCategoryLabel" class="text-truncate"><i class="bi bi-funnel me-1 text-muted"></i> Semua Buku</span>
                                    <i class="bi bi-chevron-down small text-muted"></i>
                                </button>
                                <ul class="dropdown-menu w-100 shadow-sm border-0 rounded-3 py-2" aria-labelledby="dropdownCategoryButton" id="category-filters" style="max-height: 250px; overflow-y: auto;">
                                    <li><a class="dropdown-item py-2 active category-option" href="#" data-category="all"><i class="bi bi-check2 me-2 text-warning"></i> Semua Buku</a></li>
                                    <li><hr class="dropdown-divider my-1"></li>
                                    <?php while ($cat =$q_list_kat->fetch_assoc()): ?>
                                        <li><a class="dropdown-item py-2 category-option" href="#" data-category="<?= htmlspecialchars($cat['kategori']); ?>"><?= htmlspecialchars($cat['kategori']); ?></a></li>
                                    <?php endwhile; ?>
                                </ul>
                            </div>
                        </div>
                        <!-- Live Search Input -->
                        <div class="col-md-8">
                            <div class="input-group">
                                <span class="input-group-text border-end-0 form-control-soft"><i class="bi bi-search text-muted"></i></span>
                                <input type="text" id="searchBox" class="form-control form-control-soft border-start-0 ps-0 shadow-none" placeholder="Cari judul, penulis, kode, atau penerbit...">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Container Grid Buku Card -->
        <div class="row g-4" id="book-container">
            <div class="col-12 text-center py-5">
                <div class="spinner-border spinner-border-sm text-secondary me-2" role="status"></div>
                <span class="text-muted">Memuat data katalog buku...</span>
            </div>
        </div>

        <!-- Navigasi Pagination Dinamis -->
        <nav class="mt-5" aria-label="Page navigation">
            <ul class="pagination justify-content-center" id="pagination-container"></ul>
        </nav>
    </div>

    <!-- Modal Detail Buku (Quick View) -->
    <div class="modal fade" id="detailModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow">
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="modal-title fw-bold" style="color: var(--primary-navy);">Detail Informasi Buku</h5>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="text-center mb-3">
                        <img id="modalCover" src="" alt="Cover Buku" class="rounded shadow-sm" style="width: 130px; height: 180px; object-fit: cover; border: 1px solid var(--border-soft);">
                    </div>
                    <h5 id="modalJudul" class="fw-bold text-center mb-1"></h5>
                    <p id="modalPenulis" class="text-muted text-center small mb-3"></p>
                    <hr class="text-muted opacity-25">
                    <div class="row g-2 small">
                        <div class="col-6"><strong>Kode Buku:</strong> <span id="modalKode" class="text-muted"></span></div>
                        <div class="col-6"><strong>Kategori:</strong> <span id="modalKategori" class="text-muted"></span></div>
                        <div class="col-6"><strong>Penerbit:</strong> <span id="modalPenerbit" class="text-muted"></span></div>
                        <div class="col-6"><strong>Tahun Terbit:</strong> <span id="modalTahun" class="text-muted"></span></div>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-secondary btn-sm rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- JavaScript Fitur Canggih: Dark Mode, Live Search, Filter Dropdown, Modal, & Pagination -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // --- Logika Dark Mode ---
            const toggleBtn = document.getElementById("darkModeToggle");
            const toggleIcon = document.getElementById("darkModeIcon");
            const htmlElement = document.documentElement;

            // Cek penyimpanan lokal atau preferensi sistem
            const savedTheme = localStorage.getItem("theme");
            if (savedTheme) {
                htmlElement.setAttribute("data-bs-theme", savedTheme);
                updateThemeIcon(savedTheme);
            }

            toggleBtn.addEventListener("click", function() {
                let currentTheme = htmlElement.getAttribute("data-bs-theme");
                let newTheme = currentTheme === "dark" ? "light" : "dark";
                
                htmlElement.setAttribute("data-bs-theme", newTheme);
                localStorage.setItem("theme", newTheme);
                updateThemeIcon(newTheme);
            });

            function updateThemeIcon(theme) {
                if (theme === "dark") {
                    toggleIcon.className = "bi bi-sun-fill text-warning";
                } else {
                    toggleIcon.className = "bi bi-moon-fill text-secondary";
                }
            }

            // --- Logika Data Buku & Interaksi ---
            let allBooks = [];
            let currentCategory = "all";
            let currentPage = 1;
            const itemsPerPage = 8;

            fetch("data_json.php")
                .then(response => response.json())
                .then(data => {
                    allBooks = data;
                    filterAndRender();
                })
                .catch(error => {
                    console.error('Error fetching data:', error);
                    document.getElementById("book-container").innerHTML = `<div class="col-12 text-center text-danger py-4">Gagal memuat data dari server.</div>`;
                });

            function filterAndRender() {
                let keyword = document.getElementById("searchBox").value.toLowerCase();
                
                let filtered = allBooks.filter(buku => {
                    let matchCategory = (currentCategory === "all" || buku.kategori.toLowerCase() === currentCategory.toLowerCase());
                    let matchKeyword = buku.judul.toLowerCase().includes(keyword) ||
                                       buku.penulis.toLowerCase().includes(keyword) ||
                                       buku.kode_buku.toLowerCase().includes(keyword) ||
                                       buku.penerbit.toLowerCase().includes(keyword);
                    return matchCategory && matchKeyword;
                });

                renderCards(filtered);
                renderPagination(filtered.length);
            }

            function renderCards(data) {
                let container = document.getElementById("book-container");
                container.innerHTML = "";
                
                if(data.length === 0) {
                    container.innerHTML = `
                        <div class="col-12 text-center py-5">
                            <div class="mb-2"><i class="bi bi-journal-x fs-1 text-muted"></i></div>
                            <h6 class="text-muted fw-semibold">Tidak ada buku yang ditemukan.</h6>
                            <p class="text-muted small">Coba gunakan kata kunci atau kategori lain.</p>
                        </div>`;
                    document.getElementById("pagination-container").innerHTML = "";
                    return;
                }

                let start = (currentPage - 1) * itemsPerPage;
                let paginatedData = data.slice(start, start + itemsPerPage);

                paginatedData.forEach((buku) => {
                    let gambarCover = buku.cover ? `uploads/${buku.cover}` : `https://via.placeholder.com/300x400?text=No+Cover`;

                    let col = document.createElement("div");
                    col.className = "col-md-3 col-sm-6";
                    col.innerHTML = `
                        <div class="book-card shadow-sm d-flex flex-column">
                            <div class="book-cover-wrapper" onclick='showDetail(${JSON.stringify(buku)})' title="Klik untuk lihat detail">
                                <img src="${gambarCover}" alt="Cover ${buku.judul}" class="book-cover-img">
                            </div>
                            <div class="p-3 d-flex flex-column flex-grow-1">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="badge badge-soft" style="font-size: 0.7rem;">${buku.kode_buku}</span>
                                    <small class="text-muted" style="font-size: 0.75rem;"><i class="bi bi-calendar me-1"></i>${buku.tahun_terbit}</small>
                                </div>
                                <h6 class="fw-bold mb-1 text-truncate" style="cursor: pointer;" onclick='showDetail(${JSON.stringify(buku)})' title="${buku.judul}">${buku.judul}</h6>
                                <p class="text-muted small mb-2 text-truncate" title="${buku.penulis}"><i class="bi bi-person me-1"></i>${buku.penulis}</p>
                                <div class="mb-3">
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary border" style="font-size: 0.7rem;">${buku.kategori}</span>
                                </div>
                                <div class="mt-auto d-flex justify-content-between align-items-center pt-2 border-top border-opacity-25">
                                    <span class="text-muted text-truncate me-2" style="font-size: 0.75rem; max-width: 120px;" title="${buku.penerbit}">${buku.penerbit}</span>
                                    <div class="d-flex gap-1">
                                        <a href="edit.php?id=${buku.id}" class="btn btn-sm btn-outline-warning p-1 text-dark border-warning" title="Edit"><i class="bi bi-pencil-square"></i></a>
                                        <a href="hapus.php?id=${buku.id}" class="btn btn-sm btn-outline-danger p-1" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')" title="Hapus"><i class="bi bi-trash"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                    container.appendChild(col);
                });
            }

            function renderPagination(totalItems) {
                let totalPages = Math.ceil(totalItems / itemsPerPage);
                let paginationContainer = document.getElementById("pagination-container");
                paginationContainer.innerHTML = "";

                if (totalPages <= 1) return;

                let prevDisabled = currentPage === 1 ? "disabled" : "";
                paginationContainer.innerHTML += `
                    <li class="page-item ${prevDisabled}">
                        <a class="page-link" href="#" data-page="${currentPage - 1}"><i class="bi bi-chevron-left"></i></a>
                    </li>
                `;

                for (let i = 1; i <= totalPages; i++) {
                    let active = i === currentPage ? "active" : "";
                    paginationContainer.innerHTML += `
                        <li class="page-item ${active}">
                            <a class="page-link" href="#" data-page="${i}">${i}</a>
                        </li>
                    `;
                }

                let nextDisabled = currentPage === totalPages ? "disabled" : "";
                paginationContainer.innerHTML += `
                    <li class="page-item ${nextDisabled}">
                        <a class="page-link" href="#" data-page="${currentPage + 1}"><i class="bi bi-chevron-right"></i></a>
                    </li>
                `;

                paginationContainer.querySelectorAll(".page-link").forEach(link => {
                    link.addEventListener("click", function(e) {
                        e.preventDefault();
                        let targetPage = parseInt(this.getAttribute("data-page"));
                        if (targetPage >= 1 && targetPage <= totalPages) {
                            currentPage = targetPage;
                            filterAndRender();
                            window.scrollTo({ top: 300, behavior: 'smooth' });
                        }
                    });
                });
            }

            document.getElementById("searchBox").addEventListener("keyup", function() {
                currentPage = 1;
                filterAndRender();
            });

            // Event listener untuk pilihan kategori dropdown baru
            document.querySelectorAll(".category-option").forEach(option => {
                option.addEventListener("click", function(e) {
                    e.preventDefault();
                    
                    document.querySelectorAll(".category-option").forEach(opt => {
                        opt.classList.remove("active");
                        let icon = opt.querySelector(".bi-check2");
                        if (icon) icon.remove();
                    });

                    this.classList.add("active");
                    this.insertAdjacentHTML('afterbegin', '<i class="bi bi-check2 me-2 text-warning"></i>');

                    let selectedText = this.textContent.trim();
                    document.getElementById("selectedCategoryLabel").innerHTML = `<i class="bi bi-funnel me-1 text-muted"></i> ${selectedText}`;

                    currentCategory = this.getAttribute("data-category");
                    currentPage = 1;
                    filterAndRender();
                });
            });
        });

        function showDetail(buku) {
            let gambarCover = buku.cover ? `uploads/${buku.cover}` : `https://via.placeholder.com/300x400?text=No+Cover`;
            document.getElementById("modalCover").src = gambarCover;
            document.getElementById("modalJudul").innerText = buku.judul;
            document.getElementById("modalPenulis").innerHTML = `<i class="bi bi-person me-1"></i>${buku.penulis}`;
            document.getElementById("modalKode").innerText = buku.kode_buku;
            document.getElementById("modalKategori").innerText = buku.kategori;
            document.getElementById("modalPenerbit").innerText = buku.penerbit;
            document.getElementById("modalTahun").innerText = buku.tahun_terbit;

            let myModal = new bootstrap.Modal(document.getElementById('detailModal'));
            myModal.show();
        }
    </script>
</body>
</html>