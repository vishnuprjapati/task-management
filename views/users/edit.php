<?php
require_once __DIR__ . '/../../config/Auth.php';
require_once __DIR__ . '/../../controllers/UserController.php';

$auth = new Auth();
if (!$auth->isAdmin()) {
    header('Location: /task-manager/public/index.php');
    exit;
}

if (!isset($_GET['id'])) {
    $_SESSION['error'] = 'User ID not provided';
    header('Location: /task-manager/views/users/index.php');
    exit;
}

$userController = new UserController();
$user = $userController->getUser($_GET['id']); // Use the public method

if (!$user) {
    $_SESSION['error'] = 'User not found';
    header('Location: /task-manager/views/users/index.php');
    exit;
}

$title = "Edit User";
require_once __DIR__ . '/../partials/header.php';
?>

<div class="row">
    <div class="col-md-3">

    </div>
    
    <div class="col-md-9">
        <div class="card">
            <div class="card-header">
                <h5 class="m-0">Edit User: <?= htmlspecialchars($user['name']) ?></h5>
            </div>
            <div class="card-body">
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger">
                        <?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
                    </div>
                <?php endif; ?>

                <form action="/task-manager/controllers/UserController.php?action=update" method="POST">
                    <input type="hidden" name="id" value="<?= $user['id'] ?>">
                    
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="name" name="name" 
                               value="<?= htmlspecialchars($user['name']) ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" 
                               value="<?= htmlspecialchars($user['email']) ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="role" class="form-label">Role</label>
                        <select class="form-control" id="role" name="role" required>
                            <option value="user" <?= $user['role'] === 'user' ? 'selected' : '' ?>>User</option>
                            <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <div class="alert alert-info">
                            Leave blank to keep current password
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="new_password" class="form-label">New Password</label>
                        <input type="password" class="form-control" id="new_password" name="new_password" minlength="6">
                    </div>
                    
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Confirm Password</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password">
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Update User</button>
                    <a href="/task-manager/views/users/index.php" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>

