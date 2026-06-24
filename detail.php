<?php
session_start();
include 'koneksi_cloud.php';
if (!isset($_SESSION['login'])) { header("Location: login.php"); exit; }

$check_col = mysqli_query($conn, "SHOW COLUMNS FROM kriteria LIKE 'sub_kriteria'");
if(mysqli_num_rows($check_col) == 0) {
    mysqli_query($conn, "ALTER TABLE kriteria ADD sub_kriteria TEXT NULL AFTER bobot");
}

if(isset($_POST['tambah_kriteria'])) {
    $q_kode = mysqli_query($conn, "SELECT kode FROM kriteria");
    $max_num = 0;
    while($row_k = mysqli_fetch_assoc($q_kode)) {
        $num = (int)str_ireplace('c', '', $row_k['kode']);
        if($num > $max_num) {
            $max_num = $num;
        }
    }
    $kode = 'c' . ($max_num + 1);
    
    $nama = $_POST['nama_kriteria'];
    $bobot = $_POST['bobot'];
    $atribut = $_POST['atribut'];

    $sub = [];
    if(isset($_POST['nilai']) && isset($_POST['ket'])) {
        for($i=0; $i<count($_POST['nilai']); $i++) {
            $n = trim($_POST['nilai'][$i]);
            $k_text = trim($_POST['ket'][$i]);
            if($n != '' && $k_text != '') { $sub[$n] = $k_text; }
        }
    }
    $sub_json = json_encode($sub);

    mysqli_query($conn, "INSERT INTO kriteria (kode, nama_kriteria, atribut, bobot, sub_kriteria) VALUES ('$kode', '$nama', '$atribut', '$bobot', '$sub_json')");
    mysqli_query($conn, "ALTER TABLE alternatif ADD $kode INT NOT NULL DEFAULT 1");
    
    $_SESSION['alert'] = ['type'=>'success', 'message'=>"Kriteria $nama berhasil ditambahkan ke sistem!"];
    header("Location: detail.php"); exit;
}

if(isset($_POST['edit_kriteria'])) {
    $id = $_POST['id_kriteria'];
    $nama = $_POST['nama_kriteria'];
    $bobot = $_POST['bobot'];
    $atribut = $_POST['atribut'];

    $sub = [];
    if(isset($_POST['nilai']) && isset($_POST['ket'])) {
        for($i=0; $i<count($_POST['nilai']); $i++) {
            $n = trim($_POST['nilai'][$i]);
            $k_text = trim($_POST['ket'][$i]);
            if($n != '' && $k_text != '') { $sub[$n] = $k_text; }
        }
    }
    $sub_json = json_encode($sub);

    mysqli_query($conn, "UPDATE kriteria SET nama_kriteria='$nama', bobot='$bobot', atribut='$atribut', sub_kriteria='$sub_json' WHERE id='$id'");
    $_SESSION['alert'] = ['type'=>'success', 'message'=>"Data kriteria berhasil diperbarui!"];
    header("Location: detail.php"); exit;
}

if(isset($_GET['hapus_kriteria'])) {
    $id_k = $_GET['hapus_kriteria'];
    $q_k = mysqli_query($conn, "SELECT kode FROM kriteria WHERE id='$id_k'");
    if(mysqli_num_rows($q_k) > 0) {
        $d_k = mysqli_fetch_assoc($q_k);
        $kode = $d_k['kode'];
        mysqli_query($conn, "DELETE FROM kriteria WHERE id='$id_k'");
        mysqli_query($conn, "ALTER TABLE alternatif DROP COLUMN $kode");
        $_SESSION['alert'] = ['type'=>'warning', 'message'=>"Kriteria berhasil dicabut dari sistem!"];
    }
    header("Location: detail.php"); exit;
}

include 'engine_wp.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail & Manajemen Kriteria</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Nunito', sans-serif; }
        .modal-scroll { max-height: 85vh; overflow-y: auto; }
    </style>
