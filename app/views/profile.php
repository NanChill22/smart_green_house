<div class="container-fluid">
    <h1 class="h2 mb-4"><?= htmlspecialchars($title) ?></h1>

    <?php if (isset($_GET['status']) && $_GET['status'] === 'success'): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            Profil berhasil diperbarui!
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <strong>Terjadi kesalahan:</strong>
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <!-- Form Ubah Profil -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">Ubah Data Diri</h5>
        </div>
        <div class="card-body">
            <form action="<?= BASE_URL ?>dashboard/updateProfile" method="POST">
                <div class="mb-3">
                    <label for="nama" class="form-label">Nama</label>
                    <input type="text" class="form-control" id="nama" name="nama" value="<?= htmlspecialchars($user['nama']) ?>" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" readonly>
                    <div class="form-text">Email tidak dapat diubah.</div>
                </div>
                <hr>
                <p class="text-muted">Ubah Password (opsional)</p>
                <div class="mb-3">
                    <label for="password" class="form-label">Password Baru</label>
                    <input type="password" class="form-control" id="password" name="password">
                </div>
                <div class="mb-3">
                    <label for="konfirmasi_password" class="form-label">Konfirmasi Password Baru</label>
                    <input type="password" class="form-control" id="konfirmasi_password" name="konfirmasi_password">
                </div>
                <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Simpan Perubahan</button>
            </form>
        </div>
    </div>

    <!-- Zona Berbahaya -->
    <div class="card text-white bg-danger">
         <div class="card-header">
            <h5 class="card-title mb-0">Zona Berbahaya</h5>
        </div>
        <div class="card-body">
            <p>Tindakan ini tidak dapat diurungkan. Ini akan menghapus akun Anda secara permanen.</p>
            <form action="<?= BASE_URL ?>dashboard/deleteAccount" method="POST" onsubmit="return confirm('Apakah Anda benar-benar yakin ingin menghapus akun Anda? Tindakan ini tidak dapat dibatalkan.');">
                <button type="submit" class="btn btn-light">Hapus Akun Saya</button>
            </form>
        </div>
    </div>
</div>
