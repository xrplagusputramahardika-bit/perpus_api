<?php
header("Content-Type: application/json; charset=UTF-8");
include 'koneksi.php';

$result = $conn->query("SELECT * FROM books ORDER BY id DESC");
$data = [];

while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data, JSON_PRETTY_PRINT);
?>