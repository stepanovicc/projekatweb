<?php
require_once __DIR__ . '/db_config.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/php/classes/JobListing.php';
require_once __DIR__ . '/php/classes/JobApplication.php';

requireLogin();
requireCompany();

$pageTitle      = 'Applicants';
$jobListing     = new JobListing();
$applicationObj = new JobApplication();
$companyId      = $jobListing->getCompanyIdByUserId((int)$_SESSION['user_id']);
$jobs           = $companyId ? $jobListing->getByCompany($companyId) : [];

require_once __DIR__ . '/includes/header.php';
?>
<div class="container">
    <h1 class="mb-4"><i class="bi bi-people me-2"></i>Applicants</h1>

    <?php if (empty($jobs)): ?>
        <div class="card p-4 text-center text-muted">
            You haven't posted any jobs yet. <a href="<?= BASE_URL ?>post_job.php">Post one</a> to start receiving applications.
        </div>
    <?php else: ?>
        <?php foreach ($jobs as $job): ?>
        <?php $applicants = $applicationObj->getApplicantsForJob((int)$job['id']); ?>
        <div class="card mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h5 class="mb-0"><?= htmlspecialchars($job['title']) ?></h5>
                <span class="badge-category"><?= count($applicants) ?> applicant<?= count($applicants) === 1 ? '' : 's' ?></span>
            </div>
            <div class="card-body p-0">
                <?php if (empty($applicants)): ?>
                    <div class="text-center text-muted py-4">No applications yet.</div>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Applied</th>
                                <th>Contact</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($applicants as $applicant): ?>
                            <tr>
                                <td><?= htmlspecialchars($applicant['first_name'] . ' ' . $applicant['last_name']) ?></td>
                                <td><?= htmlspecialchars($applicant['email']) ?></td>
                                <td><?= htmlspecialchars($applicant['phone'] ?? '') ?: '—' ?></td>
                                <td class="text-muted small"><?= htmlspecialchars(date('d.m.Y', strtotime($applicant['applied_at']))) ?></td>
                                <td>
                                    <a href="mailto:<?= htmlspecialchars($applicant['email']) ?>?subject=<?= urlencode('Regarding your application: ' . $job['title']) ?>"
                                       class="btn btn-sm btn-outline-secondary">
                                        <i class="bi bi-envelope me-1"></i>Email
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
