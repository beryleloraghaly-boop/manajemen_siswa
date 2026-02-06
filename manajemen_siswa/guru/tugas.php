<?php
session_start();
// Pastikan file koneksi database sudah benar path-nya
include '../config/koneksi.php'; 

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'guru') {
    // header("Location: ../login.php");
    // exit;
    $_SESSION['nama'] = "Budi Santoso";
    $_SESSION['role'] = "guru";
}

// --- LOGIKA PHP: TAMBAH TUGAS ---
$message = "";
if (isset($_POST['simpan_tugas'])) {
    $judul = $_POST['judul'];
    $mapel = $_POST['mapel'];
    $deadline = $_POST['deadline'];
    $deskripsi = $_POST['deskripsi'];
    $link_tugas = $_POST['link_tugas']; // Ambil data link

    // Query Insert ke Database (Ditambahkan kolom link_tugas)
    $query = "INSERT INTO tugas (judul, mapel, deskripsi, deadline, link_tugas) VALUES ('$judul', '$mapel', '$deskripsi', '$deadline', '$link_tugas')";
    
    if (mysqli_query($conn, $query)) {
        $message = "<div class='alert success'>Tugas berhasil ditambahkan dan dikirim ke murid!</div>";
    } else {
        $message = "<div class='alert error'>Gagal menambahkan tugas: " . mysqli_error($conn) . "</div>";
    }
}

// --- LOGIKA PHP: AMBIL DATA TUGAS (READ) ---
$result = mysqli_query($conn, "SELECT * FROM tugas ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Guru - Kelola Tugas</title>

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
/* --- 1. GLOBAL RESET & VARIABLES (SAMA SEPERTI HOME) --- */
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
    --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
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
    --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.5);
}

* { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', sans-serif; }
body { display: flex; background: var(--main-bg); min-height: 100vh; color: var(--text-main); transition: background 0.3s ease, color 0.3s ease; }

/* --- 2. SIDEBAR --- */
.sidebar { width: 280px; background: var(--sidebar-bg); min-height: 100vh; color: white; padding: 24px; display: flex; flex-direction: column; position: fixed; left: 0; top: 0; bottom: 0; z-index: 50; }
.sidebar h2 { text-align: left; margin-bottom: 30px; font-size: 20px; font-weight: 700; display: flex; align-items: center; gap: 10px; color: #fff; padding-left: 10px; }
.sidebar h2 i { color: var(--primary-color); }
.sidebar input { width: 100%; padding: 12px 16px; border-radius: 8px; border: 1px solid #334155; background: #1e293b; color: #e2e8f0; margin-bottom: 25px; font-size: 14px; transition: var(--transition); outline: none; }
.sidebar input:focus { border-color: var(--primary-color); background: #1e293b; box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.2); }
.sidebar a { display: flex; align-items: center; padding: 14px 16px; color: #94a3b8; text-decoration: none; border-radius: 8px; margin-bottom: 8px; transition: var(--transition); font-weight: 500; font-size: 15px; }
.sidebar a i { width: 24px; margin-right: 10px; text-align: center; font-size: 16px; }
.sidebar a:hover, .sidebar a.active { background: rgba(255, 255, 255, 0.05); color: white; transform: translateX(4px); }

/* --- 3. MAIN CONTENT --- */
.main { flex: 1; margin-left: 280px; padding: 32px; width: calc(100% - 280px); }

/* --- 4. TOP BAR --- */
.topbar { display: flex; justify-content: space-between; align-items: center; background: var(--secondary-bg); padding: 20px 24px; border-radius: 16px; margin-bottom: 32px; box-shadow: var(--shadow-sm); border: 1px solid var(--border-color); transition: background 0.3s ease, border-color 0.3s ease; }
.topbar .welcome h1 { font-size: 24px; font-weight: 700; color: var(--text-main); margin-bottom: 4px; }
.topbar .welcome p { color: var(--text-muted); font-size: 14px; }
.topbar .welcome span { color: var(--primary-color); font-weight: 600; background: var(--card-icon-bg); padding: 2px 8px; border-radius: 4px; font-size: 12px; }
.topbar-actions { display: flex; align-items: center; gap: 15px; }
.theme-toggle-btn { background: transparent; border: 1px solid var(--border-color); color: var(--text-muted); width: 40px; height: 40px; border-radius: 8px; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 18px; transition: var(--transition); }
.theme-toggle-btn:hover { background: var(--card-icon-bg); color: var(--primary-color); border-color: var(--primary-color); }
.logout-top a { background: var(--secondary-bg); color: var(--danger); padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: 600; transition: var(--transition); border: 1px solid #fee2e2; font-size: 14px; display: inline-flex; align-items: center; gap: 8px; }
body.dark-mode .logout-top a { border-color: #7f1d1d; background: rgba(239, 68, 68, 0.1); }
.logout-top a:hover { background: var(--danger); color: white; border-color: var(--danger); box-shadow: 0 4px 12px rgba(239, 68, 68, 0.2); }

/* --- 5. STYLING KHUSUS HALAMAN TUGAS --- */
.content-grid {
    display: grid;
    grid-template-columns: 1fr 2fr; /* Form di kiri (kecil), Tabel di kanan (besar) */
    gap: 24px;
}

@media (max-width: 1024px) {
    .content-grid { grid-template-columns: 1fr; }
}

.card {
    background: var(--secondary-bg);
    padding: 28px;
    border-radius: 16px;
    box-shadow: var(--shadow-sm);
    border: 1px solid var(--border-color);
    transition: var(--transition);
}

.card h3 {
    color: var(--text-main);
    margin-bottom: 20px;
    font-size: 18px;
    font-weight: 600;
    display: flex; align-items: center; gap: 10px;
}
.card h3 i { color: var(--primary-color); background: var(--card-icon-bg); padding: 8px; border-radius: 8px; font-size: 16px; }

/* FORM STYLING */
.form-group { margin-bottom: 15px; }
.form-group label { display: block; color: var(--text-main); font-size: 14px; font-weight: 500; margin-bottom: 6px; }
.form-control {
    width: 100%;
    padding: 10px 14px;
    border-radius: 8px;
    border: 1px solid var(--border-color);
    background: var(--main-bg);
    color: var(--text-main);
    font-size: 14px;
    outline: none;
    transition: var(--transition);
}
.form-control:focus { border-color: var(--primary-color); box-shadow: 0 0 0 2px rgba(79, 70, 229, 0.1); }
textarea.form-control { resize: vertical; min-height: 100px; }

.btn-submit {
    width: 100%;
    padding: 12px;
    background: var(--primary-color);
    color: white;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: var(--transition);
}
.btn-submit:hover { background: var(--primary-hover); transform: translateY(-2px); }

/* TABLE STYLING */
.table-responsive { overflow-x: auto; }
table { width: 100%; border-collapse: collapse; min-width: 600px; }
th, td { padding: 14px; text-align: left; border-bottom: 1px solid var(--border-color); font-size: 14px; color: var(--text-main); }
th { color: var(--text-muted); font-weight: 600; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px; }
td span.badge {
    padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 600;
    background: var(--card-icon-bg); color: var(--primary-color);
}
td a.link-btn {
    color: var(--primary-color);
    font-weight: 600;
    text-decoration: none;
    padding: 6px 12px;
    background: var(--card-icon-bg);
    border-radius: 6px;
    font-size: 12px;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    transition: 0.2s;
}
td a.link-btn:hover { background: var(--primary-color); color: white; }

/* Alert */
.alert { padding: 12px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; font-weight: 500; }
.alert.success { background: #d1fae5; color: #065f46; border: 1px solid #6ee7b7; }
.alert.error { background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; }

/* Dark mode overrides specific for form elements if needed */
body.dark-mode .form-control { background: #0f172a; }
body.dark-mode .alert.success { background: #064e3b; color: #a7f3d0; border-color: #065f46; }

/* Responsive Sidebar */
@media (max-width: 768px) {
    .sidebar { width: 80px; padding: 20px 10px; align-items: center; }
    .sidebar h2, .sidebar input, .sidebar a span { display: none; }
    .sidebar a { justify-content: center; padding: 15px; }
    .main { margin-left: 80px; width: calc(100% - 80px); padding: 20px; }
    .topbar { flex-direction: column; gap: 15px; text-align: center; }
    .content-grid { grid-template-columns: 1fr; }
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
    <a href="tugas.php" class="active" style="background: rgba(255, 255, 255, 0.05); color: white;"><i class="fa-solid fa-list-check"></i> <span>Tugas</span></a>
    <a href="settings.php"><i class="fa-solid fa-gear"></i> <span>Settings</span></a>
</div>

<div class="main">
    <div class="topbar">
        <div class="welcome">
            <h1>Kelola Tugas 📚</h1>
            <p>Buat tugas baru untuk para siswa.</p>
        </div>
        <div class="topbar-actions">
            <button class="theme-toggle-btn" id="themeToggle" title="Ganti Tema"><i class="fa-solid fa-moon"></i></button>
            <div class="logout-top"><a href="../logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a></div>
        </div>
    </div>

    <?= $message; ?>

    <div class="content-grid">
        <div class="card">
            <h3><i class="fa-solid fa-plus"></i> Buat Tugas Baru</h3>
            <form method="POST" action="">
                <div class="form-group">
                    <label>Judul Tugas</label>
                    <input type="text" name="judul" class="form-control" placeholder="Contoh: Latihan Matematika Bab 1" required>
                </div>
                <div class="form-group">
                    <label>Mata Pelajaran</label>
                    <select name="mapel" class="form-control" required>
                        <option value="Matematika">Matematika</option>
                        <option value="Bahasa Indonesia">Bahasa Indonesia</option>
                        <option value="IPA">IPA</option>
                        <option value="IPS">IPS</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Deadline</label>
                    <input type="date" name="deadline" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label>Link Tugas</label>
                    <div style="position: relative;">
                        <input type="url" name="link_tugas" class="form-control" placeholder="https://youtube.com/..." style="padding-left: 35px;">
                        <i class="fa-solid fa-link" style="position: absolute; left: 12px; top: 12px; color: var(--text-muted);"></i>
                    </div>
                    <small style="color: var(--text-muted); font-size: 12px;">Masukkan URL lengkap (awalan http:// atau https://)</small>
                </div>

                <div class="form-group">
                    <label>Deskripsi</label>
                    <textarea name="deskripsi" class="form-control" placeholder="Tuliskan detail tugas..." required></textarea>
                </div>
                <button type="submit" name="simpan_tugas" class="btn-submit">Kirim Tugas</button>
            </form>
        </div>

        <div class="card">
            <h3><i class="fa-solid fa-list"></i> Daftar Tugas Aktif</h3>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Judul</th>
                            <th>Mapel</th>
                            <th>Deadline</th>
                            <th>Link</th> <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $no = 1;
                        if(mysqli_num_rows($result) > 0) {
                            while($row = mysqli_fetch_assoc($result)) { 
                        ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td><?= htmlspecialchars($row['judul']); ?></td>
                            <td><span class="badge"><?= htmlspecialchars($row['mapel']); ?></span></td>
                            <td><?= date('d M Y', strtotime($row['deadline'])); ?></td>
                            
                            <td>
                                <?php if(!empty($row['link_tugas'])): ?>
                                    <a href="<?= htmlspecialchars($row['link_tugas']); ?>" target="_blank" class="link-btn">
                                        <i class="fa-solid fa-external-link-alt"></i> Buka
                                    </a>
                                <?php else: ?>
                                    <span style="color: var(--text-muted); font-size: 12px;">-</span>
                                <?php endif; ?>
                            </td>

                            <td style="color: var(--primary-color); font-weight:600;">Terbit</td>
                        </tr>
                        <?php 
                            } 
                        } else {
                            echo "<tr><td colspan='6' style='text-align:center; padding: 20px;'>Belum ada tugas dibuat.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
// --- SCRIPT DARK MODE (Sama seperti dashboard) ---
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