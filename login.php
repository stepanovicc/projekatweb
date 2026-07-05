<?php
require_once __DIR__ . '/db_config.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/php/classes/User.php';

if (isLoggedIn()) {
    header('Location: ' . BASE_URL . 'index.php');
    exit;
}

$pageTitle = 'Login';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid request.';
    } else {
        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            $error = 'Please fill in all fields.';
        } else {
            $userObj = new User();
            $user    = $userObj->login($email, $password);

            if ($user) {
                setUserSession($user);
                $redirect = isAdmin() ? BASE_URL . 'admin/index.php' : BASE_URL . 'index.php';
                header('Location: ' . $redirect);
                exit;
            } else {
                $error = 'Invalid credentials, account not activated, or account blocked.';
            }
        }
    }
}

require_once __DIR__ . '/includes/header.php';
?>
<div class="container" style="max-width:440px;">
    <div class="card my-5">
        <div class="card-header py-3">
            <h4 class="mb-0"><i class="bi bi-box-arrow-in-right me-2"></i>Sign In</h4>
        </div>
        <div class="card-body p-4">
            <?php if ($error): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= htmlspecialchars($error) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (!empty($_GET['activated'])): ?>
                <div class="alert alert-success">Account activated! You can now log in.</div>
            <?php endif; ?>

            <form method="POST" novalidate class="needs-validation">
                <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">

                <div class="mb-3">
                    <label class="form-label" for="email">Email Address</label>
                    <input type="email" id="email" name="email" class="form-control" required autofocus
                           value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label" for="password">Password</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <small><a href="<?= BASE_URL ?>forgot_password.php">Forgot password?</a></small>
                </div>

                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-box-arrow-in-right me-2"></i>Sign In
                </button>
            </form>

            <p class="text-center small mt-3 mb-0" style="color: var(--color-muted);">
                No account? <a href="<?= BASE_URL ?>register.php">Create one</a>
            </p>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
