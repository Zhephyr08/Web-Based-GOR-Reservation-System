<?php
include "../service/database.php";
session_start();

if (isset($_POST['kirim'])) {
    $nama = $_POST['nama'];
    $komentar = $_POST['komentar'];
    $rating = $_POST['rating'];

    $sql = "INSERT INTO ulasan (nama, komentar, rating) VALUES ('$nama', '$komentar', '$rating')";
    $result = $db->query($sql);
    $_SESSION['submit_ulasan'] = true;
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

$sql_ulasan = "SELECT * FROM ulasan ORDER BY id_ulasan DESC";
$result_ulasan = $db->query($sql_ulasan);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ulasan</title>
    <link rel="stylesheet" href="ulasan2.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
        integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11">
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
                <h1>ULASAN</h1>
                <a href="https://t.me/GOR_Kharisma_Bot">
                    <div class="containerBot">
                        <div class="chatbot">
                            <img src="logo_telegramBOT.png" alt="">
                            <p>Telegram BOT</p>
                        </div>
                    </div>
                </a>
            </div>
        </section>
        <section id="second">
            <div class="ulasan">
                <div class="input-ulasan">

                    <div class="input-item">

                        <form action="ulasan.php" method="POST">
                            <input type="text" name="nama" id="nama" placeholder="Masukkan Nama Anda" required>
                            <hr>
                            <textarea type="text" name="komentar" id="komentar" cols="30" rows="10"
                                placeholder="Masukkan Komentar Anda"></textarea>
                            <hr>
                            <div class="rating">
                                <input type="radio" id="rating1" name="rating" value="5" required hidden>
                                <label for="rating1"><i class="fa-solid fa-star"></i></label>
                                <input type="radio" id="rating2" name="rating" value="4" required hidden>
                                <label for="rating2"><i class="fa-solid fa-star"></i></label>
                                <input type="radio" id="rating3" name="rating" value="3" required hidden>
                                <label for="rating3"><i class="fa-solid fa-star"></i></label>
                                <input type="radio" id="rating4" name="rating" value="2" required hidden>
                                <label for="rating4"><i class="fa-solid fa-star"></i></label>
                                <input type="radio" id="rating5" name="rating" value="1" required hidden>
                                <label for="rating5"><i class="fa-solid fa-star"></i></label>
                            </div>
                            <div class="kirim">
                                <button type="submit" name="kirim" class="send">KIRIM</button>
                            </div>
                        </form>
                    </div>
                </div>
                <br></br>
                <?php
                if ($result_ulasan->num_rows > 0) {
                    while ($row = $result_ulasan->fetch_assoc()) {
                        $tanggal = date('j F Y, h:i:s', strtotime($row['tanggal']))
                            ?>
                        <div class="tampil">
                            <div class="tampil-container">
                                <div class="tampil-item">
                                    <div class="profil">
                                        <i class="fa-solid fa-user"></i>
                                        <h4><?php echo ($row['nama']) ?></h4>
                                    </div>
                                    <p><?php echo ($row['komentar']) ?></p>
                                    <div class="tanggal"> <?php echo $tanggal ?> </div>
                                    <div class="rating2">
                                        <?php
                                        $rating = intval($row['rating']);
                                        for ($i = 0; $i < $rating; $i++) {
                                            ?>
                                            <i class="fa-solid fa-star"></i>
                                            <?php
                                        }
                                        ?>
                                        <span class="rating2"><?php echo ($row['rating']) ?></span>
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
        </section>

        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <script>
            <?php if (isset($_SESSION['submit_ulasan']) && $_SESSION['submit_ulasan'] == true) { ?>
                Swal.fire({
                    title: "Ulasan Anda Telah Dikirim!",
                    text: "Terima kasih telah menggunakan layanan kami!",
                    icon: "success"
                });
                <?php unset($_SESSION['submit_ulasan']);
            } ?>
        </script> 
</body >
</html>