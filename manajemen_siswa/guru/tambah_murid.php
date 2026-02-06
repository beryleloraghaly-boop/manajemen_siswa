<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'guru') {
    header("Location: ../login.php"); exit;
}
require_once __DIR__ . "/../config/koneksi.php";

if (isset($_POST['submit'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $tempat = mysqli_real_escape_string($conn, $_POST['tempat_lahir']);
    $tanggal = $_POST['tanggal_lahir'];
    
    // Upload Foto Sederhana
    $foto = $_FILES['foto']['name'];
    $tmp = $_FILES['foto']['tmp_name'];
    $newFoto = date('dmYHis').$foto; // Rename agar unik
    $path = "../uploads/".$newFoto;

    if(move_uploaded_file($tmp, $path)) {
        // Default password = 12345 (sebaiknya di-hash di sistem nyata)
        $query = "INSERT INTO users (nama, role, tempat_lahir, tanggal_lahir, foto, password) 
                  VALUES ('$nama', 'murid', '$tempat', '$tanggal', '$newFoto', '12345')";
        if(mysqli_query($conn, $query)){
            header("Location: dashboard_murid.php");
        } else {
            echo "Gagal: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<title>Tambah Murid</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
    body { font-family: 'Inter', sans-serif; background: #f3f4f6; display: flex; justify-content: center; align-items: center; min-height: 100vh; padding: 20px; }
    .form-card { background: white; padding: 40px; border-radius: 16px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); width: 100%; max-width: 500px; }
    h2 { margin-bottom: 20px; color: #1e293b; text-align: center; }
    .form-group { margin-bottom: 15px; }
    label { display: block; margin-bottom: 5px; color: #475569; font-size: 14px; font-weight: 500; }
    input { width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 8px; outline: none; }
    input:focus { border-color: #4f46e5; ring: 2px solid #4f46e5; }
    .btn-submit { width: 100%; padding: 12px; background: #4f46e5; color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; margin-top: 10px; }
    .btn-submit:hover { background: #4338ca; }
    .btn-back { display: block; text-align: center; margin-top: 15px; text-decoration: none; color: #64748b; font-size: 14px; }
</style>
</head>
<body>

<div class="form-card">
    <h2>Tambah Data Murid</h2>
    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label>Nama Lengkap</label>
            <input type="text" name="nama" required placeholder="Contoh: Budi Santoso">
        </div>
        <div class="form-group">
            <label>Asal Sekolah
            </label>
            <input type="text" name="tempat_lahir" required placeholder="Contoh: Jakarta">
        </div>
        <div class="form-group">
            <label>Tanggal Lahir</label>
            <input type="date" name="tanggal_lahir" required>
        </div>
        <div class="form-group">
            <label>Foto Profil</label>
            <input type="file" name="foto" required>
        </div>
        <button type="submit" name="submit" class="btn-submit">Simpan Data</button>
        <a href="dashboard_murid.php" class="btn-back">Batal & Kembali</a>
    </form>
</div>

</body>
</html>