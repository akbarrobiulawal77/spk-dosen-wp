<?php
session_start();
include 'koneksi_cloud.php';
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

include 'engine_wp.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Perangkingan WP</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>body { font-family: 'Nunito', sans-serif; }</style>
</head>
<body class="bg-[#FDF8F5] text-[#1A202C] p-4 md:p-8 relative">
    <div class="max-w-6xl mx-auto">
        <?php include 'navbar.php'; ?>

        <div class="bg-white border-4 border-[#1A202C] rounded-3xl p-4 md:p-8 shadow-[8px_8px_0px_0px_#1A202C]">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
                <div>
                    <h2 class="text-2xl font-black flex items-center gap-2">📊 Tabel Urutan Peringkat</h2>
                    <p class="text-sm font-bold text-gray-500 mt-1">Data otomatis diurutkan secara real-time berdasarkan matriks preferensi nilai akhir tertinggi.</p>
                </div>
                <a href="tambah.php" class="bg-[#FAAE2B] text-black border-2 border-[#1A202C] font-black px-4 py-2.5 rounded-xl text-sm shadow-[3px_3px_0px_0px_#1A202C] hover:translate-y-0.5 hover:shadow-[1px_1px_0px_0px_#1A202C] transition-all flex items-center gap-2">➕ Tambah Baru</a>
            </div>

            <div class="overflow-x-auto rounded-2xl border-2 border-[#1A202C]">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-[#8BD3DD] border-b-2 border-[#1A202C]">
                            <th class="p-4 font-black text-sm text-[#1A202C]">Peringkat</th>
                            <th class="p-4 font-black text-sm text-[#1A202C]">Nama Alternatif Dosen</th>
                            <th class="p-4 font-black text-sm text-[#1A202C]">Nilai Akhir (V)</th>
                            <th class="p-4 font-black text-sm text-[#1A202C]"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 font-bold text-sm">
                        <?php foreach ($sorted_alt as $alt): ?>
                            <tr class="<?php echo ($ranks[$alt['id']] == 1) ? 'bg-[#FFBDC4]' : 'bg-white hover:bg-[#FDF8F5]'; ?> transition-colors">
                                <td class="p-4">
                                    <?php if ($ranks[$alt['id']] == 1): ?>
                                        <span class="bg-[#FE98A3] border border-[#1A202C] px-3 py-1 rounded-lg text-xs font-black shadow-[2px_2px_0px_0px_#1A202C]">🥇 #1 BEST</span>
                                    <?php else: ?>
                                        <span class="text-gray-500 px-1">#<?php echo $ranks[$alt['id']]; ?></span>
                                    <?php endif; ?>
                                </td>
                                <td class="p-4 text-[#1A202C] font-extrabold"><?php echo $alt['nama_dosen']; ?></td>
                                <td class="p-4 text-[#DD6B20] font-black"><?php echo number_format($alt['nilai_v'], 6); ?></td>
                                <td class="p-4 flex gap-2">
                                    <a href="edit.php?id=<?php echo $alt['id']; ?>" class="bg-[#8BD3DD] text-[#1A202C] border border-[#1A202C] px-3 py-1.5 rounded-lg text-xs font-black transition-transform hover:scale-105">Edit</a>
                                    <button onclick="bukaModal('<?php echo $alt['id']; ?>', '<?php echo addslashes($alt['nama_dosen']); ?>')" class="bg-[#E53E3E] text-white border border-[#1A202C] px-3 py-1.5 rounded-lg text-xs font-black transition-transform hover:scale-105 cursor-pointer">Hapus</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="mt-10 border-t-4 border-[#F7D7B3] pt-8">
                <h3 class="text-xl font-black mb-4">📈 Grafik Perbandingan Nilai Akhir (Vektor V)</h3>
                <canvas id="grafikPeringkat" height="100"></canvas>
            </div>
        </div>
    </div>

    <div id="modalHapus" class="fixed inset-0 bg-black/60 z-50 hidden flex items-center justify-center backdrop-blur-sm transition-opacity">
        <div class="bg-white border-4 border-[#1A202C] rounded-3xl p-6 md:p-8 shadow-[8px_8px_0px_0px_#1A202C] max-w-sm w-full mx-4 transform scale-95 transition-transform duration-300" id="modalContent">
            <div class="text-center">
                <div class="text-5xl mb-4">🗑️</div>
                <h3 class="text-xl font-black mb-2 text-[#1A202C]">Hapus Data?</h3>
                <p class="text-sm font-bold text-gray-600 mb-6">Apakah Anda yakin ingin menghapus <span id="namaDosenHapus" class="text-[#E53E3E] font-black"></span>? Peringkat akan dihitung ulang.</p>
                <div class="flex gap-3">
                    <button onclick="tutupModal()" class="w-1/2 bg-white text-[#1A202C] border-2 border-[#1A202C] font-black py-3 rounded-xl shadow-[3px_3px_0px_0px_#1A202C] hover:translate-y-0.5 hover:shadow-[1px_1px_0px_0px_#1A202C] transition-all cursor-pointer">Batal</button>
                    <a id="linkHapus" href="#" class="w-1/2 bg-[#E53E3E] text-white border-2 border-[#1A202C] font-black py-3 rounded-xl shadow-[3px_3px_0px_0px_#1A202C] hover:translate-y-0.5 hover:shadow-[1px_1px_0px_0px_#1A202C] transition-all flex items-center justify-center">Ya, Hapus</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        function bukaModal(id, nama) {
            document.getElementById('namaDosenHapus').innerText = nama;
            document.getElementById('linkHapus').href = 'hapus.php?id=' + id;
            document.getElementById('modalHapus').classList.remove('hidden');
            setTimeout(() => { document.getElementById('modalContent').classList.remove('scale-95'); document.getElementById('modalContent').classList.add('scale-100'); }, 10);
        }
        function tutupModal() {
            document.getElementById('modalContent').classList.remove('scale-100');
            document.getElementById('modalContent').classList.add('scale-95');
            setTimeout(() => { document.getElementById('modalHapus').classList.add('hidden'); }, 200);
        }

        const ctx = document.getElementById('grafikPeringkat').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: [<?php foreach($sorted_alt as $alt) { echo "'" . $alt['nama_dosen'] . "',"; } ?>],
                datasets: [{
                    label: 'Nilai Akhir (V)',
                    data: [<?php foreach($sorted_alt as $alt) { echo $alt['nilai_v'] . ","; } ?>],
                    backgroundColor: '#8BD3DD',
                    borderColor: '#1A202C',
                    borderWidth: 2,
                    borderRadius: 8,
                    hoverBackgroundColor: '#FE98A3'
                }]
            },
            options: { responsive: true, scales: { y: { beginAtZero: true } }, plugins: { legend: { display: false } } }
        });
    </script>
</body>
</html>