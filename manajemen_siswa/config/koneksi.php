<?php
$conn = mysqli_connect("localhost", "root", "", "manajemen_siswa");

if (!$conn) {
    die("Koneksi database gagal!");
}
