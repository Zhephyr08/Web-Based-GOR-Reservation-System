<?php

include "../service/database.php";
session_start();

if(isset($_SESSION["is_login"]) == false){
  header("location: ../USERS/home.php");
}

if(isset($_POST['logout'])) {
  $_SESSION["is_login"] = false;
  $_SESSION["is_logout"] = true;
  header("location: ../USERS/home.php");
}

$currentDate = date('j F Y');

$sql = "SELECT * FROM jadwal WHERE NOT pembayaran = 'lunas' ORDER BY id_jadwal ASC";
$result = $db->query($sql);
if($result->num_rows > 0) {
  $ada_reservasi = true;
} else  {
  $ada_reservasi = false;
}
?>

<!doctype html>
<html lang="en">

  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="Dasboard_1.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Irish+Grover&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11">
    <title>DASBOARD</title>
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
            <a href="">
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

  <div class="middlebox_1">
  
    <div class="box">
          <span class="nama">DASBOR ADMIN</span>
          <section id="jadwal">
            <div class="h3">
                <p>Jam Operasional</p>
            </div>
            <div class="jadwalBuka">
                <table>
                    <tr>
                        <td>Minggu</td>
                        <td>07.00 - 23.00</td>
                    </tr>
                    <tr>
                        <td>Senin</td>
                        <td>07.00 - 23.00</td>
                    </tr>
                    <tr>
                        <td>Selasa</td>
                        <td>07.00 - 23.00</td>
                    </tr>
                    <tr>
                        <td>Rabu</td>
                        <td>07.00 - 23.00</td>
                    </tr>
                    <tr>
                        <td>Kamis</td>
                        <td>07.00 - 23.00</td>
                    </tr>
                    <tr>
                        <td>Jumat</td>
                        <td>07.00 - 23.00</td>
                    </tr>
                    <tr>
                        <td>Sabtu</td>
                        <td>07.00 - 23.00</td>
                    </tr>
                </table>
            </div>
        </section>
    </div>

 </div>

 <div class="middlebot">
      <div class="icon">
         <i class="fa-solid fa-note-sticky"></i>
      </div>
      <a href="Dasboard_Reservasi.php">
      <div class="button">
            <span class="name">Klik Disini</span>
      </div>
      </a>  
    <hearder>
            <div class="text header-text">
              <span class="core4">RESERVASI</span>
              <?php if ($ada_reservasi == false) { ?>
                <span class="sub4">Tidak ada permintaan reservasi yang perlu di setujui</span>
              <?php } else { ?>
                <span class="sub4">Terdapat beberapa permintaan reservasi yang perlu di setujui</span>
              <?php } ?>
            </div>
    </hearder>
 </div>

 <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  <?php if (isset($_SESSION['first_login']) && $_SESSION['first_login'] == true) { ?>
    Swal.fire({
      title: "Anda Berhasil Masuk!",
      text: "Selamat Datang di Dasbor Admin!",
      icon: "success"
    });
    <?php $_SESSION['first_login'] = false; ?>
  <?php } ?>
</script>

<script>
          document.addEventListener('DOMContentLoaded', function () {
            let today = new Date().getDay();

            let tableRows = document.querySelectorAll('.jadwalBuka table tr');

            tableRows.forEach(function (row, index) {
                let day = row.cells[0].textContent.trim();

                if (day === getDayName(today)) {
                    row.classList.add('open');

                    let newCell = document.createElement('td');

                    newCell.textContent = 'BUKA';

                    newCell.classList.add('open');

                    row.appendChild(newCell);
                }
            });
        });
        function getDayName(dayIndex) {
            let days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            return days[dayIndex];
        }
</script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

  </body>
</html>