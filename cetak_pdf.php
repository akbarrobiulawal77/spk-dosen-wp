<?php
include 'koneksi.php';
if (!isset($_SESSION['login'])) { header("Location: login.php"); exit; }

$bobot = [5, 5, 5, 4, 4, 5, 5, 5, 4, 3];
$total_bobot = array_sum($bobot);
$w = []; foreach ($bobot as $b) { $w[] = $b / $total_bobot; }
$query = "SELECT * FROM alternatif";
$result = mysqli_query($conn, $query);
$alternatif = []; $total_s = 0;
while ($row = mysqli_fetch_assoc($result)) {
    $s = pow($row['c1'], $w[0]) * pow($row['c2'], $w[1]) * pow($row['c3'], $w[2]) * pow($row['c4'], $w[3]) * pow($row['c5'], $w[4]) * pow($row['c6'], $w[5]) * pow($row['c7'], $w[6]) * pow($row['c8'], $w[7]) * pow($row['c9'], $w[8]) * pow($row['c10'], $w[9]);
    $row['nilai_s'] = $s; $total_s += $s; $alternatif[] = $row;
}
if ($total_s > 0) { foreach ($alternatif as $key => $alt) { $alternatif[$key]['nilai_v'] = $alt['nilai_s'] / $total_s; } }
usort($alternatif, function($a, $b) { return $b['nilai_v'] <=> $a['nilai_v']; });
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Laporan SPK</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 40px; color: #000; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .header h1 { margin: 0 0 5px 0; font-size: 24px; }
        .header p { margin: 0; font-size: 14px; color: #555; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 12px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; text-align: center; }
        .text-center { text-align: center; }
        @media print { @page { margin: 1.5cm; } body { padding: 0; } }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <h1>LAPORAN HASIL PERANGKINGAN DOSEN TERBAIK</h1>
        <p>Sistem Pendukung Keputusan - Metode Weighted Product (WP)</p>
    </div>
    <table>
        <thead>
            <tr>
                <th width="15%">Peringkat</th>
                <th width="50%">Nama Dosen Alternatif</th>
                <th width="35%">Nilai Akhir (V)</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $rank = 1;
            foreach ($alternatif as $alt) {
                echo "<tr>";
                echo "<td class='text-center'><strong>" . $rank . "</strong></td>";
                echo "<td>" . $alt['nama_dosen'] . "</td>";
                echo "<td class='text-center'>" . number_format($alt['nilai_v'], 6) . "</td>";
                echo "</tr>";
                $rank++;
            }
            ?>
        </tbody>
    </table>
    <p style="margin-top: 30px; text-align: right; font-size: 14px;">
        Dicetak pada: <strong><?php echo date('d-m-Y H:i:s'); ?></strong>
    </p>
</body>
</html>