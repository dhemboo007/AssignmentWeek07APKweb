<?php
// Koneksi database menggunakan PDO
$host = "localhost";
$dbname = "db_registrasi";
$dbuser = "root";
$dbpass = "";

$pesan = "";
$jenisPesan = "";
$nama = "";
$email = "";
$username = "";

function aman($data) {
    return htmlspecialchars($data, ENT_QUOTES, "UTF-8");
}

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $dbuser,
        $dbpass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
} catch (PDOException $e) {
    die("Koneksi database gagal. Pastikan MySQL aktif dan database sudah dibuat.");
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nama = trim($_POST["nama"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";
    $errors = [];

    // Validasi input
    if ($nama === "") {
        $errors[] = "Nama lengkap wajib diisi.";
    }

    if ($email === "") {
        $errors[] = "Email wajib diisi.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Format email tidak valid.";
    }

    if ($username === "") {
        $errors[] = "Username wajib diisi.";
    } elseif (!preg_match('/^[a-zA-Z0-9_]{4,20}$/', $username)) {
        $errors[] = "Username harus 4-20 karakter dan hanya boleh berisi huruf, angka, atau underscore.";
    }

    if ($password === "") {
        $errors[] = "Password wajib diisi.";
    } elseif (strlen($password) < 8) {
        $errors[] = "Password minimal 8 karakter.";
    }

    if (empty($errors)) {
        try {
            // Cek apakah email atau username sudah digunakan
            $cek = $pdo->prepare(
                "SELECT id FROM users WHERE email = :email OR username = :username"
            );
            $cek->execute([
                ":email" => $email,
                ":username" => $username
            ]);

            if ($cek->fetch()) {
                $pesan = "Email atau username sudah terdaftar. Gunakan data lain.";
                $jenisPesan = "error";
            } else {
                // Password disimpan dalam bentuk hash, bukan teks biasa
                $passwordHash = password_hash($password, PASSWORD_DEFAULT);

                $sql = "INSERT INTO users (nama, email, username, password)
                        VALUES (:nama, :email, :username, :password)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ":nama" => $nama,
                    ":email" => $email,
                    ":username" => $username,
                    ":password" => $passwordHash
                ]);

                $pesan = "Registrasi berhasil! Data pengguna telah disimpan ke database.";
                $jenisPesan = "success";

                // Kosongkan input setelah berhasil
                $nama = $email = $username = "";
            }
        } catch (PDOException $e) {
            $pesan = "Terjadi kesalahan saat menyimpan data. Silakan coba kembali.";
            $jenisPesan = "error";
        }
    } else {
        $pesan = implode("<br>", array_map("aman", $errors));
        $jenisPesan = "error";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi Pengguna</title>
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 24px 16px;
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #e0e7ff, #f8fafc);
            color: #1e293b;
        }
        .container {
            width: 100%;
            max-width: 440px;
            padding: 30px;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 12px 35px #0f172a18;
        }
        h1 { margin: 0 0 8px; text-align: center; font-size: 25px; }
        .subtitle { margin: 0 0 24px; text-align: center; color: #64748b; font-size: 14px; }
        label { display: block; margin: 15px 0 7px; font-weight: bold; font-size: 14px; }
        input {
            width: 100%; padding: 12px 13px; border: 1px solid #cbd5e1;
            border-radius: 8px; font-size: 14px; outline: none;
        }
        input:focus { border-color: #4f46e5; box-shadow: 0 0 0 3px #4f46e51a; }
        button {
            width: 100%; margin-top: 23px; padding: 13px; border: 0;
            border-radius: 8px; background: #4f46e5; color: white;
            font-size: 15px; font-weight: bold; cursor: pointer;
        }
        button:hover { background: #4338ca; }
        .message { padding: 12px; margin-bottom: 15px; border-radius: 8px; font-size: 13px; line-height: 1.6; }
        .success { color: #166534; background: #dcfce7; }
        .error { color: #991b1b; background: #fee2e2; }
        .note { margin: 16px 0 0; text-align: center; color: #64748b; font-size: 12px; }
    </style>
</head>
<body>
<div class="container">
    <h1>Registrasi Pengguna</h1>
    <p class="subtitle">Buat akun baru dengan mengisi formulir berikut.</p>

    <?php if ($pesan !== ""): ?>
        <div class="message <?= aman($jenisPesan) ?>">
            <?= $pesan ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="">
        <label for="nama">Nama Lengkap</label>
        <input type="text" id="nama" name="nama"
               value="<?= aman($nama) ?>" maxlength="100" required>

        <label for="email">Alamat Email</label>
        <input type="email" id="email" name="email"
               value="<?= aman($email) ?>" maxlength="150" required>

        <label for="username">Username</label>
        <input type="text" id="username" name="username"
               value="<?= aman($username) ?>" minlength="4" maxlength="20"
               pattern="[a-zA-Z0-9_]{4,20}" required>

        <label for="password">Password</label>
        <input type="password" id="password" name="password"
               minlength="8" required>

        <button type="submit">Daftar Sekarang</button>
    </form>

    <p class="note">Password dienkripsi menggunakan password_hash() sebelum disimpan.</p>
</div>
</body>
</html>
