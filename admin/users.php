<?php
require_once __DIR__ . '/../db_config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../php/classes/User.php';

requireAdmin();

$pageTitle = 'Manage Users';
$userObj   = new User();
$message   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verifyCsrfToken($_POST['csrf_token'] ?? '')) {
    $action = $_POST['action'] ?? '';
    $userId = (int)($_POST['user_id'] ?? 0);

    if ($action === 'block') {
        $userObj->setBlocked($userId, true);
        $message = 'User blocked.';
    } elseif ($action === 'unblock') {
        $userObj->setBlocked($userId, false);
        $message = 'User unblocked.';
    }
}

$users = $userObj->getAllUsers();

require_once __DIR__ . '/../includes/header.php';
?>
<div class="container-fluid px-4">
    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="<?= BASE_URL ?>admin/index.php" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i>Dashboard
        </a>
        <h1 class="mb-0">Manage Users</h1>
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
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Registered</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($users)): ?>
                            <tr><td colspan="7" class="text-center text-muted py-4">No users found.</td></tr>
                        <?php else: ?>
                            <?php foreach ($users as $u): ?>
                            <tr>
                                <td class="text-muted"><?= $u['id'] ?></td>
                                <td><?= htmlspecialchars($u['first_name'] . ' ' . $u['last_name']) ?></td>
                                <td><?= htmlspecialchars($u['email']) ?></td>
                                <td><span class="badge-pending"><?= htmlspecialchars($u['role']) ?></span></td>
                                <td>
                                    <?php if ($u['is_blocked']): ?>
                                        <span class="badge-inactive">Blocked</span>
                                    <?php elseif ($u['is_active']): ?>
                                        <span class="badge-active">Active</span>
                                    <?php else: ?>
                                        <span class="badge-pending">Pending</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-muted small"><?= htmlspecialchars(date('d.m.Y', strtotime($u['created_at']))) ?></td>
                                <td>
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                                        <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                        <?php if ($u['is_blocked']): ?>
                                            <button type="submit" name="action" value="unblock"
                                                    class="btn btn-sm btn-outline-secondary">
                                                <i class="bi bi-unlock me-1"></i>Unblock
                                            </button>
                                        <?php else: ?>
                                            <button type="submit" name="action" value="block"
                                                    class="btn btn-sm btn-outline-secondary"
                                                    style="color:var(--color-danger)"
                                                    onclick="return confirm('Block this user?')">
                                                <i class="bi bi-lock me-1"></i>Block
                                            </button>
                                        <?php endif; ?>
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
