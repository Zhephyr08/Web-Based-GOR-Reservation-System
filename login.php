<?php
include "../service/database.php";
session_start();

if (isset($_SESSION["is_login"])) {
    header("location: dasboard.php");
    exit();
}

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM admin WHERE username=? AND password=?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $data = $result->fetch_assoc();
        $_SESSION["username"] = $data["username"];
        $_SESSION["is_login"] = true;
        $_SESSION["first_login"] = true;
        $_SESSION["is_logout"] = false;

        header("location: dasboard.php");
        exit();
    } else {
        $_SESSION['loginfailed'] = true;
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }

    $stmt->close();
    $db->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Form</title>
    <link rel="stylesheet" href="login1.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Itim&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11">
</head>
<body>
    <header>
        <nav>
            <a class="beranda" href="../USERS/home.php">
                <span>Gor</span>
                <span>Kharisma</span>
            </a>
        </nav>
    </header>
    <div class="container">
        <h1>GOR Kharisma</h1>
        <div class="login-box">
            <h2>Selamat Datang</h2>
            <p>Tolong masukkan informasi anda.</p>
            <form action="login.php" method="POST">
                <div class="input-box">
                    <input type="text" name="username" id="username" required>
                    <label for="username">Nama Pengguna</label>
                </div>
                <div class="input-box">
                    <input type="password" name="password" id="password" required>
                    <label for="password">Sandi</label>
                </div>
                <button type="submit" name="login">Masuk</button>
            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        <?php if (isset($_SESSION['loginfailed']) && $_SESSION['loginfailed'] == true) { ?>
                Swal.fire({
                    title: "Masuk Gagal!",
                    text: "Nama Pengguna atau Sandi salah.",
                    icon: "error"
                });
                <?php unset($_SESSION['loginfailed']);
        } ?>
    </script>
</body>
</html>
