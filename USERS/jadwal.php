<?php
include "../service/database.php";
session_start();

// setlocale (LC_TIME, 'INDONESIA');
$lapangan = '';
$nomor_lapangan = '';
$selectedDate = date('Y-m-d'); // Default tanggal adalah hari ini

if (isset($_POST['tanggal'])) {
    $selectedDate = $_POST['tanggal'];
    $selectedDate = date('Y-m-d', strtotime($selectedDate));
}

if (isset($_POST['Filter'])) {
  if (empty($_POST['tanggal'])){
    $selectedDate = date('Y-m-d');
    $_SESSION['submit_filter'] = true;
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
  }
  $sql = "SELECT * FROM jadwal WHERE tanggal = '$selectedDate' ORDER BY id_jadwal ASC";
} else {
  $sql = "SELECT * FROM jadwal ORDER BY id_jadwal ASC";
}

if(isset($_POST['Reset'])) {
  $selectedDate = date('Y-m-d');
  $sql = "SELECT * FROM jadwal ORDER BY id_jadwal ASC";
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['save'])){
  
  // Mengambil data dari POST
  $tanggal = isset($_POST['tanggal']) ? date('Y-m-d', strtotime($_POST['tanggal'])) : '';
  $nama_tim = isset($_POST['pemesan']) ? $_POST['pemesan'] : '';
  $jam_mulai = isset($_POST['jamMulai']) ? $_POST['jamMulai'] : '';
  $jam_selesai = isset($_POST['jamSelesai']) ? $_POST['jamSelesai'] : '';
  $durasi = isset($_POST['durasi']) ? intval($_POST['durasi']) : 0;
  $nomor_hp = isset($_POST['phone']) ? $_POST['phone'] : '';
  $lapangan = isset($_POST['lapangan']) ? $_POST['lapangan'] : '';
  $nomor_lapangan = isset($_POST['nomor_lapangan']) ? $_POST['nomor_lapangan'] : '';
  $email = isset($_POST['email']) ? $_POST['email'] : '';

  $hargaPagi = 0; $hargaMalam = 0;
  if ($lapangan === 'futsal') {
    $hargaPagi = 75000;
    $hargaMalam = 90000;
  } else if ($lapangan === 'badminton') {
    $hargaPagi = 30000;
    $hargaMalam = 40000;
  } else if ($lapangan === 'volly') {
    $hargaPagi = 50000;
    $hargaMalam = 70000;
  }

  list($jam_mulai_hour, $jam_mulai_minute) = explode(':', $jam_mulai);
  list($jam_selesai_hour, $jam_selesai_minute) = explode(':', $jam_selesai);
  $jam_mulai = intval($jam_mulai_hour) + intval($jam_mulai_minute) / 60;
  $jam_selesai = intval($jam_selesai_hour) + intval($jam_selesai_minute) / 60;
    
  $total_biaya = 0;
  if ($jam_mulai < 16 && $jam_selesai >= 16) {
    $durasiPagi = 16 - $jam_mulai;
    $durasiMalam = $durasi - $durasiPagi;
    $total_biaya = ($durasiPagi * $hargaPagi) + ($durasiMalam * $hargaMalam);
  } else if ($jam_mulai >= 16) {
    $total_biaya = $durasi * $hargaMalam;
  } else {
    $total_biaya = $durasi * $hargaPagi;
  }

  $sql_check_booking = "
        SELECT id_jadwal 
        FROM jadwal 
        WHERE 
            tanggal = '$tanggal' 
            AND lapangan = '$lapangan' 
            AND nomor_lapangan = '$nomor_lapangan'
            AND (
                (jam_mulai < '$jam_selesai' AND jam_selesai > '$jam_mulai')
            )
    ";
  $result_check_booking = $db->query($sql_check_booking);
  if ($result_check_booking->num_rows > 0) {
      $_SESSION['booking_gagal'] = true;
  } else {
      // Menyimpan data booking ke database
      $sql_insert = "INSERT INTO jadwal (tanggal, nama_tim, jam_mulai, jam_selesai, durasi, nomor_hp, nomor_lapangan, lapangan, email, harga, pembayaran) 
      VALUES ('$tanggal', '$nama_tim', '$jam_mulai', '$jam_selesai', '$durasi', '$nomor_hp', '$nomor_lapangan', '$lapangan', '$email', '$total_biaya', 'belum bayar')";
      if ($db->query($sql_insert) === true) {
        $_SESSION['booking_sukses'] = true;
      } else {
        // Jika terjadi kesalahan saat menyimpan data
        die("Error: " . $db->error);
    }
  }
  header("Location: " . $_SERVER['PHP_SELF']);
  exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>JADWAL & RESERVASI</title>
  <link rel="stylesheet" href= "jadwal4.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
    integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body>

  <header>
    <nav>
      <a class="beranda" href="home.php">
        <span>Gor</span>
        <span>Kharisma</span>
      </a>
      <ul>
                <a href="Jadwal.php">
                    <li>Jadwal dan Reservasi</li>
                </a>
                <a href="ulasan.php">
                    <li>Ulasan</li>
                </a>
                <a href="../ADMIN/login.php">
                    <li>Masuk</li>
                </a>
            </ul>
    </nav>
  </header>

  <main>
    <section id="utama">
      <div class="utama">
        <h1>JADWAL & RESERVASI LAPANGAN</h1>
      </div>
    </section>
    <section id="second">
      <div class="jadwal-container">
        <div class="atas">
          <h3>JADWAL & RESERVASI LAPANGAN</h3>

          <a href="aturan.php">Aturan Pemesanan</a>

            <select id="lapangan" name="lapangan" onchange="changeLayout()">
              <option value="futsal">Lapangan Futsal</option>
              <option value="badminton">Lapangan Bulutangkis</option>
              <option value="volly">Lapangan Volly</option>
            </select>

          <h4>*Pilih lapangan sesuai yang anda inginkan</h4>
        </div>

        <div class="bawah">
          <label for="tanggal">Filter jadwal berdasarkan tanggal :</label>

          <br>
          <form class="filter" method="POST" action="">
            <input class="filter_date" type="date" id="filtertanggal" name="tanggal">
            <input class="tombol_1" type="submit" name="Filter" value="Filter">
            <input class="tombol_2" type="submit" name="Reset" value="Reset">
          </form>

          <div class="tanggalTable">
          <?php
          // Atur pengaturan lokal ke Bahasa Indonesia
          setlocale(LC_TIME, 'id_ID.utf8');

          // Mengonversi string tanggal menjadi timestamp
          $timestamp = strtotime($selectedDate);

          // Membuat format tanggal dalam Bahasa Indonesia
          $tanggalTable = strftime('%A, %d %B %Y', $timestamp);
          ?>
          <span><?= $tanggalTable ?> </span>
      </div>


          <div class="t_container">
            <input class="tombol_3 booking-trigger" type="button" name="input" value="Reservasi Sekarang!">
                <a href="https://wa.me/6281245187954">
                  <div class="wa">
                    <i class="fa-brands fa-whatsapp"></i>
                    <span>WhatsApp</span>
                  </div>
                </a>

                <a href="https://t.me/GOR_Kharisma_Bot">
                  <div class="tele">
                    <i class="fa-brands fa-telegram"></i>
                    <span>Telegram</span>
                  </div>
                </a>
          </div>
          
        <div class="lapanganTable" id="lapanganTable">
        <table>
          <tr class = "headtable">
              <td>Waktu</td>
              <?php
              $jumlah_lapangan = 1;
              for ($i = 1; $i <= $jumlah_lapangan; $i++) {
                  echo "<td><h3>Lapangan Futsal $i</h3></td>";
              }
              ?>
          </tr>

          <?php
          $times = array(
              "07.00", "08.00", "09.00", "10.00", "11.00", "12.00", "13.00", "14.00",
              "15.00", "16.00", "17.00", "18.00", "19.00", "20.00", "21.00", "22.00",
          );

          $durasi_rows = array_fill(0, count($times), array_fill(0, $jumlah_lapangan, 0));

          foreach ($times as $index => $time) {
              echo "<tr>";
              echo "<td>" . $time . "</td>";

              $sql_tampilan_booking = "SELECT * FROM jadwal WHERE jam_mulai = '$time' AND lapangan = 'futsal' AND tanggal = '$selectedDate'";
              $result_tampilan_booking = $db->query($sql_tampilan_booking);

              for ($i = 0; $i < $jumlah_lapangan; $i++) {
                  if ($durasi_rows[$index][$i] > 0) {
                      // If there's already a rowspan in effect, decrement the counter and skip the cell.
                      $durasi_rows[$index][$i]--;
                      continue;
                  }
                  if ($result_tampilan_booking->num_rows > 0) {
                      $found_booking = false;
                      while ($row = $result_tampilan_booking->fetch_assoc()) {
                        $durasi_isi = $row['durasi'];
                        $nama = $row['nama_tim'];
                        $nomor_lapangan = $row['nomor_lapangan'] - 1;
                        $pembayaran = $row['pembayaran'];
                        if ($pembayaran == 'DP') {
                            $class = 'dp';
                        } elseif ($pembayaran == 'lunas') {
                            $class = 'lunas';
                        } else {
                            $class = 'belumbayar';
                        }
                        if ($nomor_lapangan == $i) {
                            if ($durasi_isi > 1) {
                                echo '<td rowspan="' . $durasi_isi . '" class="' . $class . '">' . $nama . '</td>';
                                for ($j = 0; $j < $durasi_isi; $j++) {
                                    $durasi_rows[$index + $j][$i] = $durasi_isi - $j;
                                }
                            } else {
                                echo '<td class="' . $class . '">' . $nama . '</td>';
                            }
                            $found_booking = true;
                            break;
                        }
                      }

                      if (!$found_booking) {
                          echo '<td></td>';
                      }
                  } else {
                      echo '<td></td>';
                  }
              }

              echo "</tr>";
          }
          ?>
        </table>
        </div>

        <div class="lapanganTable2" id="lapanganTable2" style="display: none;">
        
        <table>
          <tr class = "headtable">
              <td>Waktu</td>
              <?php
              $jumlah_lapangan = 2;
              for ($i = 1; $i <= $jumlah_lapangan; $i++) {
                  echo "<td><h3>Lapangan $i</h3></td>";
              }
              ?>
          </tr>

          <?php
          $times = array(
              "07.00", "08.00", "09.00", "10.00", "11.00", "12.00", "13.00", "14.00",
              "15.00", "16.00", "17.00", "18.00", "19.00", "20.00", "21.00", "22.00",
          );

          $durasi_rows = array_fill(0, count($times), array_fill(0, $jumlah_lapangan, 0));

          foreach ($times as $index => $time) {
              echo "<tr>";
              echo "<td>" . $time . "</td>";

              $sql_tampilan_booking = "SELECT * FROM jadwal WHERE jam_mulai = '$time' AND lapangan = 'badminton' AND tanggal = '$selectedDate' ";
              $result_tampilan_booking = $db->query($sql_tampilan_booking);


              for ($i = 0; $i < $jumlah_lapangan; $i++) {
                  if ($durasi_rows[$index][$i] > 0) {
                      $durasi_rows[$index][$i]--;
                      continue;
                  }

                  if ($result_tampilan_booking->num_rows > 0) {
                      $found_booking = false;
                      $result_tampilan_booking->data_seek(0); // Reset result pointer

                      while ($row = $result_tampilan_booking->fetch_assoc()) {
                          $durasi_isi = $row['durasi'];
                          $nama = $row['nama_tim'];
                          $nomor_lapangan = $row['nomor_lapangan'] - 1;
                          $pembayaran = $row['pembayaran'];

                          if ($pembayaran == 'DP') {
                              $class = 'dp';
                          } elseif ($pembayaran == 'lunas') {
                              $class = 'lunas';
                          } else {
                              $class = 'belumbayar';
                          }

                          if ($nomor_lapangan == $i) {
                              if ($durasi_isi > 1) {
                                  echo '<td rowspan="' . $durasi_isi . '" class="' . $class . '">' . $nama . '</td>';
                                  for ($j = 0; $j < $durasi_isi; $j++) {
                                      $durasi_rows[$index + $j][$i] = $durasi_isi - $j;
                                  }
                              } else {
                                  echo '<td class="' . $class . '">' . $nama . '</td>';
                              }
                              $found_booking = true;
                              break;
                          }
                      }
                      if (!$found_booking) {
                          echo '<td></td>';
                      }
                  } else {
                      echo '<td></td>';
                  }
              }

              echo "</tr>";
          }
          ?>
        </table>

        </div>

        <div class="lapanganTable3" id="lapanganTable3" style="display: none;">
        <table>
          <tr class = "headtable">
              <td>Waktu</td>
              <?php
              $jumlah_lapangan = 1;
              for ($i = 1; $i <= $jumlah_lapangan; $i++) {
                  echo "<td><h3>Lapangan Volly $i</h3></td>";
              }
              ?>
          </tr>

          <?php
          $times = array(
              "07.00", "08.00", "09.00", "10.00", "11.00", "12.00", "13.00", "14.00",
              "15.00", "16.00", "17.00", "18.00", "19.00", "20.00", "21.00", "22.00",
          );

          $durasi_rows = array_fill(0, count($times), array_fill(0, $jumlah_lapangan, 0));

          foreach ($times as $index => $time) {
              echo "<tr>";
              echo "<td>" . $time . "</td>";

              $sql_tampilan_booking = "SELECT * FROM jadwal WHERE jam_mulai = '$time' AND lapangan = 'volly' AND tanggal = '$selectedDate'";
              $result_tampilan_booking = $db->query($sql_tampilan_booking);

              for ($i = 0; $i < $jumlah_lapangan; $i++) {
                  if ($durasi_rows[$index][$i] > 0) {
                      // If there's already a rowspan in effect, decrement the counter and skip the cell.
                      $durasi_rows[$index][$i]--;
                      continue;
                  }

                  if ($result_tampilan_booking->num_rows > 0) {
                      $found_booking = false;

                      while ($row = $result_tampilan_booking->fetch_assoc()) {
                        $durasi_isi = $row['durasi'];
                        $nama = $row['nama_tim'];
                        $nomor_lapangan = $row['nomor_lapangan'] - 1;
                        $pembayaran = $row['pembayaran'];

                        if ($pembayaran == 'DP') {
                            $class = 'dp';
                        } elseif ($pembayaran == 'lunas') {
                            $class = 'lunas';
                        } else {
                            $class = 'belumbayar';
                        }
    
                        if ($nomor_lapangan == $i) {
                            if ($durasi_isi > 1) {
                                echo '<td rowspan="' . $durasi_isi . '" class="' . $class . '">' . $nama . '</td>';
                                for ($j = 0; $j < $durasi_isi; $j++) {
                                    $durasi_rows[$index + $j][$i] = $durasi_isi - $j;
                                }
                            } else {
                                echo '<td class="' . $class . '">' . $nama . '</td>';
                            }
                            $found_booking = true;
                            break;
                        }
                      }

                      if (!$found_booking) {
                          echo '<td></td>';
                      }
                  } else {
                      echo '<td></td>';
                  }
              }

              echo "</tr>";
          }
          ?>
        </table>

        </div>
        </div>

        <div class="kotak1">
          <div class="warna1"></div>
          <span> : Lunas</span>
        </div>
        <div class="kotak2">
        <div class="warna2"></div>
          <span> : DP</span>
        </div>
        <div class="kotak3">
        <div class="warna3"></div>
          <span> : Belum Bayar</span>
        </div>

      </div>

      <div class="booking-container" id="booking-model">
        <div class="booking-form">
          <form action="jadwal.php" method="post">
            <h2>BOOKING LAPANGAN</h2>
            <hr class="garisbooking">
            <div class="booking-item">
              <div class="booking-kiri">
                <div class="form-group">
                  <label for="lokasi">Lokasi Lapangan</label>
                  <select id="lokasi" class="" name="lapangan" onchange="updateTotal(); updateNomorLapangan();">
                    <option value="futsal" data-harga="75000" <?php echo ($lapangan == 'futsal') ? 'selected' : ''; ?>>Lapangan Futsal</option>
                    <option value="badminton" data-harga="30000" <?php echo ($lapangan == 'badminton') ? 'selected' : ''; ?>>Lapangan Badminton</option>
                    <option value="volly" data-harga="50000" <?php echo ($lapangan == 'volly') ? 'selected' : ''; ?>>Lapangan Volly</option>
                  </select> 
                </div>
                <div class="form-group">
                  <label for="tanggal">Tanggal Booking</label>
                  <input type="date" id="input_tanggal" class="" name="tanggal" required>
                </div>
                <div class="form-group" id="jam_item">
                  <div class="mulai">
                    <label for="jam_mulai">Jam Mulai</label>
                    <input type="time" id="jam_mulai"  value="07:00" class="" name="jamMulai" required>
                  </div>
                  <div class="selesai">
                    <label for="jam_selesai">Jam Selesai</label>
                    <input type="time" id="jam_selesai" value="08:00" class="inputabu" name="jamSelesai" readonly>
                  </div>
                </div>
                <div class="form-group">
                    <label for="durasi">Durasi Pemesanan Lapangan</label>
                    <div class="input-number">
                        <button id="btn-minus">-</button>
                        <input type="text" id="durasi" value="1" min="1" readonly name="durasi">
                        <button id="btn-plus">+</button>
                    </div>
                </div>
                <div class="form-group" id="total_biaya">
                    <label>Total Biaya:</label>
                    <span id="total_biaya_span" ></span>
                </div>

              </div>
              <div class="booking-kanan">
                <div class="form-group">
                  <label for="nomor_lapangan">Nomor Lapangan</label>
                  <select name="nomor_lapangan" id="nomor_lapangan" class="">
                            <!-- Options akan muncul secara dinamis -->
                  </select>
                </div>
                <div class="form-group">
                  <label for="namapemesan">Nama Pemesan</label>
                  <input type="text" id="namapemesan" placeholder="Masukkan Nama Anda" required name="pemesan" value="<?php echo isset($_POST['pemesan']) ? $_POST['pemesan'] : ''; ?>">
                </div>
                <div class="form-group">
                  <label for="phone">Nomor Telepon/Handphone Anda</label>
                  <input type="tel" id="phone" name="phone" pattern="[0-9]{10,14}" required
                    placeholder="Masukkan Nomor Telepon/Handphone Anda">
                </div>
                <div class="form-group" id="email-item">
                  <label for="email">Email <span>*opsional</span></label>
                  <input type="email" name="email" id="email" placeholder="Masukkan Email Anda">
                </div>
              </div>
            </div>
            <hr class="hr">
            <div class="btn-container">
              <button class="btn-close" onclick="closeForm()">Close</button>
              <button type="submit" name="save" class="btn-save" onclick="saveForm()">Save</button>
            </div>
        </div>
        </form>
      </div>

    </section>
    <script>

      //munculin booking
      document.addEventListener('DOMContentLoaded', function (){
          const bookingTriggerElements = document.querySelectorAll('.booking-trigger');
          const bookingModal = document.getElementById('booking-model');

          bookingTriggerElements.forEach(function (bookingTrigger) {
            bookingTrigger.addEventListener('click', function () {
              bookingModal.style.display = 'block';
              centerModal();
            });
          });

          function closeForm() {
              bookingModal.style.display = 'none';
            }
            const closeButton = document.querySelector('.btn-close');
            closeButton.addEventListener('click', closeForm);

            window.addEventListener('click', function (event) {
              if (event.target == bookingModal) {
                closeForm();
              }
          });

          function centerModal() {
            const modalWidth = bookingModal.offsetWidth;
            const modalHeight = bookingModal.offsetHeight;
            const screenWidth = window.innerWidth;
            const screenHeight = window.innerHeight;
              bookingModal.style.left = (screenWidth - modalWidth) / 2 + 'px';
              bookingModal.style.top = (screenHeight - modalHeight) / 2 + 'px';
            }
            window.addEventListener('resize', centerModal);
      });

      function updateTotal() {
        const durasi = document.getElementById('durasi').value;
        const jamMulaiValue = document.getElementById('jam_mulai').value;
        const jamSelesaiValue = document.getElementById('jam_selesai').value;
        const lapanganSelect = document.getElementById('lokasi');
        let hargaLapangan;

        // Mendapatkan jam dari jamMulaiValue
        const jamMulaiHour = parseInt(jamMulaiValue.split(':')[0]);
        const jamSelesaiHour = parseInt(jamSelesaiValue.split(':')[0]);

        const lapanganType = lapanganSelect.value;
        let hargaPagi, hargaMalam;

        // Set harga berdasarkan tipe lapangan
        if (lapanganType === 'futsal') {
            hargaPagi = 75000;
            hargaMalam = 90000;
        } else if (lapanganType === 'badminton') {
            hargaPagi = 30000;
            hargaMalam = 40000;
        } else if (lapanganType === 'volly') {
            hargaPagi = 50000;
            hargaMalam = 70000;
        }

        // Inisialisasi total biaya
        let totalBiaya = 0;

        // Jika jam mulai kurang dari 16 dan jam selesai lebih dari 16
        if (jamMulaiHour < 16 && jamSelesaiHour >= 16) {
            const durasiPagi = 16 - jamMulaiHour;
            const durasiMalam = durasi - durasiPagi;
            totalBiaya = (durasiPagi * hargaPagi) + (durasiMalam * hargaMalam);
        } else if (jamMulaiHour >= 16) {
            // Jika jam mulai >= 16
            totalBiaya = durasi * hargaMalam;
        } else {
            // Jika jam mulai < 16 dan jam selesai <= 16
            totalBiaya = durasi * hargaPagi;
        }

        document.getElementById('total_biaya_span').textContent = `Rp. ${totalBiaya.toLocaleString()}`;
      }


      // Panggil fungsi updateTotal() saat halaman dimuat ulang
      document.addEventListener('DOMContentLoaded', function () {
          updateTotal();
      });

      // dateinput
      document.querySelectorAll('.booking-trigger').forEach(function (bookingTrigger, index) {
          bookingTrigger.addEventListener('click', function () {
              document.getElementById('tanggal').value = currentDate.getFullYear() + '-' + (currentDate.getMonth() + 1).toString().padStart(2, '0') + '-' + currentDate.getDate().toString().padStart(2, '0');
          });
      });
        
      // Biar durasi ngurang
      document.getElementById('btn-minus').addEventListener('click', function() {
            event.preventDefault();
            const durasiInput = document.getElementById('durasi');
            const jamMulaiInput = document.getElementById('jam_mulai');
            const jamSelesaiInput = document.getElementById('jam_selesai');
            const jamSelesaiValue = jamSelesaiInput.value;
            const jamSelesaiDate = new Date(`01/01/2000 ${jamSelesaiValue}`);

            // Mengurangi nilai durasi hanya jika durasi lebih besar dari 1
            if (parseInt(durasiInput.value) > 1) {
                durasiInput.value = parseInt(durasiInput.value) - 1;
                updateTotal();

                // Mengurangi nilai jam selesai jika durasi lebih besar dari 1
                jamSelesaiDate.setHours(jamSelesaiDate.getHours() - 1);
                const jamSelesaiNewValue = ("0" + jamSelesaiDate.getHours()).slice(-2) + ":" + ("0" + jamSelesaiDate.getMinutes()).slice(-2);
                jamSelesaiInput.value = jamSelesaiNewValue;
                updateTotal();
            }
        });

        // Biar durasi bisa nambah
        document.getElementById('btn-plus').addEventListener('click', function(event) {
        event.preventDefault();

        const durasiInput = document.getElementById('durasi');
        const jamMulaiInput = document.getElementById('jam_mulai');
        const jamSelesaiInput = document.getElementById('jam_selesai');

        // Mendapatkan nilai jam mulai dan durasi dalam format jam dan menit
        const [jamMulai, menitMulai] = jamMulaiInput.value.split(':').map(Number);
        let durasi = parseInt(durasiInput.value);

        // Menghitung jam selesai berdasarkan jam mulai dan durasi
        let jamSelesai = jamMulai + durasi + 1;
        let menitSelesai = menitMulai;

        if (menitSelesai >= 60) {
            jamSelesai += Math.floor(menitSelesai / 60);
            menitSelesai = menitSelesai % 60;
        }

        if (jamSelesai > 23 || (jamSelesai === 23 && menitSelesai > 0)) {
            alert('Pemesanan tidak dapat melebihi waktu 23:00.');
            return;
        }

        durasiInput.value = durasi + 1;
        const jamSelesaiFormatted = ("0" + jamSelesai).slice(-2) + ":" + ("0" + menitSelesai).slice(-2);
        jamSelesaiInput.value = jamSelesaiFormatted;
        
        updateTotal();
      });


      // Fungsi untuk mengatur nilai jam selesai berdasarkan perubahan jam mulai dan durasi
      function setJamSelesaiFromJamMulai() {
        const jamMulaiInput = document.getElementById('jam_mulai');
        const durasiInput = document.getElementById('durasi');
        const jamSelesaiInput = document.getElementById('jam_selesai');

        // Mendapatkan nilai jam mulai dan durasi dalam format jam dan menit
        let [jamMulai, menitMulai] = jamMulaiInput.value.split(':').map(Number);

        // Set durasi ke 1
        const durasi = 1;
        durasiInput.value = durasi;

        // Jika menit tidak sama dengan 0, setel ke 0
        if (menitMulai !== 0) {
            menitMulai = 0;
            const jamMulaiFormatted = ("0" + jamMulai).slice(-2) + ":" + ("0" + menitMulai).slice(-2);
            jamMulaiInput.value = jamMulaiFormatted;
        }

        // Menghitung jam selesai berdasarkan jam mulai dan durasi
        let jamSelesai = jamMulai + durasi;
        let menitSelesai = menitMulai;

        // Jika jam selesai lebih dari 24, kurangi 24
        if (jamSelesai >= 24) {
            jamSelesai -= 24;
        }

        // Format jam selesai ke dalam format "hh:mm"
        const jamSelesaiFormatted = ("0" + jamSelesai).slice(-2) + ":" + ("0" + menitSelesai).slice(-2);
        jamSelesaiInput.value = jamSelesaiFormatted;

        // Memastikan jam mulai antara 07:00 dan 22:00
        if (jamMulai < 7 || jamMulai > 22 || (jamMulai === 22 && menitMulai > 0)) {
            alert('Jam mulai harus antara 07:00 dan 22:00.');
            jamMulaiInput.value = '07:00';
            durasiInput.value = '1';
            jamSelesaiInput.value = '08:00';
        }

        updateTotal();
      }

    // Tambahkan event listener untuk memeriksa perubahan pada input jam_mulai
      document.getElementById('jam_mulai').addEventListener('input', function() {
          const jamMulaiInput = document.getElementById('jam_mulai');
          let [jam, menit] = jamMulaiInput.value.split(':').map(Number);

          if (menit !== 0) {
              menit = 0;
              const jamMulaiFormatted = ("0" + jam).slice(-2) + ":" + ("0" + menit).slice(-2);
              jamMulaiInput.value = jamMulaiFormatted;
          }
      });

      // Event listener untuk memantau perubahan pada input jam mulai
      document.getElementById('jam_mulai').addEventListener('change', function() {
          setJamSelesaiFromJamMulai();
      });

    // Event listener untuk memantau perubahan pada input durasi
    document.getElementById('durasi').addEventListener('change', function() {
        setJamSelesaiFromJamMulai();
    });

        function changeLayout() {
        var selectedLapangan = document.getElementById("lapangan").value;
          if (selectedLapangan === "futsal") {
            document.getElementById("lapanganTable").style.display = "block";
            document.getElementById("lapanganTable2").style.display = "none";
            document.getElementById("lapanganTable3").style.display = "none";
          } else if (selectedLapangan === "badminton") {
            document.getElementById("lapanganTable").style.display = "none";
            document.getElementById("lapanganTable2").style.display = "block";
            document.getElementById("lapanganTable3").style.display = "none";
          } else if (selectedLapangan === "volly") {
            document.getElementById("lapanganTable").style.display = "none";
            document.getElementById("lapanganTable2").style.display = "none";
            document.getElementById("lapanganTable3").style.display = "block";
          }
        };

    </script>

  <script>
    const nomorLapanganOptions = {
        futsal: [
            { value: '1', text: 'Lapangan 1' }
        ],
        badminton: [
            { value: '1', text: 'Lapangan 1' },
            { value: '2', text: 'Lapangan 2' }
        ],
        volly: [
            { value: '1', text: 'Lapangan 1' }
        ]
    };

    function updateNomorLapangan() {
        const lapanganSelect = document.getElementById('lokasi');
        const nomorLapanganSelect = document.getElementById('nomor_lapangan');
        const selectedLapangan = lapanganSelect.value;

        // Clear current options
        nomorLapanganSelect.innerHTML = '';

        // Add new options based on selected lapangan
        nomorLapanganOptions[selectedLapangan].forEach(option => {
            const optionElement = document.createElement('option');
            optionElement.value = option.value;
            optionElement.text = option.text;
            nomorLapanganSelect.appendChild(optionElement);
        });
    }

    // Call updateNomorLapangan on page load to set initial options
    document.addEventListener('DOMContentLoaded', updateNomorLapangan);
  </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var today = new Date();
            var day = today.getDate();
            var month = today.getMonth() + 1;
            var year = today.getFullYear();

            
            if(day < 10) {
                day = '0' + day;
            } 
            if(month < 10) {
                month = '0' + month;
            }

            var todayString = year + '-' + month + '-' + day;
            document.getElementById('input_tanggal').setAttribute('min', todayString);
            document.getElementById('filtertanggal').setAttribute('min', todayString);
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        <?php if (isset($_SESSION['submit_filter']) && $_SESSION['submit_filter'] == true) { ?>
            Swal.fire({
                title: "Filter Gagal!",
                text: "Silahkan pilih tanggal terlebih dahulu!",
                icon: "error"
            });
            <?php unset($_SESSION['submit_filter']);
        } ?>
        <?php if (isset($_SESSION['booking_sukses']) && $_SESSION['booking_sukses'] == true) { ?>
            Swal.fire({
                title: "Booking Sukses!",
                text: "Reservasi anda telah dicatat, silahkan hubungi admin untuk perihal pembayaran.",
                icon: "success"
            });
            <?php unset($_SESSION['booking_sukses']);
        } ?>
        <?php if (isset($_SESSION['booking_gagal']) && $_SESSION['booking_gagal'] == true) { ?>
            Swal.fire({
                title: "Booking Gagal!",
                text: "Maaf, waktu yang Anda pilih sudah terisi. Silakan pilih waktu lain.",
                icon: "error"
            });
            <?php unset($_SESSION['booking_gagal']);
        } ?>
    </script>
</body>

</html>