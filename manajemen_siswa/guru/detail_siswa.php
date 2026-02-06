<?php
session_start();
include '../config/koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'guru') {
    header("Location: ../login.php"); exit;
}

$id_siswa = $_GET['id'];

// Ambil Data Siswa
$q_siswa = mysqli_query($conn, "SELECT * FROM users WHERE id = '$id_siswa'");
$siswa = mysqli_fetch_assoc($q_siswa);

// Ambil Tugas yang SUDAH dikerjakan siswa ini
$q_tugas = mysqli_query($conn, "
    SELECT pengumpulan.*, tugas.judul, tugas.mapel 
    FROM pengumpulan 
    JOIN tugas ON pengumpulan.id_tugas = tugas.id 
    WHERE pengumpulan.id_siswa = '$id_siswa'
    ORDER BY pengumpulan.tanggal_kumpul DESC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Detail Siswa - <?= $siswa['nama'] ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* ... Tempel CSS :root dan body disini ... */
        body { padding: 40px; font-family: 'Inter', sans-serif; background: #f3f4f6; }
        .container { max-width: 900px; margin: 0 auto; }
        .card { background: white; padding: 30px; border-radius: 16px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 15px; border-bottom: 1px solid #e5e7eb; text-align: left; }
        .btn-file { background: #4f46e5; color: white; padding: 5px 10px; border-radius: 5px; text-decoration: none; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <a href="dashboard_murid.php" style="text-decoration: none; color: #4b5563; margin-bottom: 20px; display: block;">
            <i class="fa-solid fa-arrow-left"></i> Kembali ke Dashboard Murid
        </a>

        <div class="card">
            <h1><i class="fa-solid fa-user-graduate"></i> <?= htmlspecialchars($siswa['nama']) ?></h1>
            <p style="color: #6b7280;">Email: <?= htmlspecialchars($siswa['email']) ?></p>
            
            <h3 style="margin-top: 30px; border-bottom: 2px solid #f3f4f6; padding-bottom: 10px;">
                Riwayat Tugas Selesai
            </h3>

            <?php if(mysqli_num_rows($q_tugas) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Judul Tugas</th>
                        <th>Mapel</th>
                        <th>Tanggal Kirim</th>
                        <th>File</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = mysqli_fetch_assoc($q_tugas)): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['judul']) ?></td>
                        <td><?= htmlspecialchars($row['mapel']) ?></td>
                        <td><?= date('d M Y H:i', strtotime($row['tanggal_kumpul'])) ?></td>
                        <td>
                            <a href="../uploads/<?= $row['file_tugas'] ?>" target="_blank" class="btn-file">
                                <i class="fa-solid fa-download"></i> Download
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <?php else: ?>
                <p style="text-align: center; padding: 20px; color: #9ca3af;">Belum ada tugas yang dikumpulkan.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>