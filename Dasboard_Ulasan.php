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

$sql = "SELECT * FROM ulasan ORDER BY id_ulasan DESC";
$result = $db->query($sql);

$currentDate = date('j F Y');

?>

<!doctype html>
<html lang="en">

  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="Dasboard_Ulasan_3.css">

    <!-- Icons CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <title>DASBOARD Ulasan</title>
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
  
  <span class="nama">ULASAN</span>

  <div class="middlebox_1">
      <?php
      if ($result->num_rows > 0) {
          while($row = $result->fetch_assoc()) {
            $tanggal = date('j F Y, h:i:s', strtotime($row['tanggal']))
      ?>
              <div class="tampil">
                  <div class="tampil-container">
                    <div class="tampil-item">
                      <div class="profil">
                          <i class="fa-solid fa-user"></i>
                          <h4><?php echo ($row['nama'])?></h4>
                      </div>
                      <p><?php echo ($row['komentar'])?></p>
                      <div class="tanggal"> <?php echo $tanggal ?> </div>
                      <div class="rating">
                        <?php
                        $rating = intval($row['rating']);
                        for ($i = 0; $i < $rating; $i++){
                          ?>
                          <i class="fa-solid fa-star"></i>
                          <?php
                        } 
                        ?>
                          <span class="rating"><?php echo ($row['rating'])?></span>
                      </div>
                    </div>
                  </div>
              </div>
      <?php
          }
      } else {
          echo "Belum ada ulasan.";
      }
      ?>
  </div>




    <!-- Optional JavaScript; choose one of the two! -->

    <!-- Option 1: Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

    <!-- Option 2: Separate Popper and Bootstrap JS -->
    <!--
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
    -->
  </body>
</html>