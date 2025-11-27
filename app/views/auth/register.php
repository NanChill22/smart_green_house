<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi - Smart Green House</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Kustom CSS -->
    <link rel="stylesheet" href="<?= BASE_URL ?>public/assets/css/style.css">
    <style>
        body {
            background-color: #e8f5e9;
        }
        .register-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px 0;
        }
        .register-card {
            max-width: 500px;
            width: 100%;
        }
    </style>
</head>
<body>

<div class="register-container">
    <div class="card register-card shadow-sm">
        <div class="card-body p-5">
            <h3 class="card-title text-center mb-4">Buat Akun Baru</h3>
            
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger" role="alert">
                    <strong>Terjadi kesalahan:</strong>
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?= htmlspecialchars($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form action="<?= BASE_URL ?>auth/store" method="POST">
                <div class="mb-3">
                    <label for="nama" class="form-label">Nama Lengkap</label>
                    <input type="text" class="form-control" id="nama" name="nama" value="<?= htmlspecialchars($input['nama'] ?? '') ?>" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Alamat Email</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($input['email'] ?? '') ?>" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-group position-relative">
                        <input type="password" class="form-control" id="password" name="password" required>
                        <button class="btn btn-toggle-password" type="button" id="togglePassword">
                            <i class="bi bi-eye-slash" id="togglePasswordIcon"></i>
                        </button>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="konfirmasi_password" class="form-label">Konfirmasi Password</label>
                    <div class="input-group position-relative">
                        <input type="password" class="form-control" id="konfirmasi_password" name="konfirmasi_password" required>
                        <button class="btn btn-toggle-password" type="button" id="toggleKonfirmasiPassword">
                            <i class="bi bi-eye-slash" id="toggleKonfirmasiPasswordIcon"></i>
                        </button>
                    </div>
                </div>
                <script>
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
                <div class="d-grid">
                    <button type="submit" class="btn btn-success">Daftar</button>
                </div>
            </form>
            <div class="text-center mt-4">
                <p class="text-muted">Sudah punya akun? <a href="<?= BASE_URL ?>auth/login">Login di sini</a></p>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
