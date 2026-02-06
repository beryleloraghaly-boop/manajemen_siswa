<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'guru') {
    header("Location: ../login.php"); exit;
}
require_once __DIR__ . "/../config/koneksi.php";

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Ambil info foto dulu untuk dihapus filenya
    $q = mysqli_query($conn, "SELECT foto FROM users WHERE id='$id'");
    $d = mysqli_fetch_assoc($q);
    
    // Hapus file fisik jika ada
    if ($d['foto'] != "" && file_exists("../uploads/" . $d['foto'])) {
        unlink("../uploads/" . $d['foto']);
    }

    // Hapus data dari database
    mysqli_query($conn, "DELETE FROM users WHERE id='$id'");
}

header("Location: dashboard_murid.php");
?>