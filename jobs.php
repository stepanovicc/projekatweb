<?php
require_once __DIR__ . '/db_config.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/php/classes/JobListing.php';
require_once __DIR__ . '/php/classes/Category.php';

$pageTitle  = 'Browse Jobs';
$jobListing = new JobListing();
$category   = new Category();
$categories = $category->getAll();

$filters = [];
if (!empty($_GET['category_id']))  $filters['category_id']  = (int)$_GET['category_id'];
if (!empty($_GET['location']))     $filters['location']     = $_GET['location'];
if (!empty($_GET['company_name'])) $filters['company_name'] = $_GET['company_name'];
if (!empty($_GET['keyword']))      $filters['keyword']      = $_GET['keyword'];
if (!empty($_GET['show_expired'])) $filters['show_expired'] = $_GET['show_expired'];

if (isLoggedIn() && !empty($_GET['deadline_after'])) {
    $filters['deadline_after'] = $_GET['deadline_after'];
}

$jobs = $jobListing->getAll($filters);

require_once __DIR__ . '/includes/header.php';
?>

<div class="container">
    <h1 class="mb-4">Browse Jobs</h1>

    <div class="search-box mb-5">
        <form id="job-search-form" method="GET" action="jobs.php" novalidate>
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Keyword</label>
                    <input type="text" name="keyword" class="form-control" placeholder="Title, skill, technology..."
                           value="<?= htmlspecialchars($_GET['keyword'] ?? '') ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Category</label>
                    <select name="category_id" class="form-select">
                        <option value="">All Categories</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>" <?= (!empty($_GET['category_id']) && $_GET['category_id'] == $cat['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Location</label>
                    <input type="text" name="location" class="form-control" placeholder="City or remote"
                           value="<?= htmlspecialchars($_GET['location'] ?? '') ?>">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search me-1"></i>Search
                    </button>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Company Name</label>
                    <input type="text" name="company_name" class="form-control" placeholder="Company..."
                           value="<?= htmlspecialchars($_GET['company_name'] ?? '') ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Show</label>
                    <select name="show_expired" class="form-select">
                        <option value="">All Listings</option>
                        <option value="active"  <?= ($_GET['show_expired'] ?? '') === 'active'  ? 'selected' : '' ?>>Active Only</option>
                        <option value="expired" <?= ($_GET['show_expired'] ?? '') === 'expired' ? 'selected' : '' ?>>Expired Only</option>
                    </select>
                </div>
                <?php if (isLoggedIn()): ?>
                <div class="col-md-3">
                    <label class="form-label">Deadline After</label>
                    <input type="date" name="deadline_after" class="form-control"
                           value="<?= htmlspecialchars($_GET['deadline_after'] ?? '') ?>">
                </div>
                <?php endif; ?>
                <div class="col-md-3 d-flex align-items-end">
                    <a href="jobs.php" class="btn btn-outline-secondary w-100">
                        <i class="bi bi-x-circle me-1"></i>Clear
                    </a>
                </div>
            </div>
        </form>
    </div>

    <div id="job-results" class="row g-4">
        <?php if (empty($jobs)): ?>
            <div class="col-12 text-center text-muted py-5">
                <i class="bi bi-search fs-1 d-block mb-3"></i>
                No jobs found. Try different filters.
            </div>
        <?php else: ?>
            <?php foreach ($jobs as $job): ?>
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

<?php require_once __DIR__ . '/includes/footer.php'; ?>
