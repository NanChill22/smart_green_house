<div class="container-fluid">
    <a href="<?= BASE_URL ?>user" class="btn btn-sm btn-outline-secondary mb-3"><i class="bi bi-arrow-left"></i> Batal</a>
    <h1 class="h2 mb-4"><?= htmlspecialchars($title) ?></h1>

    <div class="card">
        <div class="card-body">
            
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

            <form action="<?= htmlspecialchars($action) ?>" method="POST">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="nama" class="form-label">Nama Lengkap</label>
                            <input type="text" class="form-control" id="nama" name="nama" value="<?= htmlspecialchars($input['nama'] ?? '') ?>" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="email" class="form-label">Alamat Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($input['email'] ?? '') ?>" required>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="role" class="form-label">Role</label>
                    <select class="form-select" id="role" name="role" required>
                        <option value="petani" <?= (isset($input['role']) && $input['role'] === 'petani') ? 'selected' : '' ?>>Petani</option>
                        <option value="admin" <?= (isset($input['role']) && $input['role'] === 'admin') ? 'selected' : '' ?>>Admin</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-group position-relative">
                        <input type="password" class="form-control" id="password" name="password" <?= !$user ? 'required' : '' ?>>
                        <button class="btn btn-toggle-password" type="button" id="togglePassword"><i class="bi bi-eye-slash" id="togglePasswordIcon"></i></button>
                    </div>
                    <div class="form-text"><?= $user ? 'Biarkan kosong jika tidak ingin mengubah password.' : 'Password minimal 8 karakter.' ?></div>
                </div>

                <div class="mb-3">
                    <label for="konfirmasi_password" class="form-label">Konfirmasi Password</label>
                    <div class="input-group position-relative">
                        <input type="password" class="form-control" id="konfirmasi_password" name="konfirmasi_password" <?= !$user ? 'required' : '' ?>>
                        <button class="btn btn-toggle-password" type="button" id="toggleKonfirmasiPassword"><i class="bi bi-eye-slash" id="toggleKonfirmasiPasswordIcon"></i></button>
                    </div>
                </div>
                
                <hr>
                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Script untuk toggle password visibility
document.addEventListener('DOMContentLoaded', function () {
    function setupPasswordToggle(toggleBtnId, passwordInputId, iconId) {
        const toggleButton = document.getElementById(toggleBtnId);
        const passwordInput = document.getElementById(passwordInputId);
        const icon = document.getElementById(iconId);
        if (toggleButton && passwordInput && icon) {
            toggleButton.addEventListener('click', function () {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                icon.classList.toggle('bi-eye');
                icon.classList.toggle('bi-eye-slash');
            });
        }
    }
    setupPasswordToggle('togglePassword', 'password', 'togglePasswordIcon');
    setupPasswordToggle('toggleKonfirmasiPassword', 'konfirmasi_password', 'toggleKonfirmasiPasswordIcon');
});
</script>
