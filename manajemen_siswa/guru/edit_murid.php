<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'guru') {
    header("Location: ../login.php"); exit;
}
require_once __DIR__ . "/../config/koneksi.php";

$id = $_GET['id'];
$query = mysqli_query($conn, "SELECT * FROM users WHERE id = '$id'");
$data = mysqli_fetch_assoc($query);

if (isset($_POST['update'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $tempat = mysqli_real_escape_string($conn, $_POST['tempat_lahir']);
    $tanggal = $_POST['tanggal_lahir'];

    // Cek jika ada foto baru
    if ($_FILES['foto']['name'] != "") {
        $foto = $_FILES['foto']['name'];
        $tmp = $_FILES['foto']['tmp_name'];
        $newFoto = date('dmYHis').$foto;
        move_uploaded_file($tmp, "../uploads/".$newFoto);
        
        // Update dengan foto baru
        $q = "UPDATE users SET nama='$nama', tempat_lahir='$tempat', tanggal_lahir='$tanggal', foto='$newFoto' WHERE id='$id'";
    } else {
        // Update tanpa mengubah foto
        $q = "UPDATE users SET nama='$nama', tempat_lahir='$tempat', tanggal_lahir='$tanggal' WHERE id='$id'";
    }

    if(mysqli_query($conn, $q)){
        header("Location: dashboard_murid.php");
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<title>Edit Murid</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
    /* Paste CSS dari tambah_murid.php disini agar sama persis */
    body { font-family: 'Inter', sans-serif; background: #f3f4f6; display: flex; justify-content: center; align-items: center; min-height: 100vh; padding: 20px; }
    .form-card { background: white; padding: 40px; border-radius: 16px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); width: 100%; max-width: 500px; }
    h2 { margin-bottom: 20px; color: #1e293b; text-align: center; }
    .form-group { margin-bottom: 15px; }
    label { display: block; margin-bottom: 5px; color: #475569; font-size: 14px; font-weight: 500; }
    input { width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 8px; outline: none; }
    .btn-submit { width: 100%; padding: 12px; background: #0284c7; color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; margin-top: 10px; }
    .btn-back { display: block; text-align: center; margin-top: 15px; text-decoration: none; color: #64748b; font-size: 14px; }
    .old-photo { display: block; width: 60px; height: 60px; border-radius: 50%; margin: 10px 0; object-fit: cover; }
</style>
</head>
<body>

<div class="form-card">
    <h2>Edit Data Murid</h2>
    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label>Nama Lengkap</label>
            <input type="text" name="nama" value="<?= $data['nama']; ?>" required>
        </div>
        <div class="form-group">
            <label>Asal Sekolah</label>
            <input type="text" name="tempat_lahir" value="<?= $data['tempat_lahir']; ?>" required>
        </div>
        <div class="form-group">
            <label>Tanggal Lahir</label>
            <input type="date" name="tanggal_lahir" value="<?= $data['tanggal_lahir']; ?>" required>
        </div>
        <div class="form-group">
            <label>Foto Profil (Biarkan kosong jika tidak diganti)</label>
            <img src="../uploads/<?= $data['foto']; ?>" class="old-photo">
            <input type="file" name="foto">
        </div>
        <button type="submit" name="update" class="btn-submit">Update Data</button>
        <a href="dashboard_murid.php" class="btn-back">Batal</a>
    </form>
</div>

</body>
</html>