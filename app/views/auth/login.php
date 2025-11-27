<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Smart Green House</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Kustom CSS -->
    <link rel="stylesheet" href="<?= BASE_URL ?>public/assets/css/style.css">
    <style>
        body {
            background-color: #e8f5e9; /* Warna hijau muda */
        }
        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            max-width: 450px;
            width: 100%;
        }
    </style>
</head>
<body>

<div class="login-container">
    <div class="card login-card shadow-sm">
        <div class="card-body p-5">
            <h3 class="card-title text-center mb-4"><i class="bi bi-house-heart-fill"></i> Smart Green House</h3>
            <p class="text-center text-muted mb-4">Silakan login untuk melanjutkan</p>
            
            <?php if (isset($error)): ?>
                <div class="alert alert-danger" role="alert">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['status']) && $_GET['status'] === 'registered'): ?>
                <div class="alert alert-success" role="alert">
                    Registrasi berhasil! Silakan login.
                </div>
            <?php endif; ?>

            <form action="<?= BASE_URL ?>auth/authenticate" method="POST">
                <div class="mb-3">
                    <label for="email" class="form-label">Alamat Email</label>
                    <input type="email" class="form-control" id="email" name="email" required>
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
                    });
                </script>
                <div class="d-grid">
                    <button type="submit" class="btn btn-success">Login</button>
                </div>
            </form>
            <div class="text-center mt-4">
                <p class="text-muted">Belum punya akun? <a href="<?= BASE_URL ?>auth/register">Daftar di sini</a></p>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</body>
</html>
