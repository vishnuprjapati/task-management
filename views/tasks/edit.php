<?php
require_once '../../config/Auth.php';
require_once '../../controllers/TaskController.php';

$auth = new Auth();
if (!$auth->isLoggedIn()) {
    header('Location: ../auth/login.php');
    exit;
}

if (!isset($_GET['id'])) {
    $_SESSION['error'] = 'Task ID not provided';
    header('Location: index.php');
    exit;
}

$taskController = new TaskController();
$task = $taskController->show($_GET['id'])['task'];

if (!$task || (!$auth->isAdmin() && $task['user_id'] != $_SESSION['user_id'])) {
    $_SESSION['error'] = 'Task not found or unauthorized';
    header('Location: index.php');
    exit;
}

$title = "Edit Task";
require_once '../partials/header.php';
?>

<div class="row">
    <?php require_once '../partials/sidebar.php'; ?>
    
    <div class="col-md-9">
        <div class="card">
            <div class="card-header">
                <h5 class="m-0">Edit Task</h5>
            </div>
            <div class="card-body">
                <form action="../../controllers/TaskController.php?action=update&id=<?php echo $task['id']; ?>" method="POST">
                    <div class="mb-3">
                        <label for="title" class="form-label">Title</label>
                        <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($task['title']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"><?php echo htmlspecialchars($task['description']); ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="due_date" class="form-label">Due Date</label>
                        <input type="date" class="form-control" id="due_date" name="due_date" value="<?php echo htmlspecialchars($task['due_date']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="priority" class="form-label">Priority</label>
                        <select class="form-control" id="priority" name="priority" required>
                            <option value="low" <?php echo $task['priority'] === 'low' ? 'selected' : ''; ?>>Low</option>
                            <option value="medium" <?php echo $task['priority'] === 'medium' ? 'selected' : ''; ?>>Medium</option>
                            <option value="high" <?php echo $task['priority'] === 'high' ? 'selected' : ''; ?>>High</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-control" id="status" name="status" required>
                            <option value="pending" <?php echo $task['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                            <option value="in_progress" <?php echo $task['status'] === 'in_progress' ? 'selected' : ''; ?>>In Progress</option>
                            <option value="past_due" <?php echo $task['status'] === 'past_due' ? 'selected' : ''; ?>>Past Due</option>
                            <option value="completed" <?php echo $task['status'] === 'completed' ? 'selected' : ''; ?>>Completed</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Update Task</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once '../partials/footer.php'; ?>