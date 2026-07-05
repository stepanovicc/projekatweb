<?php
require_once __DIR__ . '/db_config.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/php/classes/JobListing.php';
require_once __DIR__ . '/php/classes/JobApplication.php';

$id  = (int)($_GET['id'] ?? 0);
$job = (new JobListing())->getById($id);

if (!$job || (!$job['is_active'] && !isAdmin())) {
    http_response_code(404);
    require_once __DIR__ . '/includes/header.php';
    echo '<div class="container py-5 text-center"><h2>Job not found.</h2><a href="jobs.php" class="btn btn-outline-secondary mt-3">Browse Jobs</a></div>';
    require_once __DIR__ . '/includes/footer.php';
    exit;
}

$expired    = strtotime($job['deadline']) < time();
$pageTitle  = htmlspecialchars($job['title']);
$canApply   = isLoggedIn() && !isAdmin() && !isCompany() && !$expired;
$hasApplied = $canApply && (new JobApplication())->hasApplied($id, (int)$_SESSION['user_id']);

require_once __DIR__ . '/includes/header.php';
?>
<div class="container" style="max-width:860px;">
    <div class="mb-3">
        <a href="jobs.php" class="text-decoration-none small" style="color:#cbd5e1">
            <i class="bi bi-arrow-left me-1"></i>Back to Jobs
        </a>
    </div>

    <div class="card mb-4">
        <div class="card-body p-4 p-md-5">
            <?php if ($expired): ?>
                <div class="alert alert-warning mb-4">
                    <i class="bi bi-clock-history me-2"></i>This job listing has expired (deadline: <?= htmlspecialchars($job['deadline']) ?>).
                </div>
            <?php endif; ?>

            <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
                <div>
                    <span class="badge-category mb-2 d-inline-block"><?= htmlspecialchars($job['category_name']) ?></span>
                    <h1 class="h2 mb-1"><?= htmlspecialchars($job['title']) ?></h1>
                    <div class="fs-5 fw-semibold mb-1"><?= htmlspecialchars($job['company_name']) ?></div>
                    <div class="small" style="color:#cbd5e1">
                        <i class="bi bi-geo-alt me-1"></i><?= htmlspecialchars($job['location']) ?>
                        &nbsp;·&nbsp;
                        <i class="bi bi-calendar3 me-1"></i>Deadline: <?= htmlspecialchars($job['deadline']) ?>
                    </div>
                </div>
                <?php if (isLoggedIn() && !isAdmin()): ?>
                <div class="d-flex flex-wrap gap-2">
                    <?php if ($canApply): ?>
                        <button type="button" id="apply-btn" class="btn btn-primary"
                                data-job-id="<?= $job['id'] ?>" data-csrf-token="<?= $csrfToken ?>"
                                <?= $hasApplied ? 'disabled' : '' ?>>
                            <i class="bi bi-send-check me-2"></i><span id="apply-btn-label"><?= $hasApplied ? 'Already Applied' : 'Apply Now' ?></span>
                        </button>
                    <?php endif; ?>
                    <a href="mailto:<?= htmlspecialchars($job['company_email']) ?>?subject=Application: <?= urlencode($job['title']) ?>"
                       class="btn btn-outline-secondary">
                        <i class="bi bi-envelope me-2"></i>Contact Company
                    </a>
                </div>
                <div id="apply-feedback" class="mt-2 small"></div>
                <?php endif; ?>
            </div>

            <hr style="border-color:var(--color-border)">

            <h5 class="mb-3">Job Description</h5>
            <div class="lh-lg" style="color:#cbd5e1">
                <?= nl2br(htmlspecialchars($job['description'])) ?>
            </div>

            <hr style="border-color:var(--color-border)" class="mt-4">
            <h5 class="mb-3">About <?= htmlspecialchars($job['company_name']) ?></h5>
            <div class="row g-3 small" style="color:#cbd5e1">
                <?php if ($job['company_description']): ?>
                <div class="col-12"><?= nl2br(htmlspecialchars($job['company_description'])) ?></div>
                <?php endif; ?>
                <?php if ($job['website']): ?>
                <div class="col-md-6">
                    <i class="bi bi-globe me-1"></i>
                    <a href="<?= htmlspecialchars($job['website']) ?>" target="_blank" rel="noopener">
                        <?= htmlspecialchars($job['website']) ?>
                    </a>
                </div>
                <?php endif; ?>
                <?php if ($job['address']): ?>
                <div class="col-md-6">
                    <i class="bi bi-building me-1"></i><?= htmlspecialchars($job['address']) ?>
                </div>
                <?php endif; ?>
            </div>

            <?php if (!isLoggedIn()): ?>
            <div class="alert alert-info mt-4">
                <i class="bi bi-info-circle me-2"></i>
                <a href="<?= BASE_URL ?>login.php">Log in</a> or <a href="<?= BASE_URL ?>register.php">register</a> to contact the company.
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
