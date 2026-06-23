<?php
include 'koneksi_cloud.php';
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

include 'engine_wp.php';

$total_dosen = count($alternatif);
$total_kriteria = count($kriteria);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard SPK WP</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <style>body { font-family: 'Nunito', sans-serif; }</style>
</head>
<body class="bg-[#FDF8F5] text-[#1A202C] p-4 md:p-8">
    <div class="max-w-6xl mx-auto">
        <?php include 'navbar.php'; ?>
        
        <div class="bg-white border-4 border-[#1A202C] rounded-3xl p-6 md:p-10 shadow-[8px_8px_0px_0px_#1A202C] mb-8">
            <div class="max-w-2xl">
                <span class="bg-[#FFBDC4] text-[#1A202C] font-black px-4 py-1.5 rounded-full text-xs uppercase tracking-wider border-2 border-[#1A202C]">Sistem Pendukung Keputusan</span>
                <h1 class="text-3xl md:text-5xl font-black mt-4 mb-4 leading-tight">Selamat Datang di SPK Penentuan Dosen Terbaik!</h1>
                <p class="text-gray-600 font-bold text-base md:text-lg mb-6">Aplikasi ini dirancang khusus untuk memproses data kualitatif dosen secara otomatis menggunakan metode ilmiah <span class="text-[#DD6B20] font-extrabold">Weighted Product (WP)</span> agar hasil keputusan objektif dan transparan.</p>
                <a href="peringkat.php" class="inline-block bg-[#FAAE2B] text-black border-2 border-black font-black px-6 py-3.5 rounded-2xl shadow-[4px_4px_0px_0px_#000] hover:translate-y-0.5 hover:shadow-[2px_2px_0px_0px_#000] transition-all">Lihat Hasil Perangkingan &rarr;</a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-[#8BD3DD] border-4 border-[#1A202C] rounded-3xl p-6 shadow-[6px_6px_0px_0px_#1A202C]">
                <span class="text-3xl">👨‍🏫</span>
                <h3 class="font-black text-xl mt-2">Total Alternatif</h3>
                <p class="text-4xl font-black mt-1"><?php echo $total_dosen; ?> <span class="text-base font-bold text-gray-700">Dosen Terdaftar</span></p>
            </div>
            <div class="bg-[#F7D7B3] border-4 border-[#1A202C] rounded-3xl p-6 shadow-[6px_6px_0px_0px_#1A202C]">
                <span class="text-3xl">🎯</span>
                <h3 class="font-black text-xl mt-2">Jumlah Kriteria</h3>
                <p class="text-4xl font-black mt-1"><?php echo $total_kriteria; ?> <span class="text-base font-bold text-gray-700">Aspek Penilaian</span></p>
            </div>
            <div class="bg-[#FFBDC4] border-4 border-[#1A202C] rounded-3xl p-6 shadow-[6px_6px_0px_0px_#1A202C]">
                <span class="text-3xl">⚙️</span>
                <h3 class="font-black text-xl mt-2">Metode Sistem</h3>
                <p class="text-2xl font-black mt-3">Weighted Product <span class="block text-sm font-bold text-gray-700 mt-0.5">(Normalisasi Pangkat Multi-Atribut)</span></p>
            </div>
        </div>
    </div>
</body>
</html>