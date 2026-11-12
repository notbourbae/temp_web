
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

            <form id="loginForm">

                <div class="login-form-group">
                    <label for="email">Email</label>
                    <input
                        type="email"
                        id="email"
                        placeholder="Masukkan email"
                        required
                    >
                </div>

                <div class="login-form-group">
                    <label for="password">Password</label>
                    <input
                        type="password"
                        id="password"
                        placeholder="Masukkan password"
                        required
                    >
                </div>

                <div class="login-form-group">
                    <label for="role">Masuk Sebagai</label>
                    <select id="role" required>
                        <option value="member">Member</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>

                <div id="loginMessage" class="login-message"></div>

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

            <div class="login-demo-info">
                <strong>Mode Demonstrasi</strong>
                <p>
                    Login ini hanya simulasi Front-End.
                    Email dan password tidak diperiksa atau disimpan.
                </p>
            </div>

        </div>

    </div>

    <script>
        const loginForm = document.getElementById("loginForm");

        loginForm.addEventListener("submit", function(event) {
            event.preventDefault();

            const email = document.getElementById("email").value.trim();
            const password = document.getElementById("password").value;
            const role = document.getElementById("role").value;
            const message = document.getElementById("loginMessage");

            message.className = "login-message";

            if (!email || !password) {
                message.textContent = "Email dan password wajib diisi.";
                message.classList.add("error");
                return;
            }

            message.textContent = "Login simulasi berhasil. Mengarahkan...";
            message.classList.add("success");

            setTimeout(function() {
                if (role === "admin") {
                    window.location.href = "index.php?page=admin-dashboard&role=admin";
                } else {
                    window.location.href = "index.php?page=dashboard&role=member";
                }
            }, 800);
        });
    </script>

</body>
</html>
