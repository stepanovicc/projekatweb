<?php
require_once __DIR__ . '/db_config.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/php/classes/User.php';

$token  = trim($_GET['token'] ?? '');
$result = false;

if ($token) {
    $userObj = new User();
    $result  = $userObj->activate($token);
}

require_once __DIR__ . '/includes/header.php';
$pageTitle = 'Account Activation';
?>
<div class="container" style="max-width:460px;">
    <div class="card my-5 text-center">
        <div class="card-body p-5">
            <?php if ($result): ?>
                <i class="bi bi-check-circle-fill fs-1 mb-3" style="color:var(--color-success)"></i>
                <h4>Account Activated!</h4>
                <p class="text-muted">Your account is ready. You can now log in.</p>
                <a href="<?= BASE_URL ?>login.php?activated=1" class="btn btn-primary">Go to Login</a>
            <?php else: ?>
                <i class="bi bi-x-circle-fill fs-1 mb-3" style="color:var(--color-danger)"></i>
                <h4>Invalid or Expired Link</h4>
                <p class="text-muted">The activation link is invalid or has already been used.</p>
                <a href="<?= BASE_URL ?>index.php" class="btn btn-outline-secondary">Go Home</a>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
