<?php
require_once __DIR__ . '/../db_config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../php/classes/User.php';
require_once __DIR__ . '/../php/classes/JobListing.php';
require_once __DIR__ . '/../php/classes/Category.php';

requireAdmin();

$pageTitle  = 'Admin Dashboard';
$userObj    = new User();
$jobListing = new JobListing();
$category   = new Category();

$totalUsers    = count($userObj->getAllUsers());
$allJobs       = $jobListing->getAll([], true);
$totalJobs     = count($allJobs);
$pendingJobs   = count(array_filter($allJobs, fn($j) => !$j['is_active']));
$totalCategories = count($category->getAll());

require_once __DIR__ . '/../includes/header.php';
?>
<div class="container-fluid px-4">
    <h1 class="mb-4"><i class="bi bi-shield-lock me-2"></i>Admin Panel</h1>

<div class="row g-4 mb-5">
    <div class="col-sm-6 col-xl-3">
        <div class="card p-4 text-center">
            <div class="fs-1 fw-bold" style="color:var(--color-accent-2)"><?= $totalUsers ?></div>
            <div style="color:#fff">Registered Users</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card p-4 text-center">
            <div class="fs-1 fw-bold" style="color:var(--color-accent-2)"><?= $totalJobs ?></div>
            <div style="color:#fff">Total Job Posts</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card p-4 text-center">
            <div class="fs-1 fw-bold" style="color:var(--color-warning)"><?= $pendingJobs ?></div>
            <div style="color:#fff">Pending Approval</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card p-4 text-center">
            <div class="fs-1 fw-bold" style="color:var(--color-accent-2)"><?= $totalCategories ?></div>
            <div style="color:#fff">Categories</div>
        </div>
    </div>
</div>

    <div class="row g-3">
        <div class="col-md-4">
            <a href="<?= BASE_URL ?>admin/jobs.php" class="card p-4 text-decoration-none d-flex flex-row align-items-center gap-3">
                <i class="bi bi-briefcase fs-2" style="color:var(--color-accent-2)"></i>
                <div>
                    <div class="fw-semibold">Manage Jobs</div>
                    <div style="color:#fff">Approve, activate, deactivate</div>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="<?= BASE_URL ?>admin/users.php" class="card p-4 text-decoration-none d-flex flex-row align-items-center gap-3">
                <i class="bi bi-people fs-2" style="color:var(--color-accent-2)"></i>
                <div>
                    <div class="fw-semibold">Manage Users</div>
                    <div style="color:#fff" >Block or approve accounts</div>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="<?= BASE_URL ?>admin/categories.php" class="card p-4 text-decoration-none d-flex flex-row align-items-center gap-3">
                <i class="bi bi-tags fs-2" style="color:var(--color-accent-2)"></i>
                <div>
                    <div class="fw-semibold">Manage Categories</div>
                    <div style="color:#fff" >Add, edit, delete categories</div>
                </div>
            </a>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
