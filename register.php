<?php
require_once __DIR__ . '/db_config.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/php/classes/User.php';
require_once __DIR__ . '/php/classes/Mailer.php';

if (isLoggedIn()) {
    header('Location: ' . BASE_URL . 'index.php');
    exit;
}

$pageTitle = 'Register';
$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Invalid request. Please try again.';
    } else {
        $firstName   = trim($_POST['first_name'] ?? '');
        $lastName    = trim($_POST['last_name'] ?? '');
        $email       = trim($_POST['email'] ?? '');
        $password    = $_POST['password'] ?? '';
        $password2   = $_POST['password2'] ?? '';
        $role        = ($_POST['role'] ?? '') === 'company' ? 'company' : 'user';
        $companyName = trim($_POST['company_name'] ?? '');
        $website     = trim($_POST['website'] ?? '');
        $address     = trim($_POST['address'] ?? '');
        $companyDesc = trim($_POST['company_description'] ?? '');

        if (empty($firstName) || empty($lastName)) $errors[] = 'First and last name are required.';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Enter a valid email address.';
        if (strlen($password) < 8) $errors[] = 'Password must be at least 8 characters.';
        if ($password !== $password2) $errors[] = 'Passwords do not match.';
        if ($role === 'company' && empty($companyName)) $errors[] = 'Company name is required.';

        if (empty($errors)) {
            $userObj = new User();
            $result  = $userObj->register([
                'email'               => $email,
                'password'            => $password,
                'first_name'          => $firstName,
                'last_name'           => $lastName,
                'role'                => $role,
                'company_name'        => $companyName,
                'website'             => $website,
                'address'             => $address,
                'company_description' => $companyDesc,
            ]);

            if ($result['success']) {
                $mailer = new Mailer();
                $mailer->sendActivation($email, $firstName . ' ' . $lastName, $result['token']);
                $success = 'Registration successful! Check your email to activate your account.';
            } else {
                $errors[] = $result['message'];
            }
        }
    }
}

require_once __DIR__ . '/includes/header.php';
?>
<div class="container" style="max-width:560px;">
    <div class="card my-4">
        <div class="card-header py-3">
            <h4 class="mb-0"><i class="bi bi-person-plus me-2"></i>Create Account</h4>
        </div>
        <div class="card-body p-4">

            <?php if ($success): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= htmlspecialchars($success) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php foreach ($errors as $err): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= htmlspecialchars($err) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endforeach; ?>

            <?php if (!$success): ?>
            <form method="POST" novalidate class="needs-validation">
                <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">

                <div class="row g-3 mb-3">
                    <div class="col-6">
                        <label class="form-label" for="first_name">First Name</label>
                        <input type="text" id="first_name" name="first_name" class="form-control" required
                               value="<?= htmlspecialchars($_POST['first_name'] ?? '') ?>">
                        <div class="invalid-feedback">Required.</div>
                    </div>
                    <div class="col-6">
                        <label class="form-label" for="last_name">Last Name</label>
                        <input type="text" id="last_name" name="last_name" class="form-control" required
                               value="<?= htmlspecialchars($_POST['last_name'] ?? '') ?>">
                        <div class="invalid-feedback">Required.</div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label" for="email">Email Address</label>
                    <input type="email" id="email" name="email" class="form-control" required
                           value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                    <div id="email-feedback" class="mt-1"></div>
                    <div class="invalid-feedback">Enter a valid email.</div>
                </div>

                <div class="mb-3">
                    <label class="form-label" for="password">Password</label>
                    <input type="password" id="password" name="password" class="form-control" required minlength="8">
                    <div class="progress mt-2" style="height:5px; background:var(--color-border);">
                        <div id="password-strength" class="progress-bar" style="width:0%; transition:width 0.3s;"></div>
                    </div>
                    <div class="invalid-feedback">Minimum 8 characters.</div>
                </div>

                <div class="mb-3">
                    <label class="form-label" for="password2">Confirm Password</label>
                    <input type="password" id="password2" name="password2" class="form-control" required>
                    <div class="invalid-feedback">Required.</div>
                </div>

                <div class="mb-3">
                    <label class="form-label" for="role">Account Type</label>
                    <select id="role" name="role" class="form-select" required>
                        <option value="user" <?= ($_POST['role'] ?? '') !== 'company' ? 'selected' : '' ?>>Individual / Job Seeker</option>
                        <option value="company" <?= ($_POST['role'] ?? '') === 'company' ? 'selected' : '' ?>>Company / Employer</option>
                    </select>
                </div>

                <div id="company-fields" class="<?= ($_POST['role'] ?? '') === 'company' ? '' : 'd-none' ?>">
                    <hr style="border-color:var(--color-border)">
                    <p class="text-muted small mb-3">Company Information</p>

                    <div class="mb-3">
                        <label class="form-label" for="company_name">Company Name *</label>
                        <input type="text" id="company_name" name="company_name" class="form-control"
                               value="<?= htmlspecialchars($_POST['company_name'] ?? '') ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="website">Website</label>
                        <input type="url" id="website" name="website" class="form-control"
                               value="<?= htmlspecialchars($_POST['website'] ?? '') ?>" placeholder="https://...">
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="address">Address</label>
                        <input type="text" id="address" name="address" class="form-control"
                               value="<?= htmlspecialchars($_POST['address'] ?? '') ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="company_description">About Company</label>
                        <textarea id="company_description" name="company_description" class="form-control" rows="3"><?= htmlspecialchars($_POST['company_description'] ?? '') ?></textarea>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100 mt-2">
                    <i class="bi bi-check2-circle me-2"></i>Create Account
                </button>
            </form>
            <?php endif; ?>

            <p class="text-center small mt-3 mb-0" style="color: var(--color-muted);">
                Already have an account? <a href="<?= BASE_URL ?>login.php">Log in</a>
            </p>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
