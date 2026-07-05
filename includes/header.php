<?php
require_once __DIR__ . '/../db_config.php';
require_once __DIR__ . '/auth.php';
$csrfToken = generateCsrfToken();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? sanitize($pageTitle) . ' | IT Job Portal' : 'IT Job Portal' ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>css/style.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark" id="main-nav">
    <div class="container">
        <a class="navbar-brand fw-bold" href="<?= BASE_URL ?>index.php">
            <i class="bi bi-code-slash me-2"></i>IT Jobs
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarMain">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>jobs.php">Browse Jobs</a></li>
            </ul>
            <ul class="navbar-nav ms-auto">
                <?php if (isLoggedIn()): ?>
                    <?php if (isAdmin()): ?>
                        <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>admin/index.php"><i class="bi bi-shield-lock me-1"></i>Admin</a></li>
                    <?php endif; ?>
                    <?php if (isCompany()): ?>
                        <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>post_job.php"><i class="bi bi-plus-circle me-1"></i>Post Job</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>applicants.php"><i class="bi bi-people me-1"></i>Applicants</a></li>
                    <?php endif; ?>
                    <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>profile.php"><i class="bi bi-person-circle me-1"></i><?= sanitize($_SESSION['first_name']) ?></a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>logout.php">Logout</a></li>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>login.php">Login</a></li>
                    <li class="nav-item"><a class="nav-link btn btn-outline-light btn-sm px-3 ms-2" href="<?= BASE_URL ?>register.php">Register</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
<main class="py-4">
