<?php
require_once  '../../config/Auth.php';
require_once  '../../controllers/TaskController.php';

$auth = new Auth();
if (!$auth->isLoggedIn()) {
    header('Location: ../auth/login.php');
    exit;
}

$taskController = new TaskController();
$data = $taskController->index();

extract($data);

$title = "Tasks";
require_once '../partials/header.php';
?>

<div class="row">
    <?php require_once '../partials/sidebar.php'; ?>
    
    <div class="col-md-9">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="m-0">Tasks</h5>
                <div>
                    <a href="create.php" class="btn btn-primary btn-sm">Create Task</a>
                </div>
            </div>
            <div class="card-body">
                <form method="GET" class="mb-4">
                    <div class="row">
                        <div class="col-md-4">
                            <input type="text" name="search" class="form-control" placeholder="Search tasks..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                        </div>
                        <div class="col-md-3">
                            <select name="status" class="form-control">
                                <option value="">All Statuses</option>
                                <option value="pending" <?php echo isset($_GET['status']) && $_GET['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                <option value="in_progress" <?php echo isset($_GET['status']) && $_GET['status'] === 'in_progress' ? 'selected' : ''; ?>>In Progress</option>
                                <option value="past_due" <?php echo isset($_GET['status']) && $_GET['status'] === 'past_due' ? 'selected' : ''; ?>>Past Due</option>
                                <option value="completed" <?php echo isset($_GET['status']) && $_GET['status'] === 'completed' ? 'selected' : ''; ?>>Completed</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="priority" class="form-control">
                                <option value="">All Priorities</option>
                                <option value="low" <?php echo isset($_GET['priority']) && $_GET['priority'] === 'low' ? 'selected' : ''; ?>>Low</option>
                                <option value="medium" <?php echo isset($_GET['priority']) && $_GET['priority'] === 'medium' ? 'selected' : ''; ?>>Medium</option>
                                <option value="high" <?php echo isset($_GET['priority']) && $_GET['priority'] === 'high' ? 'selected' : ''; ?>>High</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">Filter</button>
                        </div>
                    </div>
                </form>

                <?php if (empty($tasks)): ?>
                    <div class="alert alert-info">No tasks found.</div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Due Date</th>
                                    <th>Priority</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($tasks as $task): ?>
                                    <tr>
                                        <td>
                                            <a href="show.php?id=<?php echo $task['id']; ?>">
                                                <?php echo htmlspecialchars($task['title']); ?>
                                            </a>
                                        </td>
                                        <td><?php echo htmlspecialchars($task['due_date']); ?></td>
                                        <td>
                                            <span class="badge bg-<?php echo $task['priority'] === 'high' ? 'danger' : ($task['priority'] === 'medium' ? 'warning' : 'primary'); ?>">
                                                <?php echo ucfirst($task['priority']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?php 
                                                echo $task['status'] === 'completed' ? 'success' : 
                                                    ($task['status'] === 'in_progress' ? 'info' : 
                                                        ($task['status'] === 'past_due' ? 'danger' : 'secondary')); ?>">
                                                <?php echo ucfirst(str_replace('_', ' ', $task['status'])); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="edit.php?id=<?php echo $task['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                                            <a href="../../controllers/TaskController.php?action=delete&id=<?php echo $task['id']; ?>" 
                                               class="btn btn-sm btn-danger" 
                                               onclick="return confirm('Are you sure you want to delete this task?')">Delete</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once '../partials/footer.php'; ?>