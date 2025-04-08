<?php
require_once '../../config/Auth.php';
require_once '../../config/Security.php';

$auth = new Auth();
$security = new Security();

if (!$auth->isLoggedIn()) {
    header('Location: ../auth/login.php');
    exit;
}


$pendingCount = $pendingCount ?? 0;
$inProgressCount = $inProgressCount ?? 0;
$pastDueCount = $pastDueCount ?? 0;
$completedCount = $completedCount ?? 0;
$dueToday = $dueToday ?? [];
$pastDue = $pastDue ?? [];


$title = "Create Task";
require_once '../partials/header.php';
?>

<div class="row">
    <?php require_once '../partials/sidebar.php'; ?>
    
    <div class="col-md-9">
        <div class="card">
            <div class="card-header">
                <h5 class="m-0">Create New Task</h5>
            </div>
            <div class="card-body">

            <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($_SESSION['error']); ?></div>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>
                

                <form action="../../controllers/TaskController.php?action=create" method="POST">
                    <input type="hidden" name="csrf_token" value="<?= $security->generateCSRFToken(); ?>">
                    <div class="mb-3">
                        <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="title" name="title" required
                               value="<?= isset($_POST['title']) ? htmlspecialchars($_POST['title']) : '' ?>">
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"><?= isset($_POST['description']) ? htmlspecialchars($_POST['description']) : '' ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="due_date" class="form-label">Due Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="due_date" name="due_date" required 
                               min="<?= date('Y-m-d') ?>"
                               value="<?= isset($_POST['due_date']) ? htmlspecialchars($_POST['due_date']) : '' ?>">
                    </div>
                    <div class="mb-3">
                        <label for="priority" class="form-label">Priority <span class="text-danger">*</span></label>
                        <select class="form-control" id="priority" name="priority" required>
                            <option value="low" <?= (isset($_POST['priority']) && $_POST['priority'] === 'low') ? 'selected' : '' ?>>Low</option>
                            <option value="medium" <?= !isset($_POST['priority']) || (isset($_POST['priority']) && $_POST['priority'] === 'medium') ? 'selected' : '' ?>>Medium</option>
                            <option value="high" <?= (isset($_POST['priority']) && $_POST['priority'] === 'high') ? 'selected' : '' ?>>High</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Create Task</button>
                    <a href="index.php" class="btn btn-secondary">Cancel</a>
                </form>

            </div>
        </div>
    </div>
</div>

<?php require_once '../partials/footer.php'; ?>