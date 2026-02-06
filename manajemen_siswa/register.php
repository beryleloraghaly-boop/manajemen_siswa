<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Registrasi Akun</title>

<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Segoe UI', sans-serif;
}

body {
    min-height: 100vh;
    background: linear-gradient(135deg, #2563eb, #1e40af);
    display: flex;
    justify-content: center;
    align-items: center;
}

/* CARD */
.container {
    background: #ffffff;
    width: 720px;
    padding: 35px;
    border-radius: 16px;
    box-shadow: 0 25px 50px rgba(0,0,0,0.25);
}

.container h2 {
    text-align: center;
    margin-bottom: 25px;
    color: #1e293b;
}

/* FORM GRID */
.form-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 15px 20px;
}

/* FULL WIDTH */
.full {
    grid-column: span 2;
}

.container input,
.container select {
    width: 100%;
    padding: 12px 14px;
    border-radius: 8px;
    border: 1px solid #cbd5e1;
    font-size: 14px;
}

.container input:focus,
.container select:focus {
    border-color: #2563eb;
    outline: none;
}

/* FILE */
input[type="file"] {
    padding: 10px;
    background: #f8fafc;
}

/* BUTTON */
.container button {
    margin-top: 10px;
    width: 100%;
    padding: 14px;
    background: #2563eb;
    color: white;
    border: none;
    border-radius: 10px;
    font-size: 15px;
    font-weight: bold;
    cursor: pointer;
    transition: 0.3s;
}

.container button:hover {
    background: #1e40af;
}

/* FOOTER */
.footer-text {
    text-align: center;
    margin-top: 18px;
    font-size: 14px;
    color: #64748b;
}

.footer-text a {
    color: #2563eb;
    font-weight: bold;
    text-decoration: none;
}

.footer-text a:hover {
    text-decoration: underline;
}

/* RESPONSIVE */
@media(max-width: 768px) {
    .container {
        width: 95%;
    }

    .form-grid {
        grid-template-columns: 1fr;
    }

    .full {
        grid-column: span 1;
    }
}
</style>
</head>

<body>

<div class="container">
    <h2>Registrasi Akun</h2>

    <form method="POST" action="process/register_process.php" enctype="multipart/form-data">

        <div class="form-grid">

            <input type="text" name="nama" placeholder="Nama Lengkap" required>
            <input type="email" name="email" placeholder="Email" required>

            <input type="password" name="password" placeholder="Password" required>
            <input type="password" name="confirm_password" placeholder="Konfirmasi Password" required>

            <select name="role" id="role" class="full" required>
                <option value="">Daftar sebagai</option>
                <option value="guru">Guru</option>
                <option value="murid">Murid</option>
            </select>

            <input type="file" name="foto" accept="image/*" class="full" required>

            <input type="text" name="alamat" placeholder="Alamat Lengkap" class="full" required>

            <input type="text" name="tempat_lahir" placeholder="Tempat Lahir" required>
            <input type="date" name="tanggal_lahir" required>

            <!-- KHUSUS GURU -->
            <div id="guruField" class="full" style="display:none;">
                <input type="text" name="mapel" placeholder="Mata Pelajaran">
            </div>

        </div>

        <button type="submit">Daftar</button>
    </form>

    <p class="footer-text">
        Sudah punya akun?
        <a href="login.php">Login</a>
    </p>
</div>

<script>
const roleSelect = document.getElementById('role');
const guruField = document.getElementById('guruField');

roleSelect.addEventListener('change', function () {
    guruField.style.display = this.value === 'guru' ? 'block' : 'none';
});
</script>

</body>
</html>