</head>
<body class="bg-[#FDF8F5] text-[#1A202C] p-4 md:p-8">
    <div class="max-w-6xl mx-auto">
        <?php include 'navbar.php'; ?>

        <div class="flex flex-wrap gap-2 mb-6">
            <a href="export_excel.php" class="bg-[#8BD3DD] text-[#1A202C] border-2 border-[#1A202C] font-black px-4 py-2.5 rounded-xl text-sm shadow-[3px_3px_0px_0px_#1A202C] hover:translate-y-0.5 hover:shadow-[1px_1px_0px_0px_#1A202C] transition-all flex items-center gap-2">📗 Ekspor Excel Lengkap</a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div class="bg-white border-4 border-[#1A202C] rounded-3xl p-6 shadow-[8px_8px_0px_0px_#1A202C]">
                <h2 class="text-xl font-black mb-4">📋 Daftar Alternatif</h2>
                <div class="overflow-x-auto rounded-xl border-2 border-[#1A202C] max-h-96">
                    <table class="w-full text-center border-collapse text-sm font-bold">
                        <thead class="sticky top-0 bg-[#F7D7B3] border-b-2 border-[#1A202C] font-black">
                            <tr><td class="p-3 border-r border-[#1A202C]">Alternatif</td><td class="p-3">Kode</td></tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php $idx = 1; foreach($alternatif as $alt): ?>
                            <tr class="hover:bg-gray-50"><td class="p-3 border-r border-gray-200 text-left"><?php echo $alt['nama_dosen']; ?></td><td class="p-3">A<?php echo $idx; ?></td></tr>
                            <?php $idx++; endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="bg-white border-4 border-[#1A202C] rounded-3xl p-6 shadow-[8px_8px_0px_0px_#1A202C]">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-black">🎯 Manajemen Kriteria</h2>
                    <button onclick="bukaModal('modalTambah')" class="bg-[#FAAE2B] text-black border-2 border-[#1A202C] font-black px-3 py-1.5 rounded-lg text-xs shadow-[2px_2px_0px_0px_#1A202C] hover:translate-y-0.5 hover:shadow-[1px_1px_0px_0px_#1A202C] transition-all">➕ Tambah</button>
                </div>
                <div class="overflow-x-auto rounded-xl border-2 border-[#1A202C] max-h-96">
                    <table class="w-full text-center border-collapse text-xs md:text-sm font-bold">
                        <thead class="sticky top-0 bg-[#F7D7B3] border-b-2 border-[#1A202C] font-black">
                            <tr><td class="p-2 border-r border-[#1A202C]">Kriteria</td><td class="p-2 border-r border-[#1A202C]">Bbt</td><td class="p-2 border-r border-[#1A202C]">Atribut</td><td class="p-2">Aksi</td></tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php foreach($kriteria as $k): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="p-2 border-r border-gray-200 text-left"><?php echo strtoupper($k['kode']); ?> - <?php echo $k['nama_kriteria']; ?></td>
                                <td class="p-2 border-r border-gray-200"><?php echo $k['bobot']; ?></td>
                                <td class="p-2 border-r border-gray-200"><span class="px-2 py-0.5 rounded-full text-xs <?php echo ($k['atribut']=='Benefit') ? 'bg-[#8BD3DD]' : 'bg-[#FE98A3]'; ?>"><?php echo $k['atribut']; ?></span></td>
                                <td class="p-2 flex justify-center gap-1">
                                    <button type="button" data-json="<?php echo htmlspecialchars($k['sub_kriteria'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" onclick="editKriteria(<?php echo $k['id']; ?>, '<?php echo addslashes($k['nama_kriteria']); ?>', <?php echo $k['bobot']; ?>, '<?php echo $k['atribut']; ?>', this)" class="bg-white border border-[#1A202C] px-2 py-1 rounded-md hover:bg-gray-100">✏️</button>
                                    <button type="button" onclick="bukaModalHapusKriteria(<?php echo $k['id']; ?>, '<?php echo addslashes($k['nama_kriteria']); ?>')" class="bg-[#E53E3E] text-white border border-[#1A202C] px-2 py-1 rounded-md hover:bg-red-700 cursor-pointer">🗑️</button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="bg-white border-4 border-[#1A202C] rounded-3xl p-6 shadow-[8px_8px_0px_0px_#1A202C] mb-8">
            <h2 class="text-xl font-black mb-4">🧮 1. Bobot Kriteria Ternormalisasi (Pangkat)</h2>
            <div class="overflow-x-auto rounded-xl border-2 border-[#1A202C]">
                <table class="w-full text-center border-collapse">
                    <tr class="bg-[#8BD3DD] border-b-2 border-[#1A202C] font-black text-sm">
                        <?php foreach($kriteria as $k): ?><td class="p-3 border-r-2 border-[#1A202C] whitespace-nowrap"><?php echo strtoupper($k['kode']); ?></td><?php endforeach; ?>
                    </tr>
                    <tr class="font-bold text-sm bg-white">
                        <?php foreach($kriteria as $k): ?><td class="p-3 border-r border-gray-200"><?php echo number_format($k['w'], 6); ?></td><?php endforeach; ?>
                    </tr>
                </table>
            </div>
        </div>

        <div class="bg-white border-4 border-[#1A202C] rounded-3xl p-6 shadow-[8px_8px_0px_0px_#1A202C] mb-8">
            <h2 class="text-xl font-black mb-4">📋 2. Matriks Keputusan (X) Dinamis</h2>
            <div class="overflow-x-auto rounded-xl border-2 border-[#1A202C]">
                <table class="w-full text-center border-collapse">
                    <thead class="bg-[#F7D7B3] border-b-2 border-[#1A202C] font-black text-sm">
                        <tr><td class="p-3 border-r-2 border-[#1A202C]">Alternatif</td><?php foreach($kriteria as $k): ?><td class="p-3 border-r border-[#1A202C]"><?php echo strtoupper($k['kode']); ?></td><?php endforeach; ?></tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 font-bold text-sm">
                        <?php foreach($alternatif as $alt): ?>
                        <tr class="hover:bg-gray-50"><td class="p-3 border-r-2 border-[#1A202C] text-left whitespace-nowrap"><?php echo $alt['nama_dosen']; ?></td><?php foreach($kriteria as $k): $kode_k = strtolower($k['kode']); ?><td class="p-3 border-r border-gray-200"><?php echo isset($alt[$kode_k]) ? $alt[$kode_k] : 1; ?></td><?php endforeach; ?></tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div class="bg-white border-4 border-[#1A202C] rounded-3xl p-6 shadow-[8px_8px_0px_0px_#1A202C]">
                <h2 class="text-xl font-black mb-4">📐 3. Nilai Vektor S</h2>
                <div class="overflow-x-auto rounded-xl border-2 border-[#1A202C]">
                    <table class="w-full text-left border-collapse text-sm font-bold">
                        <thead class="bg-[#8BD3DD] border-b-2 border-[#1A202C] font-black">
                            <tr><td class="p-3 border-r-2 border-[#1A202C]">Kode Alternatif</td><td class="p-3">Nilai S</td></tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 font-mono">
                            <?php $idx=1; foreach($alternatif as $alt): ?><tr><td class="p-3 border-r-2 border-[#1A202C] font-sans">A<?php echo $idx++; ?></td><td class="p-3 text-gray-700"><?php echo number_format($alt['nilai_s'], 6); ?></td></tr><?php endforeach; ?>
                        </tbody>
                        <tfoot class="bg-gray-50 border-t-2 border-[#1A202C] font-black font-sans">
                            <tr><td class="p-3 border-r-2 border-[#1A202C] text-right">TOTAL (ΣS) :</td><td class="p-3"><?php echo number_format($total_s, 6); ?></td></tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <div class="bg-white border-4 border-[#1A202C] rounded-3xl p-6 shadow-[8px_8px_0px_0px_#1A202C]">
                <h2 class="text-xl font-black mb-4">📊 4. Nilai Vektor V</h2>
                <div class="overflow-x-auto rounded-xl border-2 border-[#1A202C]">
                    <table class="w-full text-left border-collapse text-sm font-bold">
                        <thead class="bg-[#FFBDC4] border-b-2 border-[#1A202C] font-black">
                            <tr><td class="p-3 border-r-2 border-[#1A202C]">Kode Alternatif</td><td class="p-3">Nilai V</td></tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 font-mono">
                            <?php $idx=1; foreach($alternatif as $alt): ?><tr><td class="p-3 border-r-2 border-[#1A202C] font-sans">A<?php echo $idx++; ?></td><td class="p-3 text-[#DD6B20] text-base font-black"><?php echo number_format($alt['nilai_v'], 6); ?></td></tr><?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div id="modalTambah" class="fixed inset-0 bg-black/60 z-50 hidden flex items-center justify-center backdrop-blur-sm">
        <div class="bg-white border-4 border-[#1A202C] rounded-3xl p-6 shadow-xl max-w-sm w-full mx-4 modal-scroll">
            <h3 class="text-xl font-black mb-4">➕ Tambah Kriteria Baru</h3>
            <form action="" method="POST" class="space-y-4">
                <div><label class="block font-black text-xs mb-1">Nama Kriteria</label><input type="text" name="nama_kriteria" required class="w-full border-2 border-[#1A202C] rounded-xl p-2 font-bold focus:ring-[#8BD3DD] text-sm"></div>
                <div class="flex gap-4">
                    <div class="w-1/2"><label class="block font-black text-xs mb-1">Bobot</label><input type="number" name="bobot" min="1" max="10" required class="w-full border-2 border-[#1A202C] rounded-xl p-2 font-bold focus:ring-[#8BD3DD] text-sm"></div>
                    <div class="w-1/2"><label class="block font-black text-xs mb-1">Atribut</label><select name="atribut" class="w-full border-2 border-[#1A202C] rounded-xl p-2 font-bold focus:ring-[#8BD3DD] text-sm"><option value="Benefit">Benefit (+)</option><option value="Cost">Cost (-)</option></select></div>
                </div>
                
                <div class="mt-4 bg-gray-50 p-3 rounded-xl border-2 border-gray-200">
                    <label class="block font-black text-xs mb-3 text-center text-[#1A202C]">Konfigurasi Sub Kriteria (Keterangan & Nilai)</label>
                    <?php for($i=1; $i<=5; $i++): $val_def = ($i*2)-1; ?>
                    <div class="flex gap-2 mb-2">
                        <input type="number" name="nilai[]" value="<?php echo $val_def; ?>" required class="w-1/4 border-2 border-[#1A202C] rounded-lg p-1.5 font-bold text-center text-sm focus:ring-[#8BD3DD]">
                        <input type="text" name="ket[]" placeholder="Keterangan Tingkat <?php echo $i; ?>" required class="w-3/4 border-2 border-[#1A202C] rounded-lg p-1.5 font-bold text-sm focus:ring-[#8BD3DD]">
                    </div>
                    <?php endfor; ?>
                </div>

                <div class="flex gap-2 pt-2">
                    <button type="button" onclick="tutupModal('modalTambah')" class="w-1/2 bg-gray-200 font-black p-3 rounded-xl border-2 border-[#1A202C] text-sm">Batal</button>
                    <button type="submit" name="tambah_kriteria" class="w-1/2 bg-[#FAAE2B] font-black p-3 rounded-xl border-2 border-[#1A202C] text-sm">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <div id="modalEdit" class="fixed inset-0 bg-black/60 z-50 hidden flex items-center justify-center backdrop-blur-sm">
        <div class="bg-white border-4 border-[#1A202C] rounded-3xl p-6 shadow-xl max-w-sm w-full mx-4 modal-scroll">
            <h3 class="text-xl font-black mb-4">✏️ Edit Kriteria</h3>
            <form action="" method="POST" class="space-y-4">
                <input type="hidden" name="id_kriteria" id="edit_id">
                <div><label class="block font-black text-xs mb-1">Nama Kriteria</label><input type="text" name="nama_kriteria" id="edit_nama" required class="w-full border-2 border-[#1A202C] rounded-xl p-2 font-bold focus:ring-[#8BD3DD] text-sm"></div>
                <div class="flex gap-4">
                    <div class="w-1/2"><label class="block font-black text-xs mb-1">Bobot (1-10)</label><input type="number" name="bobot" id="edit_bobot" required class="w-full border-2 border-[#1A202C] rounded-xl p-2 font-bold focus:ring-[#8BD3DD] text-sm"></div>
                    <div class="w-1/2"><label class="block font-black text-xs mb-1">Atribut</label><select name="atribut" id="edit_atribut" class="w-full border-2 border-[#1A202C] rounded-xl p-2 font-bold focus:ring-[#8BD3DD] text-sm"><option value="Benefit">Benefit (+)</option><option value="Cost">Cost (-)</option></select></div>
                </div>

                <div class="mt-4 bg-gray-50 p-3 rounded-xl border-2 border-gray-200">
                    <label class="block font-black text-xs mb-3 text-center text-[#1A202C]">Konfigurasi Sub Kriteria (Keterangan & Nilai)</label>
                    <?php for($i=0; $i<5; $i++): ?>
                    <div class="flex gap-2 mb-2">
                        <input type="number" name="nilai[]" id="edit_nilai_<?php echo $i; ?>" required class="w-1/4 border-2 border-[#1A202C] rounded-lg p-1.5 font-bold text-center text-sm focus:ring-[#8BD3DD]">
                        <input type="text" name="ket[]" id="edit_ket_<?php echo $i; ?>" required class="w-3/4 border-2 border-[#1A202C] rounded-lg p-1.5 font-bold text-sm focus:ring-[#8BD3DD]">
                    </div>
                    <?php endfor; ?>
                </div>

                <div class="flex gap-2 pt-2">
                    <button type="button" onclick="tutupModal('modalEdit')" class="w-1/2 bg-gray-200 font-black p-3 rounded-xl border-2 border-[#1A202C] text-sm">Batal</button>
                    <button type="submit" name="edit_kriteria" class="w-1/2 bg-[#8BD3DD] font-black p-3 rounded-xl border-2 border-[#1A202C] text-sm">Update</button>
                </div>
            </form>
        </div>
    </div>

    <div id="modalHapusKriteria" class="fixed inset-0 bg-black/60 z-50 hidden flex items-center justify-center backdrop-blur-sm transition-opacity">
        <div class="bg-white border-4 border-[#1A202C] rounded-3xl p-6 md:p-8 shadow-[8px_8px_0px_0px_#1A202C] max-w-sm w-full mx-4 transform scale-95 transition-transform duration-300" id="modalContentHapusKriteria">
            <div class="text-center">
                <div class="text-6xl mb-4">💣</div>
                <h3 class="text-2xl font-black mb-2 text-[#1A202C]">Hapus Kriteria?</h3>
                <p class="text-sm font-bold text-gray-600 mb-6">Yakin ingin menghapus <span id="namaKriteriaHapus" class="text-[#E53E3E] font-black underline"></span>? Seluruh data dosen untuk kriteria ini akan musnah permanen!</p>
                <div class="flex gap-3">
                    <button type="button" onclick="tutupModalHapusKriteria()" class="w-1/2 bg-white text-[#1A202C] border-2 border-[#1A202C] font-black py-3 rounded-xl shadow-[3px_3px_0px_0px_#1A202C] hover:translate-y-0.5 hover:shadow-[1px_1px_0px_0px_#1A202C] transition-all cursor-pointer">Batal</button>
                    <a id="linkHapusKriteria" href="#" class="w-1/2 bg-[#E53E3E] text-white border-2 border-[#1A202C] font-black py-3 rounded-xl shadow-[3px_3px_0px_0px_#1A202C] hover:translate-y-0.5 hover:shadow-[1px_1px_0px_0px_#1A202C] transition-all flex items-center justify-center">Ya, Hapus!</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        function bukaModal(id) { document.getElementById(id).classList.remove('hidden'); }
        function tutupModal(id) { document.getElementById(id).classList.add('hidden'); }
        
        function editKriteria(id, nama, bobot, atribut, btnElement) {
            document.getElementById('edit_id').value = id;
            document.getElementById('edit_nama').value = nama;
            document.getElementById('edit_bobot').value = bobot;
            document.getElementById('edit_atribut').value = atribut;

            let sub_str = btnElement.getAttribute('data-json');
            let subs = {};
            try { subs = JSON.parse(sub_str); } catch(e) {}
            
            if(!subs || Object.keys(subs).length === 0) {
                subs = {1: 'Sangat Kurang', 3: 'Kurang', 5: 'Cukup', 7: 'Baik', 9: 'Sangat Baik'};
            }
            
            let keys = Object.keys(subs);
            let vals = Object.values(subs);

            for(let i=0; i<5; i++) {
                document.getElementById('edit_nilai_'+i).value = keys[i] || '';
                document.getElementById('edit_ket_'+i).value = vals[i] || '';
            }
            bukaModal('modalEdit');
        }

        function bukaModalHapusKriteria(id, nama) {
            document.getElementById('namaKriteriaHapus').innerText = nama;
            document.getElementById('linkHapusKriteria').href = 'detail.php?hapus_kriteria=' + id;
            document.getElementById('modalHapusKriteria').classList.remove('hidden');
            setTimeout(() => { document.getElementById('modalContentHapusKriteria').classList.remove('scale-95'); document.getElementById('modalContentHapusKriteria').classList.add('scale-100'); }, 10);
        }
        function tutupModalHapusKriteria() {
            document.getElementById('modalContentHapusKriteria').classList.remove('scale-100');
            document.getElementById('modalContentHapusKriteria').classList.add('scale-95');
            setTimeout(() => { document.getElementById('modalHapusKriteria').classList.add('hidden'); }, 200);
        }
    </script>
</body>
</html>
