<?php
require_once __DIR__ . '/db_config.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/php/classes/JobListing.php';
require_once __DIR__ . '/php/classes/Category.php';

$pageTitle = 'Find Your Next IT Job';
$jobListing = new JobListing();
$category = new Category();

$latestJobs  = $jobListing->getAll(['show_expired' => 'active'], false);
$latestJobs  = array_slice($latestJobs, 0, 6);
$categories  = $category->getAll();
$totalJobs   = count($jobListing->getAll());

require_once __DIR__ . '/includes/header.php';
?>

<section class="hero">
    <div class="container">
        <h1>Your next IT career<br>starts here.</h1>
        <p class="lead">Browse hundreds of tech jobs from companies that move fast. Apply in minutes.</p>
        <a href="jobs.php" class="btn btn-primary btn-lg px-5 me-2">
            <i class="bi bi-search me-2"></i>Browse Jobs
        </a>
        <?php if (!isLoggedIn()): ?>
        <a href="register.php" class="btn btn-outline-light btn-lg px-5">
            <i class="bi bi-person-plus me-2"></i>Post a Job
        </a>
        <?php elseif (isCompany()): ?>
        <a href="post_job.php" class="btn btn-outline-light btn-lg px-5">
            <i class="bi bi-plus-circle me-2"></i>Post a Job
        </a>
        <?php endif; ?>
    </div>
</section>

<section class="py-4 border-bottom" style="border-color: var(--color-border) !important;">
    <div class="container">
        <div class="row g-4 text-center">
            <div class="col-6 col-md-3">
                <div class="fs-2 fw-bold" style="color:var(--color-accent-2)"><?= $totalJobs ?>+</div>
                <div class="small" style="color:#cbd5e1">Active Jobs</div>
            </div>
            <div class="col-6 col-md-3">
                <div class="fs-2 fw-bold" style="color:var(--color-accent-2)"><?= count($categories) ?></div>
                <div class="small" style="color:#cbd5e1">Categories</div>
            </div>
            <div class="col-6 col-md-3">
                <div class="fs-2 fw-bold" style="color:var(--color-accent-2)">100%</div>
                <div class="small" style="color:#cbd5e1">Free to Apply</div>
            </div>
            <div class="col-6 col-md-3">
                <div class="fs-2 fw-bold" style="color:var(--color-accent-2)">24/7</div>
                <div class="small" style="color:#cbd5e1">Always Online</div>
            </div>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <h2 class="mb-0">Latest Openings</h2>
            <a href="jobs.php" class="btn btn-outline-secondary btn-sm">View all <i class="bi bi-arrow-right ms-1"></i></a>
        </div>
        <div class="row g-4">
            <?php if (empty($latestJobs)): ?>
                <div class="col-12 text-center text-muted py-5">No jobs posted yet. Be the first to post!</div>
            <?php else: ?>
                <?php foreach ($latestJobs as $job): ?>
                <?php $expired = strtotime($job['deadline']) < time(); ?>
                <div class="col-md-6 col-lg-4">
                    <div class="job-card h-100 <?= $expired ? 'expired' : '' ?>">
                        <?php if ($expired): ?><span class="expired-label">Expired</span><?php endif; ?>
                        <span class="badge-category mb-2 d-inline-block"><?= htmlspecialchars($job['category_name']) ?></span>
                        <h5 class="mb-1"><?= htmlspecialchars($job['title']) ?></h5>
                        <div class="company-name mb-2"><?= htmlspecialchars($job['company_name']) ?></div>
                        <div class="job-meta mb-3">
                            <i class="bi bi-geo-alt me-1"></i><?= htmlspecialchars($job['location']) ?>
                            &nbsp;·&nbsp;
                            <i class="bi bi-calendar3 me-1"></i><?= htmlspecialchars($job['deadline']) ?>
                        </div>
                        <a href="job.php?id=<?= $job['id'] ?>" class="btn btn-sm btn-outline-secondary w-100">View Details</a>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<section class="py-5" style="background: var(--color-surface); border-top: 1px solid var(--color-border);">
    <div class="container">
        <h2 class="mb-4">Browse by Category</h2>
        <div class="row g-3">
            <?php foreach ($categories as $cat): ?>
            <div class="col-6 col-md-4 col-lg-3">
                <a href="jobs.php?category_id=<?= $cat['id'] ?>" class="d-block text-decoration-none">
                    <div class="card p-3 text-center h-100 category-card">
                        <i class="bi bi-code-slash fs-3 mb-2" style="color:var(--color-accent-2)"></i>
                        <div class="fw-semibold"><?= htmlspecialchars($cat['name']) ?></div>
                    </div>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
