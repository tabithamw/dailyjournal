<?php
session_start();
include "koneksi.php"; // File koneksi database

// Pastikan user login
if (!isset($_SESSION['id'])) {
    header("location:login.php");
    exit;
}

$id = $_SESSION['id']; // Ambil ID user dari session

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Variabel untuk menyimpan status simpan
    $success = false;

    // 1. Ganti Password (Jika ada input password baru)
    if (!empty($_POST['password'])) {
        $password = md5($_POST['password']); // Enkripsi MD5
        $queryPassword = "UPDATE user SET password = '$password' WHERE id = $id";
        $success = mysqli_query($conn, $queryPassword);
    }

    // 2. Ganti Foto Profil
    if (!empty($_FILES['foto']['name'])) {
        $fotoName = basename($_FILES['foto']['name']);
        $targetDir = "img/";
        $targetFile = $targetDir . $fotoName;

        // Cek apakah file berhasil diunggah
        if (move_uploaded_file($_FILES['foto']['tmp_name'], $targetFile)) {
            // Hapus foto lama jika ada
            $querySelect = "SELECT foto FROM user WHERE id = $id";
            $result = mysqli_query($conn, $querySelect);
            $data = mysqli_fetch_assoc($result);

            if ($data && !empty($data['foto'])) {
                unlink($targetDir . $data['foto']); // Hapus file lama
            }

            // Simpan nama file baru ke database
            $queryFoto = "UPDATE user SET foto = '$fotoName' WHERE id = $id";
            $success = mysqli_query($conn, $queryFoto);
        }
    }

    // Redirect setelah penyimpanan
    if ($success) {
        echo "<script>
            alert('Data berhasil disimpan.');
            document.location='admin.php?page=profil';
        </script>";
    } else {
        echo "<script>
            alert('Gagal menyimpan data.');
            document.location='admin.php?page=profil';
        </script>";
    }
}
?>
