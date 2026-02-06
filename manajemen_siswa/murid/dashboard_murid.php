<?php
session_start();
include "../config/koneksi.php";

// Cek Role Murid
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'murid') {
    header("Location: ../login.php");
    exit;
}

$id_saya = $_SESSION['id']; // ID Murid yang sedang login

// --- LOGIKA PENCARIAN ---
$keyword = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';

$query_str = "SELECT * FROM users WHERE role = 'murid'";

if ($keyword) {
    $query_str .= " AND nama LIKE '%$keyword%'";
}

$query_str .= " ORDER BY nama ASC"; // Urutkan sesuai Abjad
$data_murid = mysqli_query($conn, $query_str);
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard Murid - Teman Sekelas</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
/* --- VARIABLE CSS (TEMA MURID: BIRU) --- */
:root {
    --primary-color: #2563eb;       /* Blue */
    --primary-hover: #1d4ed8;
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
body { display: flex; background: var(--main-bg); color: var(--text-main); min-height: 100vh; transition: background 0.3s ease; }

/* SIDEBAR */
.sidebar { width: 280px; background: var(--sidebar-bg); padding: 24px; position: fixed; height: 100%; color: white; z-index: 10; }
.sidebar h2 { font-size: 20px; font-weight: 700; margin-bottom: 30px; display: flex; align-items: center; gap: 10px; justify-content: center; }
.sidebar h2 i { color: var(--primary-color); }

.sidebar input { width: 100%; padding: 12px 16px; border-radius: 8px; border: 1px solid #334155; background: #1e293b; color: #e2e8f0; margin-bottom: 25px; font-size: 14px; outline: none; transition: 0.3s; }
.sidebar input:focus { border-color: var(--primary-color); }

.sidebar a { display: flex; align-items: center; padding: 14px 16px; color: #94a3b8; text-decoration: none; border-radius: 8px; margin-bottom: 8px; transition: 0.3s; font-weight: 500; font-size: 15px; }
.sidebar a i { width: 24px; margin-right: 10px; text-align: center; font-size: 16px; }
.sidebar a:hover { background: rgba(255, 255, 255, 0.05); color: white; transform: translateX(4px); }
.sidebar a.active { background: var(--primary-color); color: white; box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3); }

/* MAIN CONTENT */
.main { flex: 1; margin-left: 280px; padding: 32px; width: calc(100% - 280px); }

/* HEADER & TOPBAR */
.topbar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
.page-title h1 { font-size: 24px; font-weight: 700; margin-bottom: 5px; }
.page-title p { color: var(--text-muted); font-size: 14px; }

.topbar-actions { display: flex; gap: 15px; align-items: center; }
.theme-toggle-btn { background: var(--secondary-bg); border: 1px solid var(--border-color); color: var(--text-muted); width: 40px; height: 40px; border-radius: 8px; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 18px; transition: 0.3s; }
.theme-toggle-btn:hover { border-color: var(--primary-color); color: var(--primary-color); }

/* CARD & TABLE */
.card { background: var(--secondary-bg); border-radius: 16px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); border: 1px solid var(--border-color); overflow: hidden; }
.card-header { padding: 20px 24px; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center; }
.card-header h3 { font-size: 16px; font-weight: 600; }

.search-box { display: flex; gap: 10px; }
.search-box input { padding: 8px 12px; border-radius: 8px; border: 1px solid var(--border-color); font-size: 14px; outline: none; background: var(--main-bg); color: var(--text-main); }
.search-box button { padding: 8px 16px; background: var(--primary-color); color: white; border: none; border-radius: 8px; cursor: pointer; }

.table-responsive { overflow-x: auto; }
table { width: 100%; border-collapse: collapse; }
th { background: #f8fafc; color: #64748b; font-weight: 600; text-transform: uppercase; font-size: 11px; letter-spacing: 0.5px; padding: 16px 24px; text-align: left; border-bottom: 1px solid var(--border-color); }
body.dark-mode th { background: #1e293b; color: #94a3b8; }
td { padding: 16px 24px; border-bottom: 1px solid var(--border-color); font-size: 14px; vertical-align: middle; }

/* Highlight Row untuk diri sendiri */
tr.me { background: rgba(37, 99, 235, 0.05); } 
tr.me td { font-weight: 600; color: var(--primary-color); }
tr:hover td { background: rgba(0,0,0,0.02); }

.foto-avatar { width: 40px; height: 40px; border-radius: 50%; object-fit: cover; border: 2px solid var(--border-color); }
.status-badge { background: var(--success-bg); color: var(--success-text); padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; }

/* Responsive */
@media (max-width: 768px) {
    .sidebar { display: none; }
    .main { margin-left: 0; padding: 20px; }
    .topbar { flex-direction: column; gap: 15px; align-items: flex-start; }
    .card-header { flex-direction: column; gap: 15px; align-items: flex-start; }
}
</style>
</head>
<body>

<div class="sidebar">
    <h2><i class="fa-solid fa-graduation-cap"></i> Berlajar:3</h2>
    <br>
    <a href="index.php"><i class="fa-solid fa-house"></i> Home</a>
    
    <a href="dashboard_murid.php" class="active"><i class="fa-solid fa-list-check"></i> Dashboard Murid</a>
    
    <a href="materi.php"><i class="fa-solid fa-book-open"></i> Materi</a>
    <a href="tugas.php"><i class="fa-solid fa-pen-to-square"></i> Dashboard Tugas</a> 
    <a href="settings.php"><i class="fa-solid fa-gear"></i> Settings</a>
</div>

<div class="main">
    <div class="topbar">
        <div class="page-title">
            <h1>Teman Sekelas</h1>
            <p>Daftar siswa yang terdaftar dalam sistem.</p>
        </div>
        <div class="topbar-actions">
            <button class="theme-toggle-btn" id="themeToggle"><i class="fa-solid fa-moon"></i></button>
            <a href="../logout.php" style="color: var(--danger); text-decoration: none; font-weight: 600; font-size: 14px;">Logout</a>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3>Data Siswa</h3>
            <form method="GET" class="search-box">
                <input type="text" name="search" placeholder="Cari nama teman..." value="<?= htmlspecialchars($keyword) ?>">
                <button type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
            </form>
        </div>

        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th width="50">#</th>
                        <th width="80">Foto</th>
                        <th>Nama Lengkap</th>
                        <th>Email</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no = 1;
                    if(mysqli_num_rows($data_murid) > 0): 
                        while($m = mysqli_fetch_assoc($data_murid)): 
                            // Cek apakah ini baris untuk user yang sedang login
                            $isMe = ($m['id'] == $id_saya) ? 'me' : '';
                            $displayName = ($m['id'] == $id_saya) ? $m['nama'] . " (Saya)" : $m['nama'];
                    ?>
                    <tr class="<?= $isMe ?>">
                        <td><?= $no++ ?></td>
                        <td>
                            <img src="../uploads/<?= !empty($m['foto']) ? htmlspecialchars($m['foto']) : 'default.png' ?>" class="foto-avatar" alt="Foto">
                        </td>
                        <td><?= htmlspecialchars($displayName) ?></td>
                        <td><?= htmlspecialchars($m['email']) ?></td>
                        <td><span class="status-badge">Aktif</span></td>
                    </tr>
                    <?php endwhile; ?>
                    <?php else: ?>
                    <tr>
                        <td colspan="5" style="text-align:center; padding: 30px; color: var(--text-muted);">Tidak ada data siswa ditemukan.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    // Logic Dark Mode
    const themeToggle = document.getElementById('themeToggle');
    const themeIcon = themeToggle.querySelector('i');
    const body = document.body;

    function updateIcon(isDark) {
        if (isDark) {
            themeIcon.classList.remove('fa-moon');
            themeIcon.classList.add('fa-sun');
        } else {
            themeIcon.classList.remove('fa-sun');
            themeIcon.classList.add('fa-moon');
        }
    }

    const savedTheme = localStorage.getItem('theme');
    if (savedTheme === 'dark') {
        body.classList.add('dark-mode');
        updateIcon(true);
    }

    themeToggle.addEventListener('click', () => {
        body.classList.toggle('dark-mode');
        const isDark = body.classList.contains('dark-mode');
        localStorage.setItem('theme', isDark ? 'dark' : 'light');
        updateIcon(isDark);
    });
</script>

</body>
</html>