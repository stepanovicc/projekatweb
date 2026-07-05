<?php
require_once __DIR__ . '/db_config.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/php/classes/JobListing.php';
require_once __DIR__ . '/php/classes/Category.php';

requireLogin();
requireCompany();

$pageTitle  = 'Post a Job';
$jobListing = new JobListing();
$category   = new Category();
$categories = $category->getAll();
$success    = '';
$errors     = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Invalid request.';
    } else {
        $title       = trim($_POST['title']       ?? '');
        $description = trim($_POST['description'] ?? '');
        $location    = trim($_POST['location']    ?? '');
        $categoryId  = (int)($_POST['category_id'] ?? 0);
        $deadline    = trim($_POST['deadline']    ?? '');

        if (empty($title))       $errors[] = 'Job title is required.';
        if (empty($description)) $errors[] = 'Job description is required.';
        if (empty($location))    $errors[] = 'Location is required.';
        if ($categoryId < 1)     $errors[] = 'Please select a category.';
        if (empty($deadline) || strtotime($deadline) < strtotime('today')) {
            $errors[] = 'Deadline must be today or in the future.';
        }

        if (empty($errors)) {
            $companyId = $jobListing->getCompanyIdByUserId((int)$_SESSION['user_id']);
            if ($companyId) {
                $jobListing->create([
                    'company_id'  => $companyId,
                    'category_id' => $categoryId,
                    'title'       => $title,
                    'description' => $description,
                    'location'    => $location,
                    'deadline'    => $deadline,
                ]);
                $success = 'Job posted successfully! It will go live after admin approval.';
            } else {
                $errors[] = 'Company not found. Please contact support.';
            }
        }
    }
}

require_once __DIR__ . '/includes/header.php';
?>
<div class="container" style="max-width:700px;">
    <div class="card my-4">
        <div class="card-header py-3">
            <h4 class="mb-0"><i class="bi bi-plus-circle me-2"></i>Post a Job</h4>
        </div>
        <div class="card-body p-4">
            <?php if ($success): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <?= htmlspecialchars($success) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php foreach ($errors as $err): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <?= htmlspecialchars($err) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endforeach; ?>

            <form method="POST" class="needs-validation" novalidate>
                <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">

                <div class="mb-3">
                    <label class="form-label" for="title">Job Title *</label>
                    <input type="text" id="title" name="title" class="form-control" required
                           value="<?= htmlspecialchars($_POST['title'] ?? '') ?>" placeholder="e.g. Senior PHP Developer">
                    <div class="invalid-feedback">Required.</div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label" for="category_id">Category *</label>
                        <select id="category_id" name="category_id" class="form-select" required>
                            <option value="">Select category...</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['id'] ?>" <?= (!empty($_POST['category_id']) && $_POST['category_id'] == $cat['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cat['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="invalid-feedback">Select a category.</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="location">Location *</label>
                        <input type="text" id="location" name="location" class="form-control" required
                               value="<?= htmlspecialchars($_POST['location'] ?? '') ?>" placeholder="e.g. Belgrade / Remote">
                        <div class="invalid-feedback">Required.</div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label" for="deadline">Application Deadline *</label>
                    <input type="date" id="deadline" name="deadline" class="form-control" required
                           min="<?= date('Y-m-d') ?>"
                           value="<?= htmlspecialchars($_POST['deadline'] ?? '') ?>">
                    <div class="invalid-feedback">Select a future date.</div>
                </div>

                <div class="mb-3">
                    <label class="form-label" for="description">Job Description *</label>
                    <textarea id="description" name="description" class="form-control" rows="8" required
                              placeholder="Describe the role, responsibilities, requirements..."><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
                    <div class="invalid-feedback">Required.</div>
                </div>

                <div class="alert alert-info mb-3">
                    <i class="bi bi-info-circle me-2"></i>
                    Your job post will be reviewed by an administrator before going live.
                </div>

                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-send me-2"></i>Submit Job Post
                </button>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
