<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'guru') {
    header("Location: ../login.php");
    exit;
}

require_once __DIR__ . "/../config/koneksi.php";

/* SEARCH LOGIC */
$keyword = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';

// --- [UPDATE 1] Menambahkan 'mapel' ke dalam SELECT Query ---
$query = "
    SELECT id, nama, mapel, tempat_lahir, tanggal_lahir, foto
    FROM users
    WHERE role = 'guru'
";

if ($keyword !== '') {
    $query .= " AND (nama LIKE '%$keyword%' OR mapel LIKE '%$keyword%')"; // Bisa cari berdasarkan mapel juga
}

$query .= " ORDER BY id DESC"; 
$data = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard Guru - Data Pengajar</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
/* STYLE GLOBAL (SAMA SEPERTI SEBELUMNYA) */
:root {
    --primary-color: #4f46e5;
    --primary-hover: #4338ca;
    --sidebar-bg: #0f172a;
    --main-bg: #f3f4f6;
    --card-bg: #ffffff;
    --text-main: #1f2937;
    --text-muted: #6b7280;
    --border-color: #e5e7eb;
    --transition: all 0.3s ease;
}

* { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', sans-serif; }
body { display: flex; background: var(--main-bg); min-height: 100vh; color: var(--text-main); }
.sidebar { width: 280px; background: var(--sidebar-bg); min-height: 100vh; padding: 24px; display: flex; flex-direction: column; position: fixed; left: 0; top: 0; bottom: 0; z-index: 50; color: white; }
.sidebar h2 { font-size: 20px; font-weight: 700; margin-bottom: 30px; display: flex; align-items: center; gap: 10px; padding-left: 10px; }
.sidebar h2 i { color: var(--primary-color); }
.sidebar input { width: 100%; padding: 12px 16px; border-radius: 8px; border: 1px solid #334155; background: #1e293b; color: #e2e8f0; margin-bottom: 25px; font-size: 14px; outline: none; transition: var(--transition); }
.sidebar input:focus { border-color: var(--primary-color); }
.sidebar a { display: flex; align-items: center; padding: 14px 16px; color: #94a3b8; text-decoration: none; border-radius: 8px; margin-bottom: 8px; font-weight: 500; font-size: 15px; transition: var(--transition); }
.sidebar a i { width: 24px; margin-right: 10px; text-align: center; font-size: 16px; }
.sidebar a:hover { background: rgba(255, 255, 255, 0.05); color: white; transform: translateX(4px); }
.sidebar a.active { background: var(--primary-color); color: white; box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3); }
.main { flex: 1; margin-left: 280px; padding: 32px; width: calc(100% - 280px); }
.dashboard-card { background: var(--card-bg); border-radius: 16px; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); border: 1px solid var(--border-color); overflow: hidden; }
.dashboard-header { padding: 24px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border-color); background: #fff; }
.dashboard-header h2 { font-size: 18px; font-weight: 600; color: var(--text-main); display: flex; align-items: center; gap: 10px; }
.search-box { display: flex; gap: 10px; }
.search-box input { padding: 10px 16px; border-radius: 8px; border: 1px solid var(--border-color); font-size: 14px; width: 200px; outline: none; transition: var(--transition); }
.search-box input:focus { border-color: var(--primary-color); box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1); }
.search-box button { padding: 10px 20px; background: var(--sidebar-bg); border: none; color: white; border-radius: 8px; cursor: pointer; font-weight: 500; font-size: 14px; transition: var(--transition); }
.table-responsive { width: 100%; overflow-x: auto; }
table { width: 100%; border-collapse: collapse; }
th { background: #f8fafc; color: #64748b; font-weight: 600; text-transform: uppercase; font-size: 12px; letter-spacing: 0.5px; padding: 16px 24px; text-align: left; border-bottom: 1px solid var(--border-color); }
td { padding: 16px 24px; border-bottom: 1px solid var(--border-color); font-size: 14px; color: var(--text-main); vertical-align: middle; }
tr:hover td { background: #f8fafc; }
.foto-avatar { width: 45px; height: 45px; object-fit: cover; border-radius: 50%; border: 2px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
.empty-state { text-align: center; padding: 40px; color: var(--text-muted); }

.btn-add { background: var(--primary-color); color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-size: 14px; font-weight: 600; display: flex; align-items: center; gap: 8px; transition: var(--transition); }
.btn-add:hover { background: var(--primary-hover); }
.action-buttons { display: flex; gap: 8px; }
.btn-action { width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; border-radius: 6px; text-decoration: none; font-size: 14px; transition: var(--transition); }
.btn-edit { background: #e0f2fe; color: #0284c7; }
.btn-edit:hover { background: #0284c7; color: white; }
.btn-delete { background: #fee2e2; color: #ef4444; }
.btn-delete:hover { background: #ef4444; color: white; }

/* --- [UPDATE 2] Style Khusus Badge Mapel --- */
.badge-mapel {
    background: #fff1f2; /* Pink muda */
    color: #be123c;      /* Merah gelap */
    padding: 6px 12px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 600;
    border: 1px solid #fecdd3;
    display: inline-block;
}
</style>
</head>
<body>

<div class="sidebar">
    <h2><i class="fa-solid fa-graduation-cap"></i> <span>Berlajar:3</span></h2>
    <input type="text" id="searchMenu" placeholder="Cari menu...">
    <a href="index.php"><i class="fa-solid fa-house"></i> <span>Home</span></a>
    <a href="dashboard_guru.php" class="active"><i class="fa-solid fa-chalkboard-user"></i> <span>Dashboard Guru</span></a>
    <a href="dashboard_murid.php"><i class="fa-solid fa-user-graduate"></i> <span>Dashboard Murid</span></a>
    <a href="tugas.php"><i class="fa-solid fa-tasks"></i> <span>Kelola Tugas</span></a>
    <a href="settings.php"><i class="fa-solid fa-gear"></i> <span>Settings</span></a>
</div>

<div class="main">
    <div class="dashboard-card">
        <div class="dashboard-header">
            <div style="display: flex; align-items: center; gap: 20px;">
                <h2>Data Guru</h2>
                <a href="tambah_guru.php" class="btn-add">
                    <i class="fa-solid fa-plus"></i> Tambah Guru
                </a>
            </div>

            <form method="GET" class="search-box">
                <input type="text" name="search" placeholder="Cari nama atau mapel..." value="<?= htmlspecialchars($keyword); ?>">
                <button type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
            </form>
        </div>

        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th width="80">Foto</th>
                        <th>Nama Lengkap</th>
                        <th>Mata Pelajaran</th> <th>Tempat Lahir</th>
                        <th>Tanggal Lahir</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($data) > 0): ?>
                        <?php while ($d = mysqli_fetch_assoc($data)): ?>
                        <tr>
                            <td>
                                <img class="foto-avatar"
                                     src="../uploads/<?= !empty($d['foto']) ? htmlspecialchars($d['foto']) : 'default.png'; ?>"
                                     alt="Foto">
                            </td>
                            <td><strong><?= htmlspecialchars($d['nama']); ?></strong></td>
                            
                            <td>
                                <?php if (!empty($d['mapel'])): ?>
                                    <span class="badge-mapel"><?= htmlspecialchars($d['mapel']); ?></span>
                                <?php else: ?>
                                    <span style="color: #94a3b8; font-size: 12px;">-</span>
                                <?php endif; ?>
                            </td>

                            <td><?= htmlspecialchars($d['tempat_lahir']); ?></td>
                            <td>
                                <span style="background: #ecfccb; color: #4d7c0f; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 600;">
                                    <?= date('d M Y', strtotime($d['tanggal_lahir'])); ?>
                                </span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="edit_guru.php?id=<?= $d['id']; ?>" class="btn-action btn-edit" title="Edit">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <a href="hapus_guru.php?id=<?= $d['id']; ?>" class="btn-action btn-delete" title="Hapus" onclick="return confirm('Yakin ingin menghapus data guru ini?');">
                                        <i class="fa-solid fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="6" class="empty-state">Tidak ada data guru.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
const searchInput = document.getElementById('searchMenu');
const menuLinks = document.querySelectorAll('.sidebar a');
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