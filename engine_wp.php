<?php
// 1. Tarik Data Kriteria Dinamis
$kriteria_query = mysqli_query($conn, "SELECT * FROM kriteria ORDER BY id ASC");
$kriteria = [];
$total_bobot = 0;
while($row = mysqli_fetch_assoc($kriteria_query)){
    $kriteria[] = $row;
    $total_bobot += $row['bobot'];
}

// 2. Normalisasi Bobot & Deteksi Otomatis Benefit/Cost
foreach($kriteria as $key => $k) {
    $w = $k['bobot'] / $total_bobot;
    // JIKA ATRIBUT COST, PANGKAT DIJADIKAN NEGATIF
    if($k['atribut'] == 'Cost') {
        $w = $w * -1; 
    }
    $kriteria[$key]['w'] = $w;
}

// 3. Kalkulasi Vektor S & V
$query_alt = mysqli_query($conn, "SELECT * FROM alternatif");
$alternatif = [];
$total_s = 0;

while($alt = mysqli_fetch_assoc($query_alt)) {
    $s = 1;
    // Looping dinamis menyesuaikan jumlah kriteria yang ada di tabel
    foreach($kriteria as $k) {
        $kode = strtolower($k['kode']);
        // Jika kriteria baru, default nilai 1 agar sistem tidak error (netral)
        $nilai = (isset($alt[$kode]) && $alt[$kode] > 0) ? $alt[$kode] : 1; 
        $s *= pow($nilai, $k['w']);
    }
    $alt['nilai_s'] = $s;
    $total_s += $s;
    $alternatif[] = $alt;
}

// 4. Perangkingan Akhir
if($total_s > 0) {
    foreach($alternatif as $key => $alt) {
        $alternatif[$key]['nilai_v'] = $alt['nilai_s'] / $total_s;
    }
}
$sorted_alt = $alternatif;
usort($sorted_alt, function($a, $b) {
    return $b['nilai_v'] <=> $a['nilai_v'];
});

$ranks = [];
$r = 1;
foreach ($sorted_alt as $sa) {
    $ranks[$sa['id']] = $r;
    $r++;
}
?>