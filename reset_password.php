<?php
require_once __DIR__ . '/db_config.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/php/classes/User.php';

$pageTitle = 'Set New Password';
$token     = trim($_GET['token'] ?? '');
$error     = '';
$success   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid request.';
    } else {
        $token    = trim($_POST['token'] ?? '');
        $password = $_POST['password'] ?? '';
        $password2 = $_POST['password2'] ?? '';

        if (strlen($password) < 8) {
            $error = 'Password must be at least 8 characters.';
        } elseif ($password !== $password2) {
            $error = 'Passwords do not match.';
        } else {
            $userObj = new User();
            if ($userObj->resetPassword($token, $password)) {
                $success = 'Password changed successfully. You can now log in.';
            } else {
                $error = 'Invalid or expired reset link.';
            }
        }
    }
}

require_once __DIR__ . '/includes/header.php';
?>
<div class="container" style="max-width:440px;">
    <div class="card my-5">
        <div class="card-header py-3">
            <h4 class="mb-0"><i class="bi bi-lock me-2"></i>New Password</h4>
        </div>
        <div class="card-body p-4">
            <?php if ($success): ?>
                <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
                <a href="<?= BASE_URL ?>login.php" class="btn btn-primary w-100">Go to Login</a>
            <?php else: ?>
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                <form method="POST" class="needs-validation" novalidate>
                    <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                    <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
                    <div class="mb-3">
                        <label class="form-label" for="password">New Password</label>
                        <input type="password" id="password" name="password" class="form-control" required minlength="8">
                        <div class="invalid-feedback">Minimum 8 characters.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="password2">Confirm Password</label>
                        <input type="password" id="password2" name="password2" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Set New Password</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
