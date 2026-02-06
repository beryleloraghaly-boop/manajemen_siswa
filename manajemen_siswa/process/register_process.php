<?php
session_start();
include "../config/koneksi.php";

// ambil data
$nama           = mysqli_real_escape_string($conn, $_POST['nama']);
$email          = mysqli_real_escape_string($conn, $_POST['email']);
$tempat_lahir   = mysqli_real_escape_string($conn, $_POST['tempat_lahir']);
$tanggal_lahir  = $_POST['tanggal_lahir'];
$password       = $_POST['password'];
$confirm        = $_POST['confirm_password'];
$role           = $_POST['role'];

// validasi password
if ($password !== $confirm) {
    echo "<script>
        alert('Konfirmasi password tidak sesuai!');
        window.location='../register.php';
    </script>";
    exit;
}

// cek email sudah ada atau belum
$cek = mysqli_query($conn, "SELECT id FROM users WHERE email='$email'");
if (mysqli_num_rows($cek) > 0) {
    echo "<script>
        alert('Email sudah terdaftar, silakan login!');
        window.location='../login.php';
    </script>";
    exit;
}

// hash password
$password_hash = password_hash($password, PASSWORD_DEFAULT);

// upload foto
$foto_name = $_FILES['foto']['name'];
$tmp = $_FILES['foto']['tmp_name'];
$folder = "../uploads/";

if (!is_dir($folder)) {
    mkdir($folder);
}

$foto_baru = time() . "_" . $foto_name;
move_uploaded_file($tmp, $folder . $foto_baru);

// insert ke database
$query = mysqli_query($conn, "
    INSERT INTO users 
    (nama, email, tempat_lahir, tanggal_lahir, password, role, foto)
    VALUES
    ('$nama', '$email', '$tempat_lahir', '$tanggal_lahir', '$password_hash', '$role', '$foto_baru')
");

// auto login setelah register
$_SESSION['nama'] = $nama;
$_SESSION['email'] = $email;
$_SESSION['role'] = $role;

// redirect berdasarkan role
if ($role === 'guru') {
    header("Location: ../guru/");
} else {
    header("Location: ../murid/");
}
exit;
