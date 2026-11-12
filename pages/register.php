
<?php
$flashSuccess = $_SESSION['flash_success'] ?? '';
$flashError = $_SESSION['flash_error'] ?? '';
unset($_SESSION['flash_success'], $_SESSION['flash_error']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - E-Book Library</title>

    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body class="login-body">

    <div class="login-container">

        <div class="login-card">

            <div class="login-logo">
                <span>📚</span>
                <h1>E-Book Library</h1>
                <p>Perpustakaan Digital</p>
            </div>

            <div class="login-heading">
                <h2>Buat Akun Baru</h2>
                <p>Daftarkan diri untuk menikmati layanan perpustakaan.</p>
            </div>

            <?php if ($flashSuccess !== ''): ?>
                <div class="login-message success" role="status"><?= htmlspecialchars($flashSuccess, ENT_QUOTES, 'UTF-8') ?></div>
            <?php endif; ?>
            <?php if ($flashError !== ''): ?>
                <div class="login-message error" role="alert"><?= htmlspecialchars($flashError, ENT_QUOTES, 'UTF-8') ?></div>
            <?php endif; ?>

            <form action="process.php" method="POST">
                <input type="hidden" name="action" value="register">
                <div class="login-form-group">
                    <label for="registerName">Nama Lengkap</label>
                    <input
                        type="text"
                        id="registerName"
                        name="name"
                        placeholder="Masukkan nama lengkap"
                        required
                    >
                </div>

                <div class="login-form-group">
                    <label for="registerEmail">Email</label>
                    <input
                        type="email"
                        id="registerEmail"
                        name="email"
                        placeholder="Masukkan email"
                        required
                    >
                </div>

                <div class="login-form-group">
                    <label for="registerPassword">Password</label>
                    <input
                        type="password"
                        id="registerPassword"
                        name="password"
                        placeholder="Minimal 6 karakter"
                        minlength="6"
                        required
                    >
                </div>

                <div class="login-form-group">
                    <label for="registerConfirmPassword">Konfirmasi Password</label>
                    <input
                        type="password"
                        id="registerConfirmPassword"
                        name="confirm_password"
                        placeholder="Ulangi password"
                        minlength="6"
                        required
                    >
                </div>

                <button type="submit" class="login-button">
                    Daftar Sekarang
                </button>

            </form>

            <div class="login-footer">
                <p>
                    Sudah mempunyai akun?
                    <a href="index.php?page=login">Masuk Sekarang</a>
                </p>
            </div>

        </div>

    </div>

</body>
</html>
