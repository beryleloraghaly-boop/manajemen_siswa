<?php
session_start();
include "../config/db.php";

$email    = $_POST['email'];
$password = $_POST['password'];

$query = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
$user  = mysqli_fetch_assoc($query);

if ($user && password_verify($password, $user['password'])) {

    $_SESSION['id']   = $user['id'];
    $_SESSION['nama'] = $user['nama'];
    $_SESSION['role'] = $user['role'];

    if ($user['role'] === 'guru') {
        header("Location: ../guru/home.php");
    } else {
        header("Location: ../murid/home.php");
    }
    exit;
} else {
    header("Location: ../login.php?error=1");
    exit;
}
