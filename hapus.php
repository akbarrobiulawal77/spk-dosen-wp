<?php
include 'koneksi_cloud.php';
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    $check = mysqli_query($conn, "SELECT nama_dosen FROM alternatif WHERE id = '$id'");
    $data = mysqli_fetch_assoc($check);
    $nama = $data['nama_dosen'];

    $query = "DELETE FROM alternatif WHERE id = '$id'";
    if (mysqli_query($conn, $query)) {
        $_SESSION['alert'] = [
            'type' => 'warning',
            'message' => 'Data alternatif ' . $nama . ' telah berhasil dihapus dari sistem.'
        ];
    }
}

header("Location: peringkat.php");
exit;
?>