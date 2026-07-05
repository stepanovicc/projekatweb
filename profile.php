<?php
require_once __DIR__ . '/db_config.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/php/classes/User.php';

requireLogin();

$pageTitle = 'My Profile';
$userObj   = new User();
$user      = $userObj->getById((int)$_SESSION['user_id']);
$success   = '';
$error     = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid request.';
    } else {
        $data = [
            'first_name' => trim($_POST['first_name'] ?? ''),
            'last_name'  => trim($_POST['last_name']  ?? ''),
            'phone'      => trim($_POST['phone']       ?? ''),
            'bio'        => trim($_POST['bio']         ?? ''),
        ];

        if (!empty($_POST['password'])) {
            if (strlen($_POST['password']) < 8) {
                $error = 'New password must be at least 8 characters.';
            } elseif ($_POST['password'] !== ($_POST['password2'] ?? '')) {
                $error = 'Passwords do not match.';
            } else {
                $data['password'] = $_POST['password'];
            }
        }

        if (!$error) {
            $userObj->updateProfile((int)$_SESSION['user_id'], $data);
            $_SESSION['first_name'] = $data['first_name'];
            $user    = $userObj->getById((int)$_SESSION['user_id']);
            $success = 'Profile updated successfully.';
        }
    }
}

require_once __DIR__ . '/includes/header.php';
?>
<div class="container" style="max-width:640px;">
    <div class="card my-4">
        <div class="card-header py-3 d-flex align-items-center gap-2">
            <i class="bi bi-person-circle fs-5"></i>
            <h4 class="mb-0">My Profile</h4>
        </div>
        <div class="card-body p-4">
            <?php if ($success): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <?= htmlspecialchars($success) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <?= htmlspecialchars($error) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="mb-3 p-3 rounded" style="background:var(--color-surface-2); border:1px solid var(--color-border);">
                <small class="text-muted">Email (username)</small>
                <div class="fw-semibold"><?= htmlspecialchars($user['email']) ?></div>
            </div>

            <form method="POST" class="needs-validation" novalidate>
                <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">

                <div class="row g-3 mb-3">
                    <div class="col-6">
                        <label class="form-label" for="first_name">First Name</label>
                        <input type="text" id="first_name" name="first_name" class="form-control" required
                               value="<?= htmlspecialchars($user['first_name']) ?>">
                    </div>
                    <div class="col-6">
                        <label class="form-label" for="last_name">Last Name</label>
                        <input type="text" id="last_name" name="last_name" class="form-control" required
                               value="<?= htmlspecialchars($user['last_name']) ?>">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label" for="phone">Phone</label>
                    <input type="tel" id="phone" name="phone" class="form-control"
                           value="<?= htmlspecialchars($user['phone'] ?? '') ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label" for="bio">Bio</label>
                    <textarea id="bio" name="bio" class="form-control" rows="4"><?= htmlspecialchars($user['bio'] ?? '') ?></textarea>
                </div>

                <hr style="border-color:var(--color-border)">
                <p class="text-muted small mb-3">Leave password fields empty to keep your current password.</p>

                <div class="mb-3">
                    <label class="form-label" for="password">New Password</label>
                    <input type="password" id="password" name="password" class="form-control" minlength="8">
                </div>
                <div class="mb-3">
                    <label class="form-label" for="password2">Confirm New Password</label>
                    <input type="password" id="password2" name="password2" class="form-control">
                </div>

                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-save me-2"></i>Save Changes
                </button>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
