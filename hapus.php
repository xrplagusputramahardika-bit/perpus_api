<?php
session_start();
if (!isset($_SESSION['admin'])) { header("Location: login.php"); exit; }
include 'koneksi.php';

$id = $_GET['id'];
$conn->query("DELETE FROM books WHERE id = $id");

header("Location: dashboard.php");
exit;
?>