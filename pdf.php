<?php
include "../service/database.php";
require_once("./fpdf/fpdf.php");
session_start();

if(isset($_SESSION["is_login"]) == false){
    header("location: ../USERS/home.php");
}

if(isset($_POST['logout'])) {
    $_SESSION["is_login"] = false;
    $_SESSION["is_logout"] = true;
    header("location: ../USERS/home.php");
}

$tanggal = isset($_SESSION['tanggal']) ? $_SESSION['tanggal'] : '';
$bulan = isset($_SESSION['bulan']) ? $_SESSION['bulan'] : '';
$tahun = isset($_SESSION['tahun']) ? $_SESSION['tahun'] : '';

$sql = "SELECT * FROM keuangan WHERE 1=1";

if (!empty($tanggal)) {
    $selectedDate = date('Y-m-d', strtotime($tanggal));
    $sql .= " AND tanggal = '$selectedDate'";
}

if (!empty($bulan) && !empty($tahun)) {
    $sql .= " AND MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun'";
}

$sql .= " ORDER BY id_keuangan ASC";
$result = $db->query($sql);

// Membuat instance FPDF
$pdf = new FPDF('P', 'mm', 'A4');

// Menambahkan halaman baru
$pdf->AddPage();

// Mengatur font
$pdf->SetFont('Times', 'B', 16);

if (!empty($tanggal) || (!empty($bulan) && !empty($tahun))) {
    $pdf->Cell(0, 3, 'Laporan Keuangan GOR Kharisma', 0, 1, 'C');
} else {
    $pdf->Cell(0, 20, 'Laporan Keuangan GOR Kharisma', 0, 1, 'C');
}

// Mengatur font untuk header tabel
$pdf->SetFont('Times', 'B', 14);

if (!empty($tanggal)) {
    $selectedDate = date('j F Y', strtotime($tanggal));
    $pdf->Cell(0, 15, 'Tanggal ' . $selectedDate, 0, 1, 'C');
}

if (!empty($bulan) && !empty($tahun)) {
    $month = date('F', mktime(0, 0, 0, $bulan, 1, date('Y')));
    $pdf->Cell(0, 15, 'Bulan ' . $month . ', Tahun ' . $tahun, 0, 1, 'C');
}

$pdf->SetFont('Times', 'B', 12);

// Header tabel
$pdf->Cell(10, 10, 'No', 1, 0, 'C');
$pdf->Cell(45, 10, 'Nama', 1, 0, 'C');
$pdf->Cell(40, 10, 'Tanggal', 1, 0, 'C');
$pdf->Cell(25, 10, 'Lapangan', 1, 0, 'C');
$pdf->Cell(20, 10, 'Durasi', 1, 0, 'C');
$pdf->Cell(20, 10, 'Status', 1, 0, 'C');
$pdf->Cell(30, 10, 'Total', 1, 0, 'C');
$pdf->Ln();

// Mengatur font untuk isi tabel
$pdf->SetFont('Times', '', 12);

$total_pemasukan = 0;
$count = 1;
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $tanggal_format = date('j F Y', strtotime($row['tanggal']));
        $pdf->Cell(10, 10, $count, 1, 0, 'C');
        $pdf->Cell(45, 10, $row['nama_tim'], 1, 0, 'C');
        $pdf->Cell(40, 10, $tanggal_format, 1, 0, 'C');
        $pdf->Cell(25, 10, ucfirst($row['lapangan']), 1, 0, 'C');
        $pdf->Cell(20, 10, $row['durasi'] . ' Jam', 1, 0, 'C');
        $pdf->Cell(20, 10, ucfirst($row['status']), 1, 0, 'C');
        $pdf->Cell(30, 10, 'Rp. ' . number_format($row['total'], 0, ',', '.'), 1, 0, 'C');
        $pdf->Ln();
        $total_pemasukan += $row['total'];
        $count++;
    }
} else {
    $pdf->Cell(190, 10, 'Belum Ada Data.', 1, 1, 'C');
}

// Menambahkan total pemasukan
$pdf->SetFont('Times', 'B', 12);
$pdf->Cell(160, 10, 'Total Pemasukan : ', 1, 0, 'R');
$pdf->Cell(30, 10, 'Rp. ' . number_format($total_pemasukan, 0, ',', '.'), 1, 1, 'C');

// Output PDF ke browser
$pdf->Output('I', 'Laporan_Keuangan.pdf');
?>