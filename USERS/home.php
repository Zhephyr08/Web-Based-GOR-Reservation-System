<?php
include "../service/database.php";
session_start();

$sql_ulasan = "SELECT * FROM ulasan ORDER BY id_ulasan DESC LIMIT 3";
$result_ulasan = $db->query($sql_ulasan);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GOR KHARISMA BANDUNG</title>
    <link rel="stylesheet" href="home1.css">
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
        <div class="utama-container">
            <div class="utama-content">
                <div class="utama-kata">
                    <h1><q>Mari memulai hari dengan olahraga karena
                            <br>untuk menangis perih juga butuh tenaga</q></h1>
                </div>
                <div class="utama-btn">
                    <a href="jadwal.php">Pesan Sekarang!</a>
                </div>
                <a href="https://t.me/GOR_Kharisma_Bot">
                    <div class="containerBot">
                        <div class="chatbot">
                            <img src="logo_telegramBOT.png" alt="">
                            <p>Telegram BOT</p>
                        </div>
                    </div>
                </a>
            </div>
        </div>
        </section>
        <hr class="garis">
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
        <section id="tempat">
            <div class="tempat-lokasi">
                <div class="lokasi-container">
                    <div class="h3">
                        <p>Lokasi</p>
                    </div>
                    <div class="lokasi-item">
                        <i class="fa-solid fa-location-dot"></i>
                        <div class="lokasi-content">
                            <p>Jl. Giri Hiyang No.27, RT.001/RW.004, Pasir Endah, Kec. Ujung Berung, Kota Bandung, Jawa
                                Barat 40619</p>
                            <a href="https://maps.app.goo.gl/1GrTrGQqgAuzRCPf8" target="_blank">Akses Google Map</a>
                        </div>
                        <iframe
                            src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d15843.582271017916!2d107.6894646!3d-6.9030908!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e68dd6a5fa50c31%3A0xabb7c3fa350e8806!2sGOR%20Kharisma!5e0!3m2!1sid!2sid!4v1711370357193!5m2!1sid!2sid"
                            width="400" height="300" style="border:0;" allowfullscreen="" loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade">
                        </iframe>
                    </div>
                </div>
                <div class="fasilitas-container">
                    <div class="tempat-head">
                        <div class="h3">
                            <p>Fasilitas</p>
                        </div>
                    </div>
                    <div class="fasilitas-item">
                        <ul>
                            <li>Lapangan Futsal</li>
                            <li>Lapangan Bulu Tangkis</li>
                            <li>Lapangan Voli dan Tenis</li>
                            <li>Kantin</li>
                            <li>Tempat Parkir</li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>
        <section id="galeri">
            <div class="atas-container">
                <div class="atas-kiri">
                    <div class="kiri-satu"></div>
                    <div class="kiri-dua"></div>
                    <div class="kiri-tiga"></div>
                </div>
                <h2>GALERI</h2>
                <div class="atas-kanan">
                    <div class="kanan-satu"></div>
                    <div class="kanan-dua"></div>
                    <div class="kanan-tiga"></div>
                </div>
            </div>
            <div class="foto-container">
                <div class="slider">
                    <img class="foto-item" src="gor1.png" alt="">
                    <img class="foto-item" src="gor4.png" alt="">
                    <img class="foto-item" src="gor2.png" alt="">
                    <img class="foto-item" src="gor3.png" alt="">
                    <img class="foto-item" src="gor5.png" alt="">
                    <img class="foto-item" src="gor6.png" alt="">
                    <img class="foto-item" src="gor7.png" alt="">
                    <img class="foto-item" src="gor10.jpg" alt="">
                    <img class="foto-item" src="gor9.png" alt="">
                    <img class="foto-item" src="gor8.png" alt="">
                </div>
                <div class="btn-slide">
                    <div class="prev">
                        <i class="fa-solid fa-caret-left"></i>
                    </div>
                    <div class="next">
                        <i class="fa-solid fa-caret-right"></i>
                    </div>
                </div>
            </div>
        </section>
        <section id="ulasan">
            <div class="ulasan-head">
                <div class="h3">
                    <p>Ulasan</p>
                </div>
                <a href="ulasan.php">
                    <div class="lainnya">
                        <span>Lainnya</span>
                        <i class="fa-solid fa-angles-right"></i>
                    </div>
                </a>
            </div>
            <div class="ulasan">


                <?php
                if ($result_ulasan->num_rows > 0) {
                    while ($row = $result_ulasan->fetch_assoc()) {
                        ?>
                        <div class="ulasan-item">
                            <div class="profil">
                                <i class="fa-solid fa-user"></i>
                                <h4><?php echo ($row['nama']) ?></h4>
                            </div>
                            <p><?php echo ($row['komentar']) ?></p>
                            <div class="rating">
                                <div class="rating-item">
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
                        <?php
                    }
                } else {
                    echo "Belum ada ulasan.";
                }
                ?>


            </div>
        </section>
    </main>
    <footer>
        <div class="foot">
            <h2>Hubungi Kami</h2>
            <div class="icon-foot">
                <div class="icon-container">
                    <div class="icon-item">
                        <i class="fa-solid fa-location-dot"></i>
                    </div>
                    <p>Jl. Giri Hiyang No.27, Pasir Endah, Ujung Berung, Bandung</p>
                </div>
                <div class="icon-container">
                    <div class="icon-item">
                        <i class="fa-solid fa-phone"></i>
                    </div>
                    <a href="https://wa.me/+6285822043211">0858-2204-3211</a>
                </div>
                <div class="icon-container">
                    <div class="icon-item">
                        <i class="fa-solid fa-at"></i>
                    </div>
                    <a href="mailto:gorkharisma.bdg@gmail.com">gorkharisma.bdg@gmail.com</a>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        <?php if (isset($_SESSION['is_logout']) && $_SESSION['is_logout'] == true) { ?>
            Swal.fire({
                title: "Anda Berhasil Keluar!",
                text: "Sampai Jumpa <?= $_SESSION["username"] ?>!",
                icon: "success"
            });
            <?php $_SESSION['is_logout'] = false;
            session_unset();
            session_destroy();
        } ?>
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const slides = document.querySelector('.slider'); // Mendapatkan elemen slider
            const slideItems = document.querySelectorAll('.foto-item'); // Mendapatkan semua item foto
            const totalSlides = slideItems.length; // Menghitung jumlah total slide
            let slideIndex = 0; // Menyimpan indeks slide saat ini

            // Fungsi untuk memperbarui posisi slide berdasarkan indeks saat ini
            function updateSlidePosition() {
                const offset = (slideIndex * -100) / 3; // Menghitung offset untuk memusatkan slide
                slides.style.transform = 'translateX(' + offset + '%)'; // Mengatur transformasi slider
            }

            // Fungsi untuk menggeser slide ke kiri atau kanan
            function moveSlide(n) {
                slideIndex += n; // Mengubah indeks slide

                // Jika indeks melebihi total slide, kembali ke slide pertama
                if (slideIndex >= (totalSlides - 2)) {
                    slideIndex = 0;
                } else if (slideIndex < 0) {
                    // Jika indeks kurang dari 0, kembali ke slide terakhir
                    slideIndex = totalSlides - 3;
                }

                updateSlidePosition(); // Memperbarui posisi slide
            }


            // Menambahkan event listener untuk tombol prev
            document.querySelector('.prev').addEventListener('click', function () {
                moveSlide(-1); // Menggeser slide ke kiri
            });

            // Menambahkan event listener untuk tombol next
            document.querySelector('.next').addEventListener('click', function () {
                moveSlide(1); // Menggeser slide ke kanan
            });

            updateSlidePosition();
        });

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
</body>

</html>