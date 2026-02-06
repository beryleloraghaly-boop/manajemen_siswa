<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'guru') {
    // header("Location: ../login.php"); // Uncomment jika struktur folder sudah benar
    // exit;
    
    // Mock session untuk testing tampilan (Hapus ini saat production)
    $_SESSION['nama'] = "Budi Santoso";
    $_SESSION['role'] = "guru";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Home Guru - Dashboard</title>

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
/* --- 1. GLOBAL RESET & VARIABLES --- */
:root {
    /* -- LIGHT MODE VARIABLES (Default) -- */
    --primary-color: #4f46e5;
    --primary-hover: #4338ca;
    --secondary-bg: #ffffff;      /* Warna Kartu/Topbar */
    --main-bg: #f3f4f6;           /* Warna Latar Belakang Utama */
    --sidebar-bg: #0f172a;        /* Sidebar tetap gelap */
    --text-main: #1f2937;         /* Teks Utama */
    --text-muted: #6b7280;        /* Teks Pudar */
    --border-color: #e5e7eb;      /* Garis Batas */
    --danger: #ef4444;
    --card-icon-bg: #e0e7ff;      /* Background icon di card */
    --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    --transition: all 0.3s ease;
}

/* -- DARK MODE VARIABLES -- */
body.dark-mode {
    --secondary-bg: #1e293b;      /* Slate 800 */
    --main-bg: #0f172a;           /* Slate 900 */
    --text-main: #f3f4f6;         /* Gray 100 */
    --text-muted: #9ca3af;        /* Gray 400 */
    --border-color: #334155;      /* Slate 700 */
    --card-icon-bg: #312e81;      /* Indigo 900 (lebih gelap) */
    --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.3);
    --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.5);
}

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Inter', sans-serif;
}

body {
    display: flex;
    background: var(--main-bg);
    min-height: 100vh;
    color: var(--text-main);
    transition: background 0.3s ease, color 0.3s ease; /* Smooth transition */
}

/* --- 2. SIDEBAR STYLING --- */
.sidebar {
    width: 280px;
    background: var(--sidebar-bg);
    min-height: 100vh;
    color: white;
    padding: 24px;
    display: flex;
    flex-direction: column;
    position: fixed;
    left: 0;
    top: 0;
    bottom: 0;
    z-index: 50;
}

.sidebar h2 {
    text-align: left;
    margin-bottom: 30px;
    font-size: 20px;
    font-weight: 700;
    letter-spacing: 0.5px;
    display: flex;
    align-items: center;
    gap: 10px;
    color: #fff;
    padding-left: 10px;
}

.sidebar h2 i {
    color: var(--primary-color);
}

/* SEARCH INPUT */
.sidebar input {
    width: 100%;
    padding: 12px 16px;
    border-radius: 8px;
    border: 1px solid #334155;
    background: #1e293b;
    color: #e2e8f0;
    margin-bottom: 25px;
    font-size: 14px;
    transition: var(--transition);
    outline: none;
}

.sidebar input:focus {
    border-color: var(--primary-color);
    background: #1e293b;
    box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.2);
}

.sidebar input::placeholder {
    color: #64748b;
}

/* NAVIGATION LINKS */
.sidebar a {
    display: flex;
    align-items: center;
    padding: 14px 16px;
    color: #94a3b8;
    text-decoration: none;
    border-radius: 8px;
    margin-bottom: 8px;
    transition: var(--transition);
    font-weight: 500;
    font-size: 15px;
}

.sidebar a i {
    width: 24px;
    margin-right: 10px;
    text-align: center;
    font-size: 16px;
}

.sidebar a:hover {
    background: rgba(255, 255, 255, 0.05);
    color: white;
    transform: translateX(4px);
}

/* --- 3. MAIN CONTENT --- */
.main {
    flex: 1;
    margin-left: 280px;
    padding: 32px;
    width: calc(100% - 280px);
}

/* --- 4. TOP BAR --- */
.topbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: var(--secondary-bg);
    padding: 20px 24px;
    border-radius: 16px;
    margin-bottom: 32px;
    box-shadow: var(--shadow-sm);
    border: 1px solid var(--border-color);
    transition: background 0.3s ease, border-color 0.3s ease;
}

.topbar .welcome h1 {
    font-size: 24px;
    font-weight: 700;
    color: var(--text-main);
    margin-bottom: 4px;
}

.topbar .welcome p {
    color: var(--text-muted);
    font-size: 14px;
}

.topbar .welcome span {
    color: var(--primary-color);
    font-weight: 600;
    background: var(--card-icon-bg); /* Use variable */
    padding: 2px 8px;
    border-radius: 4px;
    font-size: 12px;
}

/* WRAPPER UNTUK ACTIONS DI TOPBAR (Theme Toggle + Logout) */
.topbar-actions {
    display: flex;
    align-items: center;
    gap: 15px;
}

/* THEME TOGGLE BUTTON STYLE */
.theme-toggle-btn {
    background: transparent;
    border: 1px solid var(--border-color);
    color: var(--text-muted);
    width: 40px;
    height: 40px;
    border-radius: 8px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    transition: var(--transition);
}

.theme-toggle-btn:hover {
    background: var(--card-icon-bg);
    color: var(--primary-color);
    border-color: var(--primary-color);
}

