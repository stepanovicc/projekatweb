<?php
require_once __DIR__ . '/db_config.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/php/classes/User.php';
require_once __DIR__ . '/php/classes/Mailer.php';

$pageTitle = 'Forgot Password';
$message   = '';
$error     = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid request.';
    } else {
        $email   = trim($_POST['email'] ?? '');
        $userObj = new User();
        $result  = $userObj->generateResetToken($email);

        if ($result) {
            $mailer = new Mailer();
            $mailer->sendPasswordReset($email, 'User', $result['token']);
        }
        $message = 'If that email is registered, a reset link has been sent.';
    }
}

require_once __DIR__ . '/includes/header.php';
?>
<div class="container" style="max-width:440px;">
    <div class="card my-5">
        <div class="card-header py-3">
            <h4 class="mb-0"><i class="bi bi-key me-2"></i>Reset Password</h4>
        </div>
        <div class="card-body p-4">
            <?php if ($message): ?>
                <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST" class="needs-validation" novalidate>
                <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                <div class="mb-3">
                    <label class="form-label" for="email">Your Email Address</label>
                    <input type="email" id="email" name="email" class="form-control" required autofocus>
                    <div class="invalid-feedback">Enter a valid email.</div>
                </div>
                <button type="submit" class="btn btn-primary w-100">Send Reset Link</button>
            </form>
            <p class="text-center text-muted small mt-3 mb-0">
                <a href="<?= BASE_URL ?>login.php">Back to Login</a>
            </p>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
