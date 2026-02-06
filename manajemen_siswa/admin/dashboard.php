<?php
include "../config/database.php";
$data = mysqli_query($conn, "SELECT * FROM siswa ORDER BY id DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard Admin</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>

<div class="dashboard">
    <h2>Dashboard Data Siswa</h2>

    <table>
        <tr>
            <th>No</th>
            <th>Foto</th>
            <th>Nama</th>
            <th>Email</th>
            <th>TTL</th>
            <th>Aksi</th>
        </tr>

        <?php $no=1; while($row = mysqli_fetch_assoc($data)) { ?>
        <tr>
            <td><?= $no++ ?></td>
            <td><img src="../uploads/<?= $row['foto'] ?>"></td>
            <td><?= $row['nama'] ?></td>
            <td><?= $row['email'] ?></td>
            <td><?= $row['tempat_lahir'] ?>, <?= $row['tanggal_lahir'] ?></td>
            <td>
                <a class="btn danger" href="delete.php?id=<?= $row['id'] ?>" onclick="return confirm('Hapus data?')">Hapus</a>
            </td>
        </tr>
        <?php } ?>
    </table>
</div>

</body>
</html>
