<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Smart Green House' ?></title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Kustom CSS -->
    <link rel="stylesheet" href="<?= BASE_URL ?>public/assets/css/style.css">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark sticky-top">
    <div class="container-fluid">
        <!-- Hamburger Menu Button (for sidebar) -->
        <button class="navbar-toggler me-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebar" aria-controls="sidebar" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <a class="navbar-brand" href="<?= BASE_URL ?>dashboard"><i class="bi bi-house-heart-fill"></i> Smart Green House</a>
        <!-- Right side user menu (always visible, not collapsible on small screens) -->
        <div class="ms-auto">
            <ul class="navbar-nav">
                 <?php if (isset($_SESSION['user_id'])): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-person-circle"></i> <?= htmlspecialchars($_SESSION['user_nama']) ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="<?= BASE_URL ?>dashboard/profile"><i class="bi bi-person-fill-gear"></i> Ubah Profile</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="<?= BASE_URL ?>auth/logout"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
                        </ul>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<!-- Sidebar Offcanvas Structure -->
<div class="offcanvas offcanvas-start offcanvas-lg sidebar" tabindex="-1" id="sidebar" aria-labelledby="sidebarLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="sidebarLabel">Menu</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" data-bs-target="#sidebar" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
         <?php if (isset($_SESSION['user_id'])): ?>
        <div class="d-grid gap-2"> <!-- Added gap for better spacing -->
            <?php if ($_SESSION['user_role'] === 'admin'): ?>
                <!-- Menu for Admin -->
                <a href="<?= BASE_URL ?>dashboard" class="btn btn-light text-start"><i class="bi bi-speedometer2"></i> Dashboard</a>
                <a href="<?= BASE_URL ?>dashboard/kontrolsensor" class="btn btn-light text-start"><i class="bi bi-toggles"></i> Kontrol Manual</a>
                <a href="<?= BASE_URL ?>dashboard/laporanharian" class="btn btn-light text-start"><i class="bi bi-calendar-check"></i> Laporan Harian</a>
                <a href="<?= BASE_URL ?>user" class="btn btn-light text-start"><i class="bi bi-person-lines-fill"></i> Kelola User</a>
            <?php else: ?>
                <!-- Menu for Petani -->
                <a href="<?= BASE_URL ?>dashboard" class="btn btn-light text-start"><i class="bi bi-speedometer2"></i> Dashboard</a>
                <a href="<?= BASE_URL ?>dashboard/laporanharian" class="btn btn-light text-start"><i class="bi bi-calendar-check"></i> Laporan Harian</a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Main content area -->
<div class="wrapper d-flex">
    <div class="flex-shrink-0 d-none d-lg-block"> <!-- Placeholder for static sidebar width on large screens -->
        <div style="width: 250px;"></div>
    </div>
    <main class="content flex-grow-1">

