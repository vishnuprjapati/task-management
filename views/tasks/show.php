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
$data = $taskController->show($_GET['id']);
$task = $data['task'];
$comments = $data['comments'];

if (!$task || (!$auth->isAdmin() && $task['user_id'] != $_SESSION['user_id'])) {
    $_SESSION['error'] = 'Task not found or unauthorized';
    header('Location: index.php');
    exit;
}

$title = "Task Details";
require_once '../partials/header.php';
?>

<div class="row">
    <?php require_once '../partials/sidebar.php'; ?>
    
    <div class="col-md-9">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="m-0">Task Details</h5>
                <div>
                    <a href="edit.php?id=<?php echo $task['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                    <a href="../../controllers/TaskController.php?action=delete&id=<?php echo $task['id']; ?>" 
                       class="btn btn-sm btn-danger" 
                       onclick="return confirm('Are you sure you want to delete this task?')">Delete</a>
                </div>
            </div>
            <div class="card-body">
                <h4><?php echo htmlspecialchars($task['title']); ?></h4>
                <p><?php echo nl2br(htmlspecialchars($task['description'])); ?></p>
                
                <div class="row mt-4">
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="m-0">Details</h6>
                            </div>
                            <div class="card-body">
                                <p><strong>Due Date:</strong> <?php echo htmlspecialchars($task['due_date']); ?></p>
                                <p><strong>Priority:</strong> 
                                    <span class="badge bg-<?php echo $task['priority'] === 'high' ? 'danger' : ($task['priority'] === 'medium' ? 'warning' : 'primary'); ?>">
                                        <?php echo ucfirst($task['priority']); ?>
                                    </span>
                                </p>
                                <p><strong>Status:</strong> 
                                    <span class="badge bg-<?php 
                                        echo $task['status'] === 'completed' ? 'success' : 
                                            ($task['status'] === 'in_progress' ? 'info' : 
                                                ($task['status'] === 'past_due' ? 'danger' : 'secondary')); ?>">
                                        <?php echo ucfirst(str_replace('_', ' ', $task['status'])); ?>
                                    </span>
                                </p>
                                <p><strong>Created:</strong> <?php echo date('M j, Y', strtotime($task['created_at'])); ?></p>
                                <p><strong>Last Updated:</strong> <?php echo date('M j, Y', strtotime($task['updated_at'])); ?></p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-9">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="m-0">Comments</h6>
                            </div>
                            <div class="card-body">
                                <?php if (empty($comments)): ?>
                                    <p>No comments yet.</p>
                                <?php else: ?>
                                    <div class="comments-list mb-4">
                                        <?php foreach ($comments as $comment): ?>
                                            <div class="card mb-2">
                                                <div class="card-body">
                                                    <div class="d-flex justify-content-between">
                                                        <h6 class="card-title"><?php echo htmlspecialchars($comment['user_name']); ?></h6>
                                                        <small class="text-muted"><?php echo date('M j, Y g:i a', strtotime($comment['created_at'])); ?></small>
                                                    </div>
                                                    <p class="card-text"><?php echo nl2br(htmlspecialchars($comment['content'])); ?></p>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                                
                                <form action="../../controllers/TaskController.php?action=addComment&task_id=<?php echo $task['id']; ?>" method="POST">
                                    <div class="mb-3">
                                        <label for="content" class="form-label">Add Comment</label>
                                        <textarea class="form-control" id="content" name="content" rows="3" required></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../partials/footer.php'; ?>