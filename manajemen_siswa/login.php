<?php
session_start();
include "config/koneksi.php";

$error = "";

if (isset($_POST['login'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    $query = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
    $user = mysqli_fetch_assoc($query);

    if ($user && password_verify($password, $user['password'])) {
        // Set Session
        $_SESSION['id']   = $user['id'];
        $_SESSION['nama'] = $user['nama'];
        $_SESSION['role'] = $user['role'];

        // LOGIKA PENGALIHAN HALAMAN (ROUTING)
        if ($user['role'] === 'admin') {
            header("Location: admin/");
        } elseif ($user['role'] === 'guru') {
            header("Location: guru/");
        } elseif ($user['role'] === 'murid') {
            // PERBAIKAN DI SINI: Arahkan murid ke folder murid
            header("Location: murid/"); 
        } else {
            // Jika role tidak dikenali, kembalikan ke index atau dashboard umum
            header("Location: index.php");
        }
        exit;
    } else {
        $error = "Email atau password salah!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Login</title>

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
    width: 380px;
    padding: 30px;
    border-radius: 14px;
    box-shadow: 0 20px 40px rgba(0,0,0,0.2);
}

.container h2 {
    text-align: center;
    margin-bottom: 10px;
    color: #1e293b;
}

.subtitle {
    text-align: center;
    font-size: 14px;
    color: #64748b;
    margin-bottom: 20px;
}

/* FORM */
.form-group {
    margin-bottom: 15px;
}

label {
    display: block;
    font-size: 13px;
    color: #475569;
    margin-bottom: 6px;
}

input {
    width: 100%;
    padding: 12px 14px;
    border-radius: 8px;
    border: 1px solid #cbd5e1;
    font-size: 14px;
    transition: 0.3s;
}

input:focus {
    border-color: #2563eb;
    outline: none;
}

/* BUTTON */
button {
    width: 100%;
    padding: 12px;
    background: #2563eb;
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 15px;
    font-weight: bold;
    cursor: pointer;
    transition: 0.3s;
    margin-top: 10px;
}

button:hover {
    background: #1e40af;
}

/* ERROR */
.error {
    background: #fee2e2;
    color: #b91c1c;
    padding: 10px;
    border-radius: 8px;
    font-size: 14px;
    margin-bottom: 15px;
    text-align: center;
}

/* LINK */
.link {
    text-align: center;
    margin-top: 15px;
    font-size: 14px;
    color: #64748b;
}

.link a {
    color: #2563eb;
    font-weight: bold;
    text-decoration: none;
}

.link a:hover {
    text-decoration: underline;
}
</style>
</head>

<body>

<div class="container">
    <h2>Login</h2>
    <p class="subtitle">Masuk ke akun Anda</p>

    <?php if ($error): ?>
        <div class="error"><?= $error; ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" required>
        </div>

        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" required>
        </div>

        <button type="submit" name="login">Login</button>
    </form>

    <p class="link">
        Belum punya akun?
        <a href="register.php">Daftar</a>
    </p>
</div>

</body>
</html>