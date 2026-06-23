<?php
include 'koneksi_cloud.php';
if (!isset($_SESSION['login'])) { header("Location: login.php"); exit; }
include 'engine_wp.php';

function render_dropdown($kode, $nama_kriteria, $sub_json, $selected = null) {
    $kode = strtolower($kode);
    $html = "<div><label class='block font-black text-xs mb-1.5 text-gray-700'>" . strtoupper($kode) . " - " . htmlspecialchars($nama_kriteria) . "</label>";
    $html .= "<select name='$kode' required class='w-full border-2 border-[#1A202C] rounded-xl p-3 font-bold bg-white focus:outline-none focus:ring-2 focus:ring-[#8BD3DD]'>";
    $html .= "<option value='' disabled " . ($selected === null ? "selected" : "") . ">Pilih Keterangan...</option>";
    
    $opts = [];
    if (!empty($sub_json)) {
        $opts = json_decode($sub_json, true);
    }
    
    if (empty($opts)) {
        $opts = [1=>"Sangat Kurang", 3=>"Kurang", 5=>"Cukup", 7=>"Baik", 9=>"Sangat Baik"];
    }
    
    foreach($opts as $val => $text) {
        $sel = ($selected !== null && $selected == $val) ? "selected" : "";
        $html .= "<option value='$val' $sel>$text - Nilai: $val</option>";
    }
    $html .= "</select></div>";
    return $html;
}

if (isset($_POST['submit'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama_dosen']);
    $fields = ['nama_dosen'];
    $values = ["'$nama'"];

    foreach($kriteria as $k) {
        $kode = strtolower($k['kode']);
        $val = isset($_POST[$kode]) ? intval($_POST[$kode]) : 1;
        $fields[] = $kode;
        $values[] = "'$val'";
    }

    $query = "INSERT INTO alternatif (" . implode(', ', $fields) . ") VALUES (" . implode(', ', $values) . ")";
    
    if (mysqli_query($conn, $query)) {
        $_SESSION['alert'] = ['type' => 'success', 'message' => 'Data alternatif ' . $nama . ' berhasil ditambahkan!'];
        header("Location: peringkat.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input Data Dosen</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <style>body { font-family: 'Nunito', sans-serif; }</style>
</head>
<body class="bg-[#FDF8F5] text-[#1A202C] p-4 md:p-8">
    <div class="max-w-6xl mx-auto">
        <?php include 'navbar.php'; ?>
        <div class="bg-white border-4 border-[#1A202C] rounded-3xl p-6 md:p-8 shadow-[8px_8px_0px_0px_#1A202C] max-w-4xl mx-auto">
            <h2 class="text-2xl font-black mb-6 flex items-center gap-2">📝 Tambah Alternatif Dosen</h2>
            <form action="" method="POST" class="space-y-6">
                <div>
                    <label class="block font-black text-sm mb-2 text-[#1A202C]">Nama Lengkap Dosen</label>
                    <input type="text" name="nama_dosen" placeholder="Masukkan nama dosen..." required class="w-full border-2 border-[#1A202C] rounded-xl p-3 font-bold focus:outline-none focus:ring-4 focus:ring-[#8BD3DD] transition">
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <?php 
                    foreach($kriteria as $k) {
                        echo render_dropdown($k['kode'], $k['nama_kriteria'], $k['sub_kriteria'] ?? '');
                    }
                    ?>
                </div>
                <div class="pt-4">
                    <button type="submit" name="submit" class="w-full bg-[#FE98A3] text-[#1A202C] border-2 border-[#1A202C] font-black p-4 rounded-xl shadow-[4px_4px_0px_0px_#1A202C] hover:translate-y-0.5 hover:shadow-[2px_2px_0px_0px_#1A202C] transition-all cursor-pointer">Simpan Data Alternatif</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>