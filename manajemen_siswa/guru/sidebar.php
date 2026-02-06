<?php
// Cek session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Deteksi halaman saat ini untuk status Active
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<style>
/* STYLE SIDEBAR */
.sidebar {
    width: 260px;
    background: #1e293b; /* Warna Slate Gelap */
    height: 100vh;       /* Full tinggi layar */
    color: white;
    padding: 22px;
    position: sticky;    /* Tetap diam saat scroll */
    top: 0;
    display: flex;
    flex-direction: column;
    z-index: 50;
    flex-shrink: 0;      /* Mencegah sidebar mengecil */
    border-right: 1px solid #334155;
}

.sidebar h2 {
    text-align: center;
    margin-bottom: 25px;
    font-size: 20px;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    color: #f8fafc;
}

.sidebar h2 i { color: #4f46e5; }

/* ACTION BUTTON (TOMBOL MENONJOL) */
.action-btn {
    display: flex; align-items: center; justify-content: center;
    background: linear-gradient(135deg, #4f46e5, #4338ca);
    color: white !important; padding: 12px; border-radius: 12px;
    margin-bottom: 25px; text-decoration: none; font-weight: 600;
    font-size: 15px; box-shadow: 0 4px 10px rgba(79, 70, 229, 0.3);
    transition: all 0.3s ease; border: 1px solid rgba(255,255,255,0.1);
}
.action-btn:hover {
    transform: translateY(-3px); box-shadow: 0 8px 15px rgba(79, 70, 229, 0.4);
    background: linear-gradient(135deg, #4338ca, #3730a3);
}
.action-btn i { margin-right: 8px; }

/* MENU BIASA */
.sidebar-menu a {
    display: flex; align-items: center; padding: 13px 16px;
    color: #94a3b8; text-decoration: none; border-radius: 10px;
    margin-bottom: 8px; transition: 0.25s; font-size: 15px; font-weight: 500;
}
.sidebar-menu a i { width: 25px; text-align: center; margin-right: 10px; font-size: 16px; }
.sidebar-menu a:hover {
    background: rgba(255, 255, 255, 0.05); color: white; transform: translateX(5px);
}

/* STATUS ACTIVE */
.sidebar-menu a.active {
    background: #2563eb; color: white; font-weight: 600;
    box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2);
}

/* FOOTER LOGOUT */
.sidebar .footer { margin-top: auto; padding-top: 20px; }
.sidebar .footer a {
    display: flex; align-items: center; justify-content: center;
    background: rgba(239, 68, 68, 0.1); color: #ef4444; padding: 12px;
    border-radius: 10px; text-decoration: none; font-weight: 600;
    border: 1px solid rgba(239, 68, 68, 0.2); transition: 0.3s;
}
.sidebar .footer a:hover {
    background: #ef4444; color: white; border-color: #ef4444;
}
</style>

<div class="sidebar">
    <h2><i class="fa-solid fa-graduation-cap"></i>Berlajar:3</h2>

    <a href="tugas.php" class="action-btn">
        <i class="fa-solid fa-plus"></i> Kelola Tugas
    </a>

    <div class="sidebar-menu">
        <a href="index.php" class="<?= ($currentPage == 'index.php') ? 'active' : '' ?>">
            <i class="fa-solid fa-house"></i> Home
        </a>
        
        <a href="dashboard_murid.php" class="<?= ($currentPage == 'dashboard_murid.php') ? 'active' : '' ?>">
            <i class="fa-solid fa-user-graduate"></i> Data Murid
        </a>

        <a href="tugas.php" class="<?= ($currentPage == 'tugas.php') ? 'active' : '' ?>">
            <i class="fa-solid fa-list-check"></i> List Tugas
        </a>

        <a href="settings.php" class="<?= ($currentPage == 'settings.php') ? 'active' : '' ?>">
            <i class="fa-solid fa-gear"></i> Settings
        </a>
    </div>

    <div class="footer">
        <a href="../logout.php">
            <i class="fa-solid fa-right-from-bracket"></i> Logout
        </a>
    </div>
</div>