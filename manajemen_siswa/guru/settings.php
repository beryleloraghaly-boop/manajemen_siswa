<?php
session_start();
// Pastikan file koneksi database sudah benar path-nya (Opsional untuk page ini, tapi baik untuk standar)
include '../config/koneksi.php'; 

// Cek Role Guru
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'guru') {
    // header("Location: ../login.php");
    // exit;
    // Mock session (Hapus saat production)
    $_SESSION['nama'] = "Budi Santoso"; 
    $_SESSION['role'] = "guru";
}

// Menandai halaman aktif di sidebar
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Settings - Berlajar:3</title>

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
/* --- 1. GLOBAL VARIABLES (SAMA SEPERTI PAGE LAIN) --- */
:root {
    --primary-color: #4f46e5;
    --primary-hover: #4338ca;
    --secondary-bg: #ffffff;
    --main-bg: #f3f4f6;
    --sidebar-bg: #0f172a;
    --text-main: #1f2937;
    --text-muted: #6b7280;
    --border-color: #e5e7eb;
    --danger: #ef4444;
    --card-icon-bg: #e0e7ff;
    --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    --transition: all 0.3s ease;
}

body.dark-mode {
    --secondary-bg: #1e293b;
    --main-bg: #0f172a;
    --text-main: #f3f4f6;
    --text-muted: #9ca3af;
    --border-color: #334155;
    --card-icon-bg: #312e81;
    --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.3);
}

* { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', sans-serif; }
body { display: flex; background: var(--main-bg); min-height: 100vh; color: var(--text-main); transition: background 0.3s ease, color 0.3s ease; }

/* --- 2. SIDEBAR --- */
.sidebar { width: 280px; background: var(--sidebar-bg); min-height: 100vh; color: white; padding: 24px; display: flex; flex-direction: column; position: fixed; left: 0; top: 0; bottom: 0; z-index: 50; }
.sidebar h2 { text-align: left; margin-bottom: 30px; font-size: 20px; font-weight: 700; display: flex; align-items: center; gap: 10px; color: #fff; padding-left: 10px; }
.sidebar h2 i { color: var(--primary-color); }

.sidebar input { width: 100%; padding: 12px 16px; border-radius: 8px; border: 1px solid #334155; background: #1e293b; color: #e2e8f0; margin-bottom: 25px; font-size: 14px; transition: var(--transition); outline: none; }
.sidebar input:focus { border-color: var(--primary-color); background: #1e293b; box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.2); }

.sidebar-menu a { display: flex; align-items: center; padding: 12px 16px; color: #94a3b8; text-decoration: none; border-radius: 8px; margin-bottom: 8px; transition: var(--transition); font-weight: 500; font-size: 15px; }
.sidebar-menu a i { width: 24px; margin-right: 10px; text-align: center; font-size: 16px; }
.sidebar-menu a:hover { background: rgba(255, 255, 255, 0.05); color: white; transform: translateX(4px); }
.sidebar-menu a.active { background: var(--primary-color); color: white; box-shadow: 0 4px 12px rgba(79, 70, 229, 0.2); }

/* Action Button Sidebar */
.action-btn { display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #4f46e5, #4338ca); color: white !important; padding: 12px; border-radius: 12px; margin-bottom: 25px; text-decoration: none; font-weight: 600; box-shadow: 0 4px 10px rgba(79, 70, 229, 0.3); transition: 0.3s; }
.action-btn:hover { transform: translateY(-2px); box-shadow: 0 8px 15px rgba(79, 70, 229, 0.4); }
.action-btn i { margin-right: 8px; }

.footer { margin-top: auto; padding-top: 20px; }
.footer a { display: flex; align-items: center; justify-content: center; background: rgba(239, 68, 68, 0.1); color: var(--danger); padding: 12px; border-radius: 8px; text-decoration: none; font-weight: 600; border: 1px solid rgba(239, 68, 68, 0.2); transition: 0.3s; }
.footer a:hover { background: var(--danger); color: white; }

/* --- 3. MAIN CONTENT --- */
.main { flex: 1; margin-left: 280px; padding: 32px; width: calc(100% - 280px); display: flex; flex-direction: column; }

/* TOPBAR */
.topbar { display: flex; justify-content: space-between; align-items: center; background: var(--secondary-bg); padding: 20px 24px; border-radius: 16px; margin-bottom: 32px; box-shadow: var(--shadow-sm); border: 1px solid var(--border-color); }
.topbar .welcome h1 { font-size: 24px; font-weight: 700; color: var(--text-main); margin-bottom: 4px; }
.topbar .welcome p { color: var(--text-muted); font-size: 14px; }
.topbar-actions { display: flex; align-items: center; gap: 15px; }
.theme-toggle-btn { background: transparent; border: 1px solid var(--border-color); color: var(--text-muted); width: 40px; height: 40px; border-radius: 8px; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 18px; transition: var(--transition); }
.theme-toggle-btn:hover { background: var(--card-icon-bg); color: var(--primary-color); border-color: var(--primary-color); }

/* --- 4. EMPTY STATE STYLE (KHUSUS PAGE INI) --- */
.empty-state-container {
    flex: 1; /* Mengisi sisa ruang ke bawah */
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    background: var(--secondary-bg);
    border-radius: 16px;
    border: 1px solid var(--border-color);
    padding: 60px 20px;
    box-shadow: var(--shadow-sm);
    text-align: center;
    min-height: 400px; /* Minimal tinggi agar terlihat bagus */
}

.empty-icon-wrapper {
    width: 120px;
    height: 120px;
    background: var(--card-icon-bg);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 24px;
    animation: float 3s ease-in-out infinite;
}

.empty-icon-wrapper i {
    font-size: 50px;
    color: var(--primary-color);
}

.empty-title {
    font-size: 24px;
    font-weight: 700;
    color: var(--text-main);
    margin-bottom: 10px;
}

.empty-subtitle {
    color: var(--text-muted);
    font-size: 16px;
    max-width: 400px;
    line-height: 1.5;
}

/* Animasi melayang kecil */
@keyframes float {
    0% { transform: translateY(0px); }
    50% { transform: translateY(-10px); }
    100% { transform: translateY(0px); }
}

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

    <input type="text" id="searchMenu" placeholder="Cari menu...">

    <a href="tugas.php" class="action-btn">
        <i class="fa-solid fa-plus"></i> Kelola Tugas
    </a>

    <div class="sidebar-menu">
        <a href="index.php"><i class="fa-solid fa-house"></i> <span>Home</span></a>
        <a href="dashboard_guru.php"><i class="fa-solid fa-chalkboard-user"></i> <span>Dashboard Guru</span></a>
        <a href="dashboard_murid.php"><i class="fa-solid fa-user-graduate"></i> <span>Dashboard Murid</span></a>
        <a href="tugas.php"><i class="fa-solid fa-list-check"></i> <span>Tugas</span></a>
        <a href="settings.php" class="active"><i class="fa-solid fa-gear"></i> <span>Settings</span></a>
    </div>

    <div class="footer">
        <a href="../logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
    </div>
</div>

<div class="main">
    
    <div class="topbar">
        <div class="welcome">
            <h1>Settings Akun</h1>
            <p>Pengaturan profil dan preferensi aplikasi.</p>
        </div>
        <div class="topbar-actions">
            <button class="theme-toggle-btn" id="themeToggle" title="Ganti Tema">
                <i class="fa-solid fa-moon"></i>
            </button>
            <div style="font-weight: 600; color: var(--text-main);">
                <?= htmlspecialchars($_SESSION['nama']); ?>
            </div>
        </div>
    </div>

    <div class="empty-state-container">
        <div class="empty-icon-wrapper">
            <i class="fa-solid fa-person-digging"></i>
        </div>
        <h2 class="empty-title">Belum ada apa-apa</h2>
        <p class="empty-subtitle">
            Halaman pengaturan sedang dalam tahap pengembangan. Segera hadir fitur untuk mengubah profil dan keamanan.
        </p>
    </div>

</div>

<script>
// --- SCRIPT DARK MODE ---
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

// --- SEARCH MENU ---
const searchInput = document.getElementById('searchMenu');
const menuLinks = document.querySelectorAll('.sidebar-menu a');

searchInput.addEventListener('keyup', function () {
    const filter = searchInput.value.toLowerCase();
    menuLinks.forEach(link => {
        const text = link.textContent.toLowerCase(); 
        link.style.display = text.includes(filter) ? 'flex' : 'none'; 
    });
});
</script>

</body>
</html>