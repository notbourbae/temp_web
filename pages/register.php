
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

            <form id="registerForm">

                <div class="login-form-group">
                    <label for="registerName">Nama Lengkap</label>
                    <input
                        type="text"
                        id="registerName"
                        placeholder="Masukkan nama lengkap"
                        required
                    >
                </div>

                <div class="login-form-group">
                    <label for="registerEmail">Email</label>
                    <input
                        type="email"
                        id="registerEmail"
                        placeholder="Masukkan email"
                        required
                    >
                </div>

                <div class="login-form-group">
                    <label for="registerPassword">Password</label>
                    <input
                        type="password"
                        id="registerPassword"
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
                        placeholder="Ulangi password"
                        minlength="6"
                        required
                    >
                </div>

                <div id="registerMessage" class="login-message"></div>

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

            <div class="login-demo-info">
                <strong>Mode Demonstrasi</strong>
                <p>
                    Pendaftaran ini hanya simulasi Front-End.
                    Data akun tidak disimpan.
                </p>
            </div>

        </div>

    </div>

    <script>
        const registerForm = document.getElementById("registerForm");

        registerForm.addEventListener("submit", function(event) {
            event.preventDefault();

            const name = document.getElementById("registerName").value.trim();
            const email = document.getElementById("registerEmail").value.trim();
            const password = document.getElementById("registerPassword").value;
            const confirmPassword = document.getElementById("registerConfirmPassword").value;
            const message = document.getElementById("registerMessage");

            message.className = "login-message";

            if (!name || !email || !password || !confirmPassword) {
                message.textContent = "Semua kolom wajib diisi.";
                message.classList.add("error");
                return;
            }

            if (password.length < 6) {
                message.textContent = "Password minimal 6 karakter.";
                message.classList.add("error");
                return;
            }

            if (password !== confirmPassword) {
                message.textContent = "Konfirmasi password tidak sesuai.";
                message.classList.add("error");
                return;
            }

            message.textContent =
                "Pendaftaran simulasi berhasil! Mengarahkan ke halaman Login...";

            message.classList.add("success");

            registerForm.querySelector("button[type='submit']").disabled = true;

            setTimeout(function() {
                window.location.href = "index.php?page=login";
            }, 1500);
        });
    </script>

</body>
</html>
