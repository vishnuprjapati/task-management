<?php
require_once __DIR__ . '/../../config/Auth.php';
require_once __DIR__ . '/../../controllers/UserController.php';

$auth = new Auth();
if (!$auth->isAdmin()) {
    $_SESSION['error'] = 'Unauthorized access';
    header('Location: /task-manager/public/index.php');
    exit;
}

if (!isset($_GET['id'])) {
    $_SESSION['error'] = 'User ID not provided';
    header('Location: /task-manager/views/users/index.php');
    exit;
}

$userController = new UserController();

// Check if this is a confirmation request
if (!isset($_GET['confirm'])) {
    $user = $userController->getUser($_GET['id']);
    if (!$user) {
        $_SESSION['error'] = 'User not found';
        header('Location: /task-manager/views/users/index.php');
        exit;
    }
    
    // Show confirmation page
    $title = "Confirm Deletion";
    require_once __DIR__ . '/../partials/header.php';
    ?>
    
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-danger text-white">
                        <h4 class="mb-0">Confirm User Deletion</h4>
                    </div>
                    <div class="card-body">
                        <p>Are you sure you want to delete user <strong><?= htmlspecialchars($user['name']) ?></strong> (<?= htmlspecialchars($user['email']) ?>)?</p>
                        <p class="text-danger">This action cannot be undone!</p>
                        
                        <form method="GET" action="">
                            <input type="hidden" name="id" value="<?= $user['id'] ?>">
                            <input type="hidden" name="confirm" value="1">
                            
                            <button type="submit" class="btn btn-danger">Delete Permanently</button>
                            <a href="/task-manager/views/users/index.php" class="btn btn-secondary">Cancel</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php
    require_once __DIR__ . '/../partials/footer.php';
    exit;
}

// If confirmed, proceed with deletion
try {
    if ($userController->deleteUser($_GET['id'])) {
        $_SESSION['success'] = 'User deleted successfully';
    } else {
        $_SESSION['error'] = 'Failed to delete user';
    }
} catch (Exception $e) {
    $_SESSION['error'] = 'Error: ' . $e->getMessage();
}

header('Location: /task-manager/views/users/index.php');
exit;
?>