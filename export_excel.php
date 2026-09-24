<?php
include 'koneksi.php';

// Header untuk file Excel (.xls)
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=data_buku.xls");
?>
<center>
    <h3>LAPORAN DATA BUKU PERPUSTAKAAN</h3>
</center>
<table border="1">
    <thead>
        <tr>
            <th>NO / ID</th>
            <th>KODE BUKU</th>
            <th>JUDUL</th>
            <th>PENULIS</th>
            <th>KATEGORI</th>
            <th>TAHUN TERBIT</th>
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