.logout-top a {
    background: var(--secondary-bg); /* Mengikuti tema */
    color: var(--danger);
    padding: 10px 20px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 600;
    transition: var(--transition);
    border: 1px solid #fee2e2;
    font-size: 14px;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

/* Dark mode specific fix for logout button border */
body.dark-mode .logout-top a {
    border-color: #7f1d1d;
    background: rgba(239, 68, 68, 0.1);
}

.logout-top a:hover {
    background: var(--danger);
    color: white;
    border-color: var(--danger);
    box-shadow: 0 4px 12px rgba(239, 68, 68, 0.2);
}

/* --- 5. CARDS --- */
.cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 24px;
}

.card {
    background: var(--secondary-bg);
    padding: 28px;
    border-radius: 16px;
    box-shadow: var(--shadow-sm);
    border: 1px solid var(--border-color);
    transition: var(--transition);
    position: relative;
    overflow: hidden;
    cursor: default;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow-md);
    border-color: #6366f1; /* Indigo 500 */
}

.card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 4px;
    background: var(--primary-color);
    opacity: 0;
    transition: var(--transition);
}

.card:hover::before {
    opacity: 1;
}

.card h3 {
    color: var(--text-main);
    margin-bottom: 12px;
    font-size: 18px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 10px;
}

.card h3 i {
    color: var(--primary-color);
    background: var(--card-icon-bg); /* Use variable */
    padding: 8px;
    border-radius: 8px;
    font-size: 16px;
}

.card p {
    color: var(--text-muted);
    font-size: 14px;
    line-height: 1.6;
}

/* Responsive adjustment */
@media (max-width: 768px) {
    .sidebar { width: 80px; padding: 20px 10px; align-items: center; }
    .sidebar h2, .sidebar input, .sidebar a span { display: none; }
    .sidebar a { justify-content: center; padding: 15px; }
    .sidebar a i { margin: 0; font-size: 20px; }
    .main { margin-left: 80px; width: calc(100% - 80px); padding: 20px; }
    .topbar { flex-direction: column; gap: 15px; text-align: center; }
}
</style>
</head>
<body>

<div class="sidebar">
    <h2><i class="fa-solid fa-graduation-cap"></i> Berlajar:3</h2>

    <input type="text" id="searchMenu" placeholder="Cari menu...">

    <a href="index.php"><i class="fa-solid fa-house"></i> <span>Home</span></a>
    <a href="dashboard_guru.php"><i class="fa-solid fa-chalkboard-user"></i> <span>Dashboard Guru</span></a>
    <a href="dashboard_murid.php"><i class="fa-solid fa-user-graduate"></i> <span>Dashboard Murid</span></a>
    <a href="tugas.php"><i class="fa-solid fa-list-check"></i> <span>Tugas</span></a>
    <a href="settings.php"><i class="fa-solid fa-gear"></i> <span>Settings</span></a>
</div>

<div class="main">

    <div class="topbar">
        <div class="welcome">
            <h1>Halo, <?= htmlspecialchars($_SESSION['nama']); ?> 👋</h1>
            <p>Status Login: <span>GURU</span></p>
        </div>

        <div class="topbar-actions">
            <button class="theme-toggle-btn" id="themeToggle" title="Ganti Tema">
                <i class="fa-solid fa-moon"></i>
            </button>

            <div class="logout-top">
                <a href="../logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
            </div>
        </div>
    </div>

    <div class="cards">
        <div class="card">
            <h3><i class="fa-solid fa-chart-line"></i> Dashboard Guru</h3>
            <p>Kelola data, jadwal mengajar, dan aktivitas guru dengan antarmuka yang efisien.</p>
        </div>

        <div class="card">
            <h3><i class="fa-solid fa-users"></i> Dashboard Murid</h3>
            <p>Lihat perkembangan siswa, nilai, dan kelola data murid yang terdaftar di kelas.</p>
        </div>

        <div class="card">
            <h3><i class="fa-solid fa-sliders"></i> Settings</h3>
            <p>Sesuaikan profil akun, keamanan password, dan preferensi sistem aplikasi Anda.</p>
        </div>
    </div>
</div>

<script>
// --- 1. SCRIPT SEARCH MENU (Existing) ---
const searchInput = document.getElementById('searchMenu');
const menuLinks = document.querySelectorAll('.sidebar a');

searchInput.addEventListener('keyup', function () {
    const filter = searchInput.value.toLowerCase();
    menuLinks.forEach(link => {
        const text = link.textContent.toLowerCase(); 
        link.style.display = text.includes(filter) ? 'flex' : 'none'; 
    });
});

// --- 2. SCRIPT DARK MODE (New) ---
const themeToggle = document.getElementById('themeToggle');
const themeIcon = themeToggle.querySelector('i');
const body = document.body;

// Fungsi untuk update icon
function updateIcon(isDark) {
    if (isDark) {
        themeIcon.classList.remove('fa-moon');
        themeIcon.classList.add('fa-sun');
    } else {
        themeIcon.classList.remove('fa-sun');
        themeIcon.classList.add('fa-moon');
    }
}

// Cek LocalStorage saat halaman dimuat
const savedTheme = localStorage.getItem('theme');
if (savedTheme === 'dark') {
    body.classList.add('dark-mode');
    updateIcon(true);
}

// Event Listener saat tombol diklik
themeToggle.addEventListener('click', () => {
    body.classList.toggle('dark-mode');
    const isDark = body.classList.contains('dark-mode');
    
    // Simpan preferensi ke LocalStorage
    localStorage.setItem('theme', isDark ? 'dark' : 'light');
    
    // Update Icon
    updateIcon(isDark);
});
</script>

</body>
</html>