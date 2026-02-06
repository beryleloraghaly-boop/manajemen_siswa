<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'guru') {
    header("Location: ../login.php"); exit;
}
require_once __DIR__ . "/../config/koneksi.php";

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    $q = mysqli_query($conn, "SELECT foto FROM users WHERE id='$id'");
    $d = mysqli_fetch_assoc($q);
    
    if ($d['foto'] != "" && file_exists("../uploads/" . $d['foto'])) {
        unlink("../uploads/" . $d['foto']);
    }

    mysqli_query($conn, "DELETE FROM users WHERE id='$id'");
}

header("Location: dashboard_guru.php");
?>