<?php
include 'koneksi.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Data Buku PDF</title>
    <style>
        body { font-family: Arial, sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; font-size: 12px; }
        th { background-color: #f2f2f2; }
        h3 { text-align: center; }
    </style>
</head>
<body onload="window.print()">

    <h3>LAPORAN DATA BUKU PERPUSTAKAAN</h3>
    <table>
        <thead>
            <tr>
                <th>NO</th>
                <th>KODE</th>
                <th>JUDUL</th>
                <th>PENULIS</th>
                <th>KATEGORI</th>
                <th>TAHUN</th>
                <th>PENERBIT</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 1;
            $sql = $conn->query("SELECT * FROM books");
            while ($row = $sql->fetch_assoc()) {
                echo "<tr>
                    <td>{$no}</td>
                    <td>{$row['kode_buku']}</td>
                    <td>{$row['judul']}</td>
                    <td>{$row['penulis']}</td>
                    <td>{$row['kategori']}</td>
                    <td>{$row['tahun_terbit']}</td>
                    <td>{$row['penerbit']}</td>
                </tr>";
                $no++;
            }
            ?>
        </tbody>
    </table>

</body>
</html>