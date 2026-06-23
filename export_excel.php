<?php
include 'koneksi.php';
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

header("Content-type: application/vnd-ms-excel; charset=UTF-8");
header("Content-Disposition: attachment; filename=SPK Penentuan Dosen Terbaik.xls");
header("Pragma: no-cache");
header("Expires: 0");

include 'engine_wp.php';

$b = 'style="border: 1pt solid black;"';
$bc = 'style="border: 1pt solid black; text-align: center;"';
$bh = 'style="border: 1pt solid black; background-color: #f2f2f2; font-weight: bold; text-align: center;"';
$bl = 'style="border: 1pt solid black; background-color: #f2f2f2; font-weight: bold;"';
?>
<html xmlns:x="urn:schemas-microsoft-com:office:excel">
<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head>
<body>
<table style="border-collapse: collapse;">
    <tr>
        <td></td><td></td>
        <td colspan="10" style="font-weight: bold; font-size: 14pt;">Perhitungan Penentuan Dosen Terbaik dengan Metode WP</td>
    </tr>
    <tr><td></td></tr>
    
    <tr>
        <td colspan="2" style="font-weight: bold;">Tabel Alternatif</td>
    </tr>
    <tr>
        <td <?php echo $bh; ?>>alternatif</td>
        <td <?php echo $bh; ?>>Kode</td>
    </tr>
    <?php 
    $idx = 1;
    foreach ($alternatif as $alt) { 
        echo "<tr><td $b>" . $alt['nama_dosen'] . "</td><td $bc>A" . $idx . "</td></tr>";
        $idx++;
    } 
    ?>
    <tr><td></td></tr>

    <tr>
        <td></td>
        <td colspan="4" style="font-weight: bold;">Tabel Kriteria</td>
    </tr>
    <tr>
        <td <?php echo $bh; ?>>No</td>
        <td <?php echo $bh; ?>>Kriteria</td>
        <td <?php echo $bh; ?>>Bobot</td>
        <td <?php echo $bh; ?>>Atribut</td>
        <td <?php echo $bh; ?>>Kode</td>
    </tr>
    <?php
    $no = 1;
    foreach($kriteria as $k){
        echo "<tr>";
        echo "<td $bc>".$no."</td>";
        echo "<td $b>".$k['nama_kriteria']."</td>";
        echo "<td $bc>".$k['bobot']."</td>";
        echo "<td $b>".$k['atribut']."</td>";
        echo "<td $bc>".strtoupper($k['kode'])."</td>";
        echo "</tr>";
        $no++;
    }
    ?>
    <tr><td <?php echo $b; ?>></td><td <?php echo $bl; ?>>Jumlah</td><td <?php echo $bh; ?>><?php echo $total_bobot; ?></td><td <?php echo $b; ?>></td><td <?php echo $b; ?>></td></tr>
    <tr><td></td></tr>

    <tr>
        <td <?php echo $bh; ?>>2</td>
        <td <?php echo $bh; ?>>Bobot/ kriteria</td>
        <?php foreach($kriteria as $k) { echo "<td $bh>".strtoupper($k['kode'])."</td>"; } ?>
        <td <?php echo $bh; ?>>Σ wj</td>
    </tr>
    <tr>
        <td <?php echo $b; ?>></td>
        <td <?php echo $b; ?>>bobot kepentingan</td>
        <?php 
        $sum_w = 0;
        foreach($kriteria as $k) { 
            echo "<td $bc>".$k['w']."</td>"; 
            $sum_w += $k['w'];
        } 
        echo "<td $bc>".$sum_w."</td>";
        ?>
    </tr>
    <tr><td></td></tr>

    <tr>
        <td <?php echo $bh; ?>>3</td>
        <td <?php echo $bh; ?>>alternatif / kriteria</td>
        <?php foreach($kriteria as $k) { echo "<td $bh>".strtoupper($k['kode'])."</td>"; } ?>
    </tr>
    <?php 
    foreach ($alternatif as $alt) {
        echo "<tr>";
        echo "<td $b></td><td $b>" . $alt['nama_dosen'] . "</td>";
        foreach($kriteria as $k) {
            $kode_k = strtolower($k['kode']);
            $val = isset($alt[$kode_k]) ? $alt[$kode_k] : 1;
            echo "<td $bc>" . $val . "</td>";
        }
        echo "</tr>";
    }
    ?>
    <tr>
        <td <?php echo $b; ?>></td><td <?php echo $bl; ?>>Pangkat</td>
        <?php foreach($kriteria as $k) { echo "<td $bc>".$k['w']."</td>"; } ?>
    </tr>
    <tr><td></td></tr>

    <tr>
        <td <?php echo $bh; ?>>4</td>
        <td <?php echo $bh; ?>>Alternatif</td>
        <td <?php echo $bh; ?>>S</td>
    </tr>
    <?php 
    $idx = 1;
    foreach ($alternatif as $alt) {
        echo "<tr><td $b></td><td $bc>A" . $idx . "</td><td $b>" . $alt['nilai_s'] . "</td></tr>";
        $idx++;
    }
    ?>
    <tr>
        <td <?php echo $b; ?>></td><td <?php echo $bh; ?>>JUMLAH</td><td <?php echo $bl; ?>><?php echo $total_s; ?></td>
    </tr>
    <tr><td></td></tr>

    <tr>
        <td <?php echo $bh; ?>>5</td>
        <td <?php echo $bh; ?>>Alternatif</td>
        <td <?php echo $bh; ?>>V</td>
    </tr>
    <?php 
    $idx = 1;
    foreach ($alternatif as $alt) {
        echo "<tr><td $b></td><td $bc>A" . $idx . "</td><td $b>" . $alt['nilai_v'] . "</td></tr>";
        $idx++;
    }
    ?>
    <tr><td></td></tr>

    <tr>
        <td <?php echo $bh; ?>>6</td>
        <td <?php echo $bh; ?>>Alternatif</td>
        <td <?php echo $bh; ?>>V</td>
        <td <?php echo $bh; ?>>Rangking</td>
    </tr>
    <?php 
    foreach ($sorted_alt as $alt) {
        echo "<tr>";
        echo "<td $b></td><td $b>" . $alt['nama_dosen'] . "</td>";
        echo "<td $b>" . $alt['nilai_v'] . "</td>";
        echo "<td $bc>" . $ranks[$alt['id']] . "</td>";
        echo "</tr>";
    }
    ?>
</table>
</body>
</html>