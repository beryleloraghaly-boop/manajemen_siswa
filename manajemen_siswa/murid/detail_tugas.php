<?php
session_start();
include '../config/koneksi.php';

// Cek Sesi Murid
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'murid') {
    header("Location: ../login.php");
    exit;
}

$id_tugas = $_GET['id'];
$id_siswa = $_SESSION['id'];

// --- 1. AMBIL DETAIL TUGAS ---
$query_tugas = mysqli_query($conn, "SELECT * FROM tugas WHERE id = '$id_tugas'");
$tugas = mysqli_fetch_assoc($query_tugas);

// --- 2. CEK APAKAH SUDAH MENGUMPULKAN? ---
$query_cek = mysqli_query($conn, "SELECT * FROM pengumpulan WHERE id_tugas = '$id_tugas' AND id_siswa = '$id_siswa'");
$sudah_kumpul = mysqli_fetch_assoc($query_cek);

// --- 3. PROSES UPLOAD TUGAS ---
$pesan = "";
if (isset($_POST['kirim_tugas'])) {
    $nama_file = $_FILES['file_tugas']['name'];
    $tmp_name = $_FILES['file_tugas']['tmp_name'];
    $error = $_FILES['file_tugas']['error'];

    // Rename file agar unik (time_namafile)
    $nama_baru = time() . '_' . $nama_file;
    $upload_dir = "../uploads/"; // Pastikan folder ini ada

    if ($error === 0) {
        if(move_uploaded_file($tmp_name, $upload_dir . $nama_baru)) {
            $insert = mysqli_query($conn, "INSERT INTO pengumpulan (id_tugas, id_siswa, file_tugas) VALUES ('$id_tugas', '$id_siswa', '$nama_baru')");
            if($insert) {
                header("Location: detail_tugas.php?id=$id_tugas&status=sukses");
                exit;
            }
        } else {
            $pesan = "<div class='alert error'>Gagal mengupload file.</div>";
        }
    } else {
        $pesan = "<div class='alert error'>Terjadi kesalahan pada file.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Detail Tugas - <?= htmlspecialchars($tugas['judul']) ?></title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
/* CSS COPY DARI SEBELUMNYA (Disingkat agar muat, gunakan style tugas.php sebelumnya) */
:root { --primary-color: #4f46e5; --secondary-bg: #ffffff; --main-bg: #f3f4f6; --text-main: #1f2937; --text-muted: #6b7280; --border-color: #e5e7eb; }
body { background: var(--main-bg); color: var(--text-main); font-family: 'Inter', sans-serif; display: flex; justify-content: center; padding: 40px; }
.container { max-width: 800px; width: 100%; }
.card { background: var(--secondary-bg); padding: 30px; border-radius: 16px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); margin-bottom: 20px; }
h1 { font-size: 24px; font-weight: 700; margin-bottom: 10px; }
.badge { background: #e0e7ff; color: #4338ca; padding: 4px 10px; border-radius: 6px; font-size: 12px; font-weight: 600; }
.desc { margin-top: 20px; line-height: 1.6; color: #4b5563; }
.form-group { margin-bottom: 20px; }
label { display: block; margin-bottom: 8px; font-weight: 600; font-size: 14px; }
input[type="text"], input[type="file"] { width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px; background: #f8fafc; }
.btn-submit { background: var(--primary-color); color: white; border: none; padding: 12px 20px; border-radius: 8px; cursor: pointer; font-weight: 600; width: 100%; transition: 0.3s; }
.btn-submit:hover { background: #4338ca; }
.btn-back { display: inline-flex; align-items: center; gap: 5px; text-decoration: none; color: #6b7280; margin-bottom: 20px; font-weight: 500; }
.status-done { background: #dcfce7; color: #166534; padding: 15px; border-radius: 12px; text-align: center; font-weight: 600; border: 1px solid #bbf7d0; }
.alert { padding: 15px; border-radius: 8px; margin-bottom: 20px; background: #fee2e2; color: #991b1b; }
</style>
</head>
<body>

<div class="container">
    <a href="tugas.php" class="btn-back"><i class="fa-solid fa-arrow-left"></i> Kembali ke List Tugas</a>

    <div class="card">
        <span class="badge"><?= htmlspecialchars($tugas['mapel']) ?></span>
        <span style="float: right; color: #6b7280; font-size: 13px;">Deadline: <?= date('d M Y', strtotime($tugas['deadline'])) ?></span>
        <h1 style="margin-top: 10px;"><?= htmlspecialchars($tugas['judul']) ?></h1>
        
        <p class="desc"><?= nl2br(htmlspecialchars($tugas['deskripsi'])) ?></p>

        <?php if(!empty($tugas['link_tugas'])): ?>
            <div style="margin-top: 20px; padding: 15px; background: #f0f9ff; border-radius: 8px; border: 1px solid #bae6fd;">
                <i class="fa-solid fa-link" style="color: #0284c7;"></i> 
                Link Tugas: <a href="<?= $tugas['link_tugas'] ?>" target="_blank"><?= $tugas['link_tugas'] ?></a>
            </div>
        <?php endif; ?>
    </div>

    <div class="card">
        <h3><i class="fa-solid fa-paper-plane"></i> Pengumpulan Tugas</h3>
        <hr style="border: 0; border-top: 1px solid #e5e7eb; margin: 15px 0;">

        <?php if ($sudah_kumpul): ?>
            <div class="status-done">
                <i class="fa-solid fa-circle-check"></i> Kamu sudah mengerjakan tugas ini.
                <br>
                <small>Dikirim pada: <?= date('d M Y H:i', strtotime($sudah_kumpul['tanggal_kumpul'])) ?></small>
                <div style="margin-top: 10px;">
                    <a href="../uploads/<?= $sudah_kumpul['file_tugas'] ?>" target="_blank" style="color: #166534; text-decoration: underline;">Lihat File Kamu</a>
                </div>
            </div>
        <?php else: ?>
            <?= $pesan ?>
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Atas Nama (Siswa)</label>
                    <input type="text" value="<?= $_SESSION['nama'] ?>" readonly style="background: #e2e8f0; cursor: not-allowed;">
                    <small style="color: #64748b;">Nama diambil otomatis dari akun Anda.</small>
                </div>

                <div class="form-group">
                    <label>Upload File Tugas (PDF/DOC/JPG)</label>
                    <input type="file" name="file_tugas" required>
                </div>

                <button type="submit" name="kirim_tugas" class="btn-submit">Kirim Jawaban</button>
            </form>
        <?php endif; ?>
    </div>
</div>

</body>
</html>