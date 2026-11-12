
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
    <title>Login - E-Book Library</title>

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
                <h2>Selamat Datang!</h2>
                <p>Silakan masuk ke akun Anda.</p>
            </div>

            <?php if ($flashSuccess !== ''): ?>
                <div class="login-message success" role="status"><?= htmlspecialchars($flashSuccess, ENT_QUOTES, 'UTF-8') ?></div>
            <?php endif; ?>
            <?php if ($flashError !== ''): ?>
                <div class="login-message error" role="alert"><?= htmlspecialchars($flashError, ENT_QUOTES, 'UTF-8') ?></div>
            <?php endif; ?>

            <form action="process.php" method="POST">
                <input type="hidden" name="action" value="login">
                <div class="login-form-group">
                    <label for="email">Email</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        placeholder="Masukkan email"
                        required
                    >
                </div>

                <div class="login-form-group">
                    <label for="password">Password</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="Masukkan password"
                        required
                    >
                </div>

                <button type="submit" class="login-button">
                    Masuk
                </button>

            </form>

            <div class="login-footer">
                <p>
                    Belum mempunyai akun?
                    <a href="index.php?page=register">Daftar Sekarang</a>
                </p>
            </div>

        </div>

    </div>

</body>
</html>
