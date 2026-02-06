<?php
// Pastikan session hanya dimulai jika belum aktif
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include "../config/koneksi.php";

// Cek Role Murid
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'murid') {
    header("Location: ../login.php");
    exit;
}

$id_siswa = $_SESSION['id'];

// --- AMBIL DATA TUGAS DARI DATABASE ---
// Menggunakan LEFT JOIN untuk mengecek status pengumpulan siswa
// Logikanya: Ambil semua tugas, lalu cek di tabel pengumpulan apakah siswa ini sudah mengumpulkan
$query = mysqli_query($conn, "
    SELECT tugas.*, pengumpulan.id as id_pengumpulan 
    FROM tugas 
    LEFT JOIN pengumpulan ON tugas.id = pengumpulan.id_tugas AND pengumpulan.id_siswa = '$id_siswa'
    ORDER BY tugas.deadline ASC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Tugas Saya - Murid</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
/* --- VARIABLE CSS --- */
:root {
    --primary-color: #2563eb;
    --secondary-bg: #ffffff;
    --main-bg: #f3f4f6;
    --sidebar-bg: #0f172a;
    --text-main: #1f2937;
    --text-muted: #6b7280;
    --border-color: #e5e7eb;
    --card-hover: #eff6ff;
    --success-bg: #dcfce7;
    --success-text: #166534;
}

body.dark-mode {
    --secondary-bg: #1e293b;
    --main-bg: #0f172a;
    --text-main: #f3f4f6;
    --text-muted: #9ca3af;
    --border-color: #334155;
    --card-hover: #1e3a8a;
    --success-bg: #064e3b;
    --success-text: #a7f3d0;
}

* { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', sans-serif; }
body { display: flex; background: var(--main-bg); color: var(--text-main); min-height: 100vh; }

/* SIDEBAR */
.sidebar { width: 280px; background: var(--sidebar-bg); padding: 24px; position: fixed; height: 100%; color: white; z-index: 10; }
.sidebar a { display: flex; align-items: center; padding: 14px 16px; color: #94a3b8; text-decoration: none; border-radius: 8px; margin-bottom: 8px; transition: 0.3s; font-weight: 500; }
.sidebar a i { width: 24px; margin-right: 10px; }
.sidebar a:hover, .sidebar a.active { background: rgba(255, 255, 255, 0.1); color: white; }

/* MAIN CONTENT */
.main { flex: 1; margin-left: 280px; padding: 32px; width: calc(100% - 280px); }

/* HEADER */
.page-header { margin-bottom: 30px; }
.page-header h1 { font-size: 24px; font-weight: 700; margin-bottom: 5px; }
.page-header p { color: var(--text-muted); }

/* GRID TUGAS CARD */
.task-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 24px;
}

.task-card {
    background: var(--secondary-bg);
    border: 1px solid var(--border-color);
    border-radius: 16px;
    padding: 24px;
    transition: all 0.3s ease;
    position: relative;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

.task-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
    border-color: var(--primary-color);
}

/* Badge Mapel */
.badge {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 50px;
    font-size: 11px;
    font-weight: 600;
    margin-bottom: 10px;
}
.badge-matematika { background: #e0e7ff; color: #4338ca; }
.badge-fisika { background: #fae8ff; color: #86198f; }
.badge-kimia { background: #ecfccb; color: #3f6212; }
.badge-bahasa { background: #ffedd5; color: #9a3412; }
.badge-default { background: #f3f4f6; color: #374151; }

/* Status Badge Baru */
.status-badge {
    font-size: 11px;
    font-weight: 700;
    padding: 4px 10px;
    border-radius: 6px;
    float: right;
}
.status-done { background: var(--success-bg); color: var(--success-text); }
.status-pending { background: #fee2e2; color: #991b1b; }

.task-title { font-size: 18px; font-weight: 600; margin-bottom: 10px; line-height: 1.4; clear: both; }
.task-desc { color: var(--text-muted); font-size: 14px; margin-bottom: 20px; line-height: 1.6; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }

/* Footer Card */
.task-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-top: 1px solid var(--border-color);
    padding-top: 15px;
    margin-top: auto;
}

.deadline-info { font-size: 13px; color: var(--text-muted); display: flex; align-items: center; gap: 6px; }
.deadline-info.urgent { color: #ef4444; font-weight: 600; }

.btn-detail {
    background: var(--primary-color);
    color: white;
    text-decoration: none;
    padding: 8px 16px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 500;
    transition: 0.2s;
}
.btn-detail.done { background: #10b981; } /* Hijau jika selesai */
.btn-detail:hover { opacity: 0.9; }

/* EMPTY STATE */
.empty-state { text-align: center; padding: 50px; color: var(--text-muted); grid-column: 1/-1; }
.empty-state i { font-size: 40px; margin-bottom: 15px; opacity: 0.5; }

/* Responsive */
@media (max-width: 768px) {
    .sidebar { display: none; }
    .main { margin-left: 0; padding: 20px; }
}
</style>
</head>
<body>

<div class="sidebar">
    <h2><i class="fa-solid fa-graduation-cap"></i> Berlajar:3</h2>
    <br>
    <a href="index.php"><i class="fa-solid fa-house"></i> Home</a>
    <a href="dashboard_murid.php"><i class="fa-solid fa-list-check"></i> Dashboard Murid</a>
    <a href="materi.php"><i class="fa-solid fa-book-open"></i> Materi</a>
    <a href="tugas.php" class="active"><i class="fa-solid fa-pen-to-square"></i>Dashboard Tugas</a> 
    <a href="settings.php"><i class="fa-solid fa-gear"></i> Settings</a>
</div>

<div class="main">
    <div class="page-header">
        <h1>Daftar Tugas</h1>
        <p>Tugas yang sudah kamu kerjakan akan ditandai dengan label hijau.</p>
    </div>

    <div class="task-grid">
        
        <?php if(mysqli_num_rows($query) > 0): ?>
            <?php while($tugas = mysqli_fetch_assoc($query)): ?>
                <?php
                    // 1. Logika Status Pengerjaan (Dari Query JOIN)
                    // Jika id_pengumpulan tidak NULL, berarti siswa sudah submit
                    $isSubmitted = !empty($tugas['id_pengumpulan']);
                    $statusLabel = $isSubmitted ? "Sudah Dikerjakan" : "Belum Dikerjakan";
                    $statusClass = $isSubmitted ? "status-done" : "status-pending";
                    $btnText     = $isSubmitted ? "Lihat Detail" : "Kerjakan";
                    $btnClass    = $isSubmitted ? "btn-detail done" : "btn-detail";
                    $btnIcon     = $isSubmitted ? "fa-circle-check" : "fa-arrow-right";

                    // 2. Logika Pewarnaan Badge Mapel
                    $mapel = strtolower($tugas['mapel']);
                    $badgeClass = 'badge-default';
                    if(strpos($mapel, 'matematika') !== false) $badgeClass = 'badge-matematika';
                    if(strpos($mapel, 'fisika') !== false) $badgeClass = 'badge-fisika';
                    if(strpos($mapel, 'kimia') !== false) $badgeClass = 'badge-kimia';
                    if(strpos($mapel, 'bahasa') !== false) $badgeClass = 'badge-bahasa';

                    // 3. Hitung Sisa Hari (Deadline)
                    $deadlineDate = new DateTime($tugas['deadline']);
                    $today = new DateTime();
                    $interval = $today->diff($deadlineDate);
                    $daysLeft = $interval->format('%r%a'); 
                    
                    $deadlineText = "";
                    $urgentClass = "";
                    
                    if ($daysLeft < 0) {
                        $deadlineText = "Terlewat " . abs($daysLeft) . " hari";
                        $urgentClass = "urgent";
                    } elseif ($daysLeft == 0) {
                        $deadlineText = "Hari ini!";
                        $urgentClass = "urgent";
                    } else {
                        $deadlineText = $daysLeft . " hari lagi";
                    }
                ?>

                <div class="task-card">
                    <div>
                        <span class="badge <?= $badgeClass ?>"><?= htmlspecialchars($tugas['mapel']) ?></span>
                        
                        <span class="status-badge <?= $statusClass ?>">
                            <?= $statusLabel ?>
                        </span>

                        <h3 class="task-title"><?= htmlspecialchars($tugas['judul']) ?></h3>
                        <p class="task-desc"><?= htmlspecialchars($tugas['deskripsi']) ?></p>
                    </div>

                    <div class="task-footer">
                        <div class="deadline-info <?= $urgentClass ?>">
                            <i class="fa-regular fa-clock"></i> <?= $deadlineText ?>
                            <small>(<?= date('d M', strtotime($tugas['deadline'])) ?>)</small>
                        </div>
                        
                        <a href="detail_tugas.php?id=<?= $tugas['id'] ?>" class="<?= $btnClass ?>">
                            <?= $btnText ?> <i class="fa-solid <?= $btnIcon ?>"></i>
                        </a>
                    </div>
                </div>

            <?php endwhile; ?>
        <?php else: ?>
            
            <div class="empty-state">
                <i class="fa-solid fa-clipboard-check"></i>
                <h3>Tidak ada tugas aktif</h3>
                <p>Hore! Guru belum memberikan tugas baru.</p>
            </div>

        <?php endif; ?>

    </div>
</div>

<script>
    // Logic Dark Mode (Menjaga konsistensi tema saat refresh)
    const savedTheme = localStorage.getItem('theme');
    if (savedTheme === 'dark') {
        document.body.classList.add('dark-mode');
    }
</script>

</body>
</html>