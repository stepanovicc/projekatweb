<?php
require_once __DIR__ . '/../db_config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../php/classes/JobListing.php';

requireAdmin();

$pageTitle  = 'Manage Jobs';
$jobListing = new JobListing();
$message    = '';
$error      = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verifyCsrfToken($_POST['csrf_token'] ?? '')) {
    $action = $_POST['action'] ?? '';
    $jobId  = (int)($_POST['job_id'] ?? 0);

    if ($action === 'activate') {
        $jobListing->setActive($jobId, true);
        $message = 'Job listing activated.';
    } elseif ($action === 'deactivate') {
        $jobListing->setActive($jobId, false);
        $message = 'Job listing deactivated.';
    } elseif ($action === 'delete') {
        $jobListing->delete($jobId);
        $message = 'Job listing deleted.';
    }
}

$jobs = $jobListing->getAll([], true);

require_once __DIR__ . '/../includes/header.php';
?>
<div class="container-fluid px-4">
    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="<?= BASE_URL ?>admin/index.php" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i>Dashboard
        </a>
        <h1 class="mb-0">Manage Jobs</h1>
    </div>

    <?php if ($message): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?= htmlspecialchars($message) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Title</th>
                            <th>Company</th>
                            <th>Category</th>
                            <th>Deadline</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($jobs)): ?>
                            <tr><td colspan="7" class="text-center text-muted py-4">No job listings found.</td></tr>
                        <?php else: ?>
                            <?php foreach ($jobs as $job): ?>
                            <?php $expired = strtotime($job['deadline']) < time(); ?>
                            <tr>
                                <td class="text-muted"><?= $job['id'] ?></td>
                                <td>
                                    <a href="<?= BASE_URL ?>job.php?id=<?= $job['id'] ?>" target="_blank">
                                        <?= htmlspecialchars($job['title']) ?>
                                    </a>
                                    <?php if ($expired): ?>
                                        <span class="badge-inactive ms-1">Expired</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($job['company_name']) ?></td>
                                <td><?= htmlspecialchars($job['category_name']) ?></td>
                                <td class="<?= $expired ? 'text-danger' : '' ?>"><?= htmlspecialchars($job['deadline']) ?></td>
                                <td>
                                    <?php if ($job['is_active']): ?>
                                        <span class="badge-active">Active</span>
                                    <?php else: ?>
                                        <span class="badge-pending">Pending</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                                        <input type="hidden" name="job_id" value="<?= $job['id'] ?>">
                                        <?php if ($job['is_active']): ?>
                                            <button type="submit" name="action" value="deactivate"
                                                    class="btn btn-sm btn-outline-secondary me-1"
                                                    onclick="return confirm('Deactivate this job?')">
                                                <i class="bi bi-pause-fill"></i>
                                            </button>
                                        <?php else: ?>
                                            <button type="submit" name="action" value="activate"
                                                    class="btn btn-sm btn-outline-secondary me-1">
                                                <i class="bi bi-play-fill"></i> Approve
                                            </button>
                                        <?php endif; ?>
                                        <button type="submit" name="action" value="delete"
                                                class="btn btn-sm btn-outline-secondary"
                                                style="color:var(--color-danger)"
                                                onclick="return confirm('Delete this job permanently?')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
