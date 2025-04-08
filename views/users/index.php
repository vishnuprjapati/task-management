<?php
require_once    '../../config/Auth.php';
require_once   '../../controllers/UserController.php';


$auth = new Auth();
if (!$auth->isAdmin()) {
    header('Location: ../../public/index.php');
    exit;
}

$userController = new UserController();
$data = $userController->index();
$users = $data['users'];

$title = "Manage Users";
require_once '../partials/header.php';
?>

<div class="row">
    <div class="col-md-3">
        <div class="card mb-4">
            <div class="card-header">
                Navigation
            </div>
            <div class="card-body">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link" href="../../public/index.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../../views/tasks/index.php">Manage Tasks</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="#">Manage Users</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    
    <div class="col-md-9">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="m-0">Users</h5>
                <div>
                <a href="/task-manager/views/users/create.php" class="btn btn-primary btn-sm">Create User</a>
                </div>
            </div>
            <div class="card-body">
                <?php if (empty($users)): ?>
                    <div class="alert alert-info">No users found.</div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Joined</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($users as $user): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($user['name']); ?></td>
                                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                                        <td><?php echo ucfirst($user['role']); ?></td>
                                        <td><?php echo date('M j, Y', strtotime($user['created_at'])); ?></td>
                                        <td>
                                            <a href="edit.php?id=<?php echo $user['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                                            <a href="../../controllers/UserController.php?action=delete&id=<?php echo $user['id']; ?>" 
                                               class="btn btn-sm btn-danger" 
                                               onclick="return confirm('Are you sure you want to delete this user?')">Delete</a>
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