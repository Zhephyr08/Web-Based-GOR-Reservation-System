<?php
include "../service/database.php";
session_start();

if(isset($_SESSION["is_login"]) == false){
  header("location: ../USERS/home.php");
}

unset($_SESSION['tanggal']);
unset($_SESSION['bulan']);
unset($_SESSION['tahun']);

if(isset($_POST['logout'])) {
    $_SESSION["is_login"] = false;
    $_SESSION["is_logout"] = true;
    header("location: ../USERS/home.php");
}

$sql = "SELECT * FROM keuangan WHERE 1=1";

if (isset($_POST['FilterTanggal']) || isset($_POST['FilterBulan'])) {
    if (isset($_POST['FilterTanggal'])) {
        if (!empty($_POST['tanggal'])) {
            $_SESSION['tanggal'] = $_POST['tanggal'];
            unset($_SESSION['bulan']);
            unset($_SESSION['tahun']);
        } else {
            $_SESSION['gagal_filterTanggal'] = true;
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        }
    }

    if (isset($_POST['FilterBulan'])) {
        if (!empty($_POST['bulan']) && !empty($_POST['tahun'])) {
            $_SESSION['bulan'] = $_POST['bulan'];
            $_SESSION['tahun'] = $_POST['tahun'];
            unset($_SESSION['tanggal']);
        } else {
            $_SESSION['gagal_filterBulan'] = true;
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        }
    }
} elseif (isset($_POST['Reset'])) {
    unset($_SESSION['tanggal']);
    unset($_SESSION['bulan']);
    unset($_SESSION['tahun']);
}

if (isset($_SESSION['tanggal'])) {
    $selectedDate = date('Y-m-d', strtotime($_SESSION['tanggal']));
    $sql .= " AND tanggal = '$selectedDate'";
}

if (isset($_SESSION['bulan']) && isset($_SESSION['tahun'])) {
    $selectedMonth = $_SESSION['bulan'];
    $selectedYear = $_SESSION['tahun'];
    $sql .= " AND MONTH(tanggal) = '$selectedMonth' AND YEAR(tanggal) = '$selectedYear'";
}

$sql .= " ORDER BY id_keuangan ASC";
$result = $db->query($sql);

$currentDate = date('j F Y');
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="Dasboard_Keuangan.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <title>DASBOARD Keuangan</title>
</head>

<body>
    <nav class="middlebar">
        <hearder>
            <div class="text header-text">
                <span class="name"><?= $_SESSION["username"] ?></span>
                <span class="date"><?= $currentDate ?></span>
            </div>
        </hearder>
    </nav>

    <nav class="sidebar">

        <hearder>
            <div class="text header-text">
                <a href="Dasboard.php">
                    <span class="name">HALAMAN UTAMA</span>
                </a>
            </div>
        </hearder>

        <div class="menu-bar">
            <div class="menu">

                <li class="nav-link">
                    <a href="Dasboard_Keuangan.php">
                        <span class="text nav-text">KEUANGAN</span>
                    </a>
                </li>
                <li class="nav-link">
                    <a href="Dasboard_Reservasi.php">
                        <span class="text nav-text">RESERVASI</span>
                    </a>
                </li>
                <li class="nav-link">
                    <a href="Dasboard_Ulasan.php">
                        <span class="text nav-text">ULASAN</span>
                    </a>
                </li>

                <div class="text header-text">
                    <form action="dasboard.php" method="POST">
                        <button class="name_1" type="submit" name="logout">KELUAR</button>
                    </form>
                </div>

            </div>
        </div>

    </nav>

    <span class="nama">KEUANGAN</span>

    <div class="middlebox_1">
        <div class="table-container">
            <form class="filter" method="POST" action="">
                    <label for="tanggal">Filter berdasarkan tanggal :</label>
                    <input class="filter_date" type="date" id="tanggal" name="tanggal">
                    <input class="tombol_1" type="submit" name="FilterTanggal" value="Filter">

                    <label for="bulan">Filter berdasarkan bulan :</label>
                    <select id="bulan" name="bulan">
                        <option value="">Pilih Bulan</option>
                        <?php for ($m=1; $m<=12; $m++) { 
                            $month = date('F', mktime(0, 0, 0, $m, 1, date('Y')));
                            echo "<option value='$m'>$month</option>";
                        } ?>
                    </select>
                    <label for="tahun">Tahun :</label>
                    <select id="tahun" name="tahun">
                        <option value="">Pilih Tahun</option>
                        <?php 
                        $currentYear = date('Y');
                        for ($y=$currentYear; $y>=2000; $y--) { 
                            echo "<option value='$y'>$y</option>";
                        } ?>
                    </select>

                    <input class="tombol_2" type="submit" name="FilterBulan" value="Filter">
                    <input class="tombol_3" type="submit" name="Reset" value="Reset">
            </form>

            <form class="pdf" method="POST" action="pdf.php" target="_blank">
                <input type="hidden" name="tanggal" id="hiddenTanggal">
                <input type="hidden" name="bulan" id="hiddenBulan">
                <input type="hidden" name="tahun" id="hiddenTahun">
                <input type="submit" class="exportpdf" value="Buat PDF">
            </form>


            <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Nomor</th>
                    <th>Nama</th>
                    <th>Tanggal</th>
                    <th>Lapangan</th>
                    <th>Durasi</th>
                    <th>Status</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    $total_pemasukan = 0;
                    $count = 1;
                    while ($row = $result->fetch_assoc()) {
                        $tanggal_format = date('j F Y', strtotime($row['tanggal']))
                        ?>
                        <tr>
                            <td><?= $count ?></td>
                            <td><?= $row['nama_tim'] ?></td>
                            <td><?= $tanggal_format ?></td>
                            <td><?= ucfirst($row['lapangan']) ?></td>
                            <td><?= $row['durasi'] ?> Jam</td>
                            <td><?= ucfirst($row['status']) ?></td>
                            <td>Rp. <?= number_format($row['total'], 0, ',', '.') ?></td>
                        </tr>
                        <?php
                        $total_pemasukan = $total_pemasukan + $row['total'];
                        $count++;
                    }
                } else {
                    $total_pemasukan = 0;
                    echo "<tr><td colspan='7'>Belum Ada Data.</td></tr>";
                }
                ?>
                <tr>
                    <td colspan="6" style="text-align: right;"><strong>Total Pemasukan : </strong></td>
                    <td><strong>Rp. <?= number_format($total_pemasukan, 0, ',', '.') ?></strong></td>
                </tr>
            </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        <?php if (isset($_SESSION['gagal_filterTanggal']) && $_SESSION['gagal_filterTanggal'] == true) { ?>
            Swal.fire({
                title: "Filter Gagal!",
                text: "Silahkan pilih tanggal terlebih dahulu!",
                icon: "error"
            });
            <?php unset($_SESSION['gagal_filterTanggal']);
        } ?>
        <?php if (isset($_SESSION['gagal_filterBulan']) && $_SESSION['gagal_filterBulan'] == true) { ?>
            Swal.fire({
                title: "Filter Gagal!",
                text: "Silahkan pilih bulan dan tahun terlebih dahulu!",
                icon: "error"
            });
            <?php unset($_SESSION['gagal_filterBulan']);
        } ?>
    </script>
</body>

</html>
