<?php
include "../service/database.php";
session_start();
$lapangan = '';
$nomor_lapangan = '';

if (!isset($_SESSION["is_login"])) {
    header("location: ../USERS/home.php");
    exit();
}

if (isset($_POST['logout'])) {
    $_SESSION["is_login"] = false;
    $_SESSION["is_logout"] = true;
    header("location: ../USERS/home.php");
    exit();
}

if (isset($_POST['kirimN'])) {
    $id_jadwal = $_POST['id_jadwal'];
    $sql = "DELETE FROM `jadwal` WHERE id_jadwal = $id_jadwal";
    $result = $db->query($sql);
    if ($result) {
        $_SESSION['status_cancel'] = true;
    } else {
        echo "Error: " . $db->error;
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

if (isset($_POST['kirimY'])) {
    $id_jadwal = $_POST['id_jadwal'];

    $sql_check_existing = "SELECT COUNT(*) as count FROM keuangan WHERE id_keuangan = $id_jadwal";
    $result_check_existing = $db->query($sql_check_existing);
    $row_check_existing = $result_check_existing->fetch_assoc();
    $existing_count = $row_check_existing['count'];

    $sql = "UPDATE jadwal SET pembayaran = 'lunas' WHERE id_jadwal = $id_jadwal";
    $result = $db->query($sql);

    $sql_jadwal = "SELECT * FROM jadwal WHERE id_jadwal = $id_jadwal";
    $result_jadwal = $db->query($sql_jadwal);
    $row_jadwal = $result_jadwal->fetch_assoc();

    $id_keuangan = $id_jadwal;
    $nama_tim = $row_jadwal['nama_tim'];
    $tanggal = $row_jadwal['tanggal'];
    $lapangan = $row_jadwal['lapangan'];
    $durasi = $row_jadwal['durasi'];
    $harga = $row_jadwal['harga'];
    $status = $row_jadwal['pembayaran'];
    $total = $harga;

    if ($existing_count > 0) {
        $sql_update_keuangan = "UPDATE keuangan SET status = '$status', total = $total WHERE id_keuangan = $id_jadwal";
        $result_update_keuangan = $db->query($sql_update_keuangan);
        if ($result_update_keuangan) {
            $_SESSION['status_lunas'] = true;
        } else {
            echo "Error: " . $db->error;
        }
    } else {
        $sql_insert = "INSERT INTO keuangan VALUES ('$id_keuangan', '$nama_tim', '$tanggal', '$lapangan', '$durasi', '$status', '$total')";
        $result_insert = $db->query($sql_insert);
        if ($result_insert) {
            $_SESSION['status_lunas'] = true;
        } else {
            echo "Error: " . $db->error;
        }
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

if (isset($_POST['kirimD'])) {
    $id_jadwal = $_POST['id_jadwal'];

    $sql = "UPDATE jadwal SET pembayaran = 'DP' WHERE id_jadwal = $id_jadwal";
    $result = $db->query($sql);

    $sql_jadwal = "SELECT * FROM jadwal WHERE id_jadwal = $id_jadwal";
    $result_jadwal = $db->query($sql_jadwal);
    $row_jadwal = $result_jadwal->fetch_assoc();

    $id_keuangan = $id_jadwal;
    $nama_tim = $row_jadwal['nama_tim'];
    $tanggal = $row_jadwal['tanggal'];
    $lapangan = $row_jadwal['lapangan'];
    $durasi = $row_jadwal['durasi'];
    $harga = $row_jadwal['harga'];
    $status = $row_jadwal['pembayaran'];
    $total = 20000;

    $sql_insert = "INSERT INTO keuangan VALUES ('$id_keuangan', '$nama_tim', '$tanggal', '$lapangan', '$durasi', '$status', '$total')";
    $result_insert = $db->query($sql_insert);
    if ($result_insert) {
        $_SESSION['status_DP'] = true;
    } else {
        echo "Error: " . $db->error;
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['save'])){
  
    // Mengambil data dari POST
    $tanggal = isset($_POST['tanggal']) ? date('Y-m-d', strtotime($_POST['tanggal'])) : '';
    $nama_tim = isset($_POST['pemesan']) ? $_POST['pemesan'] : '';
    $jam_mulai = isset($_POST['jamMulai']) ? $_POST['jamMulai'] : '';
    $jam_selesai = isset($_POST['jamSelesai']) ? $_POST['jamSelesai'] : '';
    $durasi = isset($_POST['durasi']) ? intval($_POST['durasi']) : 0;
    $status = isset($_POST['status_pembayaran']) ? $_POST['status_pembayaran'] : '';
    $lapangan = isset($_POST['lapangan']) ? $_POST['lapangan'] : '';
    $nomor_lapangan = isset($_POST['nomor_lapangan']) ? $_POST['nomor_lapangan'] : '';
    $ID = isset($_POST['ID']) ? $_POST['ID'] : '';
  
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
  
    // Memeriksa ketersediaan jadwal
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
        $_SESSION['update_gagal'] = true;
    } else {
        $sql_get_previous_status = "SELECT pembayaran FROM jadwal WHERE id_jadwal = $ID";
        $result_get_previous_status = $db->query($sql_get_previous_status);
        if ($result_get_previous_status->num_rows > 0) {
            $row = $result_get_previous_status->fetch_assoc();
            $previous_status = $row['pembayaran'];

            $sql_update_reservasi = "UPDATE jadwal SET tanggal = '$tanggal', nama_tim = '$nama_tim', jam_mulai = '$jam_mulai', jam_selesai = '$jam_selesai', durasi = '$durasi', nomor_lapangan = '$nomor_lapangan', lapangan = '$lapangan', harga = '$total_biaya', pembayaran = '$status' WHERE id_jadwal = $ID";
            if ($db->query($sql_update_reservasi) === true) {
                if ($previous_status == 'DP' && $status == 'belum bayar') {
                    $sql_delete_keuangan = "DELETE FROM keuangan WHERE id_keuangan = $ID";
                    if ($db->query($sql_delete_keuangan) === true) {
                    } else {
                        die("Error: " . $db->error);
                    }
                } else if ($previous_status == 'lunas' && $status == 'belum bayar') {
                    $sql_delete_keuangan = "DELETE FROM keuangan WHERE id_keuangan = $ID";
                    if ($db->query($sql_delete_keuangan) === true) {
                    } else {
                        die("Error: " . $db->error);
                    }
                }
                $_SESSION['update_sukses'] = true;
            } else {
                die("Error: " . $db->error);
            }
        } else {
            $_SESSION['update_error'] = true;
        }
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

$sql = "SELECT * FROM jadwal ORDER BY id_jadwal ASC";
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="Dasboard_Reservasi2.css">

    <!-- Icons CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11"></link>
    <title>DASBOARD Reservasi</title>
</head>
<body>
<nav class="middlebar">
    <header>
        <div class="text header-text">
            <span class="name"><?= $_SESSION["username"] ?></span>
            <span class="date"><?= $currentDate ?></span>
        </div>
    </header>
</nav>

<nav class="sidebar">
    <header>
        <div class="text header-text">
            <a href="Dasboard.php">
                <span class="name">HALAMAN UTAMA</span>
            </a>
        </div>
    </header>
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

<span class="nama">RESERVASI</span>


<div class="middlebox_1">
    <div class="table-container">
    <input class="tombol_3 booking-trigger" type="button" name="input" value="Perbaharui Tabel">
        <table class="table table-bordered">
            <thead>
            <tr>
                <th>ID</th>
                <th>Lapangan</th>
                <th>NL</th>
                <th>Tanggal</th>
                <th>Nama</th>
                <th>Mulai</th>
                <th>Selesai</th>
                <th>Durasi</th>
                <th>Harga</th>
                <th>Nomor HP</th>
                <th>Pembayaran</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $bukan_lunas = false;
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    if ($row['pembayaran'] === 'lunas') {
                        continue;
                    } else if ($row['pembayaran'] !== 'lunas'){
                        $bukan_lunas = true;
                    }
                    $tgl = date('j F Y', strtotime($row['tanggal']))
                    ?>
                    <tr>
                        <td><?= $row['id_jadwal'] ?></td>
                        <td><?= ucfirst($row['lapangan']) ?></td>
                        <td><?= $row['nomor_lapangan'] ?></td>
                        <td><?= $tgl ?></td>
                        <td><?= $row['nama_tim'] ?></td>
                        <td><?= $row['jam_mulai'] ?>:00</td>
                        <td><?= $row['jam_selesai'] ?>:00</td>
                        <td><?= $row['durasi'] ?> Jam</td>
                        <td>Rp. <?= number_format($row['harga'], 0, ',', '.') ?></td>
                        <td><?= $row['nomor_hp'] ?></td>
                        <td>
                            <form method="POST" action="">
                                <input type="hidden" name="id_jadwal" value="<?= $row['id_jadwal']; ?>">
                                <?php if ($row['pembayaran'] == 'DP') { ?>
                                    <button type="button" name="kirimY" id="kirimY_<?= $row['id_jadwal']; ?>"
                                            class="btn btn-success">Lunas
                                    </button>
                                    <button type="button" name="kirimN" id="kirimN_<?= $row['id_jadwal']; ?>"
                                            class="btn btn-danger">Batalkan
                                    </button>
                                <?php } else { ?>
                                    <button type="button" name="kirimD" id="kirimD_<?= $row['id_jadwal']; ?>"
                                            class="btn btn-warning">DP
                                    </button>
                                    <button type="button" name="kirimY" id="kirimY_<?= $row['id_jadwal']; ?>"
                                            class="btn btn-success">Lunas
                                    </button>
                                    <button type="button" name="kirimN" id="kirimN_<?= $row['id_jadwal']; ?>"
                                            class="btn btn-danger">Batalkan
                                    </button>
                                <?php } ?>
                            </form>
                        </td>
                    </tr>
                    <?php
                }
                if ($bukan_lunas === false) {
                    echo "<tr><td colspan='12'>Belum ada reservasi.</td></tr>";
                }
            } else {
                echo "<tr><td colspan='12'>Belum ada reservasi.</td></tr>";
            }
            ?>
            </tbody>
        </table>
    </div>
</div>

    <div class="booking-container" id="booking-model">
        <div class="booking-form">
          <form action="Dasboard_Reservasi.php" method="post">
            <h2>UPDATE TABEL RESERVASI</h2>
            <hr class="garisbooking">
            <div class="booking-item">
              <div class="booking-kiri">
                <div class="form-group" id="ID-item">
                  <label for="ID">ID <span>*Wajib</span></label>
                  <input name="ID" id="ID" placeholder="Masukkan ID Tabel" required>
                </div>
                <div class="form-group">
                  <label for="tanggal">Tanggal Booking</label>
                  <input type="date" id="input_tanggal" class="" name="tanggal" required>
                </div>
                <div class="form-group" id="jam_item">
                  <div class="mulai">
                    <label for="jam_mulai">Jam Mulai</label>
                    <input type="time" id="jam_mulai"  value="07:00" class="" name="jamMulai" onchange="updateTotal();" required>
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
                  <label for="lokasi">Lokasi Lapangan</label>
                  <select id="lokasi" class="" name="lapangan" onchange="updateTotal(); updateNomorLapangan();">
                    <option value="futsal" data-harga="75000" <?php echo ($lapangan == 'futsal') ? 'selected' : ''; ?>>Lapangan Futsal</option>
                    <option value="badminton" data-harga="30000" <?php echo ($lapangan == 'badminton') ? 'selected' : ''; ?>>Lapangan Badminton</option>
                    <option value="volly" data-harga="50000" <?php echo ($lapangan == 'volly') ? 'selected' : ''; ?>>Lapangan Volly</option>
                  </select> 
                </div>
                <div class="form-group">
                  <label for="nomor_lapangan">Nomor Lapangan</label>
                  <select name="nomor_lapangan" id="nomor_lapangan" class="">
                            <!-- Options akan muncul secara dinamis -->
                  </select>
                </div>
                <div class="form-group">
                  <label for="namapemesan">Nama Pemesan</label>
                  <input type="text" id="namapemesan" placeholder="Masukkan Nama" required name="pemesan" value="<?php echo isset($_POST['pemesan']) ? $_POST['pemesan'] : ''; ?>">
                </div>
                <div class="form-group">
                  <label for="status_pembayaran">Status Pembayaran</label>
                  <select id="status_pembayaran" class="" name="status_pembayaran">
                    <option value="belum bayar">Belum Bayar</option>
                  </select> 
                </div>
              </div>
            </div>
            <hr class="hr">
            <div class="btn-container">
              <button type="button" class="btn-tutup" onclick="closeForm()">Close</button>
              <button type="submit" name="save" class="btn-save" onclick="saveForm()">Update</button>
            </div>
        </div>
        </form>
    </div>

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

      const closeButton = document.querySelector('.btn-tutup');
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
  });
</script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('button[name="kirimY"], button[name="kirimN"], button[name="kirimD"]').forEach(function (button) {
            button.addEventListener('click', function (event) {
                event.preventDefault();
                let id = this.id;
                let form = this.closest('form');
                if (id.startsWith('kirimY')) {
                    Swal.fire({
                        title: "Apakah anda yakin?",
                        text: "Status pembayaran akan diubah menjadi lunas!",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#3085d6",
                        cancelButtonColor: "#d33",
                        confirmButtonText: "Yes!"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            let hiddenInput = document.createElement('input');
                            hiddenInput.type = 'hidden';
                            hiddenInput.name = 'kirimY';
                            form.appendChild(hiddenInput);
                            form.submit();
                        }
                    });
                } else if (id.startsWith('kirimN')) {
                    Swal.fire({
                        title: "Apakah anda yakin?",
                        text: "Reservasi akan dibatalkan dan data akan dihapus!",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#3085d6",
                        cancelButtonColor: "#d33",
                        confirmButtonText: "Yes!"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            let hiddenInput = document.createElement('input');
                            hiddenInput.type = 'hidden';
                            hiddenInput.name = 'kirimN';
                            form.appendChild(hiddenInput);
                            form.submit();
                        }
                    });
                } else if (id.startsWith('kirimD')) {
                    Swal.fire({
                        title: "Apakah anda yakin?",
                        text: "Status pembayaran akan diubah menjadi DP!",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#3085d6",
                        cancelButtonColor: "#d33",
                        confirmButtonText: "Yes!"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            let hiddenInput = document.createElement('input');
                            hiddenInput.type = 'hidden';
                            hiddenInput.name = 'kirimD';
                            form.appendChild(hiddenInput);
                            form.submit();
                        }
                    });
                }
            });
        });
    });
