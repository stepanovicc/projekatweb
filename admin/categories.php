<?php
require_once __DIR__ . '/../db_config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../php/classes/Category.php';

requireAdmin();

$pageTitle   = 'Manage Categories';
$categoryObj = new Category();
$message     = '';
$error       = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verifyCsrfToken($_POST['csrf_token'] ?? '')) {
    $action     = $_POST['action'] ?? '';
    $categoryId = (int)($_POST['category_id'] ?? 0);
    $name       = trim($_POST['name'] ?? '');

    if ($action === 'create') {
        if (empty($name)) {
            $error = 'Category name is required.';
        } else {
            $categoryObj->create($name) ? $message = 'Category created.' : $error = 'Name already exists.';
        }
    } elseif ($action === 'update') {
        if (empty($name)) {
            $error = 'Category name is required.';
        } else {
            $categoryObj->update($categoryId, $name) ? $message = 'Category updated.' : $error = 'Failed to update.';
        }
    } elseif ($action === 'delete') {
        $categoryObj->delete($categoryId) ? $message = 'Category deleted.' : $error = 'Cannot delete (jobs may be using it).';
    }
}

$categories = $categoryObj->getAll();

require_once __DIR__ . '/../includes/header.php';
?>
<div class="container" style="max-width:860px;">
    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="<?= BASE_URL ?>admin/index.php" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i>Dashboard
        </a>
        <h1 class="mb-0">Manage Categories</h1>
    </div>

    <?php if ($message): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?= htmlspecialchars($message) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <?= htmlspecialchars($error) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="card mb-4">
        <div class="card-header py-3">
            <h5 class="mb-0"><i class="bi bi-plus-circle me-2"></i>Add Category</h5>
        </div>
        <div class="card-body p-4">
            <form method="POST" class="d-flex gap-3 needs-validation" novalidate>
                <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                <input type="hidden" name="action" value="create">
                <input type="text" name="name" class="form-control" placeholder="Category name..." required>
                <button type="submit" class="btn btn-primary px-4">Add</button>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Slug</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($categories)): ?>
                        <tr><td colspan="4" class="text-center text-muted py-4">No categories yet.</td></tr>
                    <?php else: ?>
                        <?php foreach ($categories as $cat): ?>
                        <tr>
                            <td class="text-muted"><?= $cat['id'] ?></td>
                            <td>
                                <form method="POST" class="d-flex gap-2">
                                    <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                                    <input type="hidden" name="action" value="update">
                                    <input type="hidden" name="category_id" value="<?= $cat['id'] ?>">
                                    <input type="text" name="name" class="form-control form-control-sm"
                                           value="<?= htmlspecialchars($cat['name']) ?>" required style="max-width:200px;">
                                    <button type="submit" class="btn btn-sm btn-outline-secondary">
                                        <i class="bi bi-save"></i>
                                    </button>
                                </form>
                            </td>
                            <td class="text-muted small"><?= htmlspecialchars($cat['slug']) ?></td>
                            <td>
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="category_id" value="<?= $cat['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-secondary"
                                            style="color:var(--color-danger)"
                                            onclick="return confirm('Delete this category?')">
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

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
