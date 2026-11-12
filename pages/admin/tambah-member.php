
<section class="page-header">
    <div>
        <h1>Tambah Member</h1>
        <p>Tambahkan anggota baru ke dalam perpustakaan.</p>
    </div>
</section>

<section class="content-card member-form-card">

    <div class="content-card-header">
        <div>
            <h2>Informasi Member</h2>
            <p>Lengkapi informasi anggota di bawah ini.</p>
        </div>
    </div>

    <form id="memberForm" class="member-form">

        <div class="member-form-grid">

            <div class="form-group">
                <label for="memberName">Nama Lengkap</label>
                <input
                    type="text"
                    id="memberName"
                    name="name"
                    placeholder="Masukkan nama lengkap"
                    required
                >
            </div>

            <div class="form-group">
                <label for="memberEmail">Email</label>
                <input
                    type="email"
                    id="memberEmail"
                    name="email"
                    placeholder="contoh@email.com"
                    required
                >
            </div>

            <div class="form-group">
                <label for="memberPassword">Password</label>
                <input
                    type="password"
                    id="memberPassword"
                    name="password"
                    placeholder="Masukkan password"
                    minlength="6"
                    required
                >
            </div>

            <div class="form-group">
                <label for="confirmPassword">Konfirmasi Password</label>
                <input
                    type="password"
                    id="confirmPassword"
                    name="confirmPassword"
                    placeholder="Ulangi password"
                    minlength="6"
                    required
                >
            </div>

            <div class="form-group">
                <label for="memberRole">Role</label>
                <select id="memberRole" name="role" required>
                    <option value="member">Member</option>
                    <option value="admin">Admin</option>
                </select>
            </div>

        </div>

        <div id="memberFormMessage" class="member-form-message"></div>

        <div class="member-form-actions">
            <a href="index.php?page=kelola-member" class="btn-reset">
                Kembali
            </a>

            <button type="submit" class="btn-primary">
                Simpan Member
            </button>
        </div>

    </form>

</section>