</script>

<script>
        <?php if (isset($_SESSION['update_sukses']) && $_SESSION['update_sukses'] == true) { ?>
            Swal.fire({
                title: "Update Sukses!",
                text: "Reservasi telah diubah, status pembayaran di-reset kembali.",
                icon: "success"
            });
            <?php unset($_SESSION['update_sukses']);
        } ?>
        <?php if (isset($_SESSION['update_gagal']) && $_SESSION['update_gagal'] == true) { ?>
            Swal.fire({
                title: "Update Gagal!",
                text: "Maaf, waktu yang dipilih sudah terisi. Silakan pilih waktu lain.",
                icon: "error"
            });
            <?php unset($_SESSION['update_gagal']);
        } ?>
        <?php if (isset($_SESSION['update_error']) && $_SESSION['update_error'] == true) { ?>
            Swal.fire({
                title: "Update Gagal!",
                text: "Maaf, ID jadwal tidak ditemukan.",
                icon: "error"
            });
            <?php unset($_SESSION['update_error']);
        } ?>
        <?php if (isset($_SESSION['status_lunas']) && $_SESSION['status_lunas'] == true) { ?>
            Swal.fire({
                title: "Sukses!",
                text: "Reservasi telah dibayar secara penuh.",
                icon: "success",
            });
            <?php unset($_SESSION['status_lunas']);
        } ?>
        <?php if (isset($_SESSION['status_cancel']) && $_SESSION['status_cancel'] == true) { ?>
            Swal.fire({
                title: "Reservasi Dibatalkan!",
                text: "Data reservasi telah dihapus.",
                icon: "success",
            });
            <?php unset($_SESSION['status_cancel']);
        } ?>
        <?php if (isset($_SESSION['status_DP']) && $_SESSION['status_DP'] == true) { ?>
            Swal.fire({
                title: "DP Telah Terkonfirmasi!",
                text: "Status pembayaran telah diubah menjadi DP.",
                icon: "success",
            });
            <?php unset($_SESSION['status_DP']);
        } ?>
</script>
</body>
</html>
