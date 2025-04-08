<?php
require_once '../config/Auth.php';
require_once '../controllers/TaskController.php';

$auth = new Auth();
if (!$auth->isLoggedIn()) {
    header('Location: ../views/auth/login.php');
    exit;
}

$taskController = new TaskController();
$data = $taskController->index();

extract($data);

$title = "Dashboard";
require_once '../views/partials/header.php';
?>

<div class="row">
    <?php require_once '../views/partials/sidebar.php'; ?>
    
    <div class="col-md-9">
        <div class="row">
            <div class="col-md-3">
                <div class="card text-white bg-primary mb-3">
                    <div class="card-header">Pending</div>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $pendingCount; ?></h5>
                        <p class="card-text">Tasks waiting to be started</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-info mb-3">
                    <div class="card-header">In Progress</div>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $inProgressCount; ?></h5>
                        <p class="card-text">Tasks currently being worked on</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-danger mb-3">
                    <div class="card-header">Past Due</div>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $pastDueCount; ?></h5>
                        <p class="card-text">Tasks that missed their deadline</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-success mb-3">
                    <div class="card-header">Completed</div>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $completedCount; ?></h5>
                        <p class="card-text">Tasks successfully completed</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="m-0">Recent Tasks</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($tasks)): ?>
                            <div class="alert alert-info">No tasks found.</div>
                        <?php else: ?>
                            <div class="list-group">
                                <?php foreach (array_slice($tasks, 0, 5) as $task): ?>
                                    <a href="../views/tasks/show.php?id=<?php echo $task['id']; ?>" class="list-group-item list-group-item-action">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h6 class="mb-1"><?php echo htmlspecialchars($task['title']); ?></h6>
                                            <small class="text-<?php 
                                                echo $task['status'] === 'completed' ? 'success' : 
                                                    ($task['status'] === 'in_progress' ? 'info' : 
                                                        ($task['status'] === 'past_due' ? 'danger' : 'secondary')); ?>">
                                                <?php echo ucfirst(str_replace('_', ' ', $task['status'])); ?>
                                            </small>
                                        </div>
                                        <p class="mb-1"><?php echo htmlspecialchars(substr($task['description'], 0, 50)); ?>...</p>
                                        <small>Due: <?php echo htmlspecialchars($task['due_date']); ?></small>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="m-0">Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="../views/tasks/create.php" class="btn btn-primary">Create New Task</a>
                            <a href="../views/tasks/index.php" class="btn btn-secondary">View All Tasks</a>
                            <?php if ($auth->isAdmin()): ?>
                                <a href="../views/users/index.php" class="btn btn-info">Manage Users</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../views/partials/footer.php'; ?>