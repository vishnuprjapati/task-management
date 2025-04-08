<div class="col-md-3">
    <div class="card mb-4">
        <div class="card-header">
            Navigation
        </div>
        <div class="card-body">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link" href="/task-manager/public/index.php">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/task-manager/views/tasks/create.php">Create Task</a>
                </li>
                <?php if ($auth->isAdmin()): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="/task-manager/views/users/index.php">Manage Users</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            Task Summary
        </div>
        <div class="card-body">
            <ul class="list-group">
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    Pending
                    <span class="badge bg-primary rounded-pill"><?php echo $pendingCount ?? 0; ?></span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    In Progress
                    <span class="badge bg-info rounded-pill"><?php echo $inProgressCount ?? 0; ?></span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    Past Due
                    <span class="badge bg-danger rounded-pill"><?php echo $pastDueCount ?? 0; ?></span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    Completed
                    <span class="badge bg-success rounded-pill"><?php echo $completedCount ?? 0; ?></span>
                </li>
            </ul>
        </div>
    </div>

    <?php if (!empty($dueToday)): ?>
        <div class="card mb-4">
            <div class="card-header bg-warning text-dark">
                Due Today
            </div>
            <div class="card-body">
                <ul class="list-group">
                    <?php foreach ($dueToday as $task): ?>
                        <li class="list-group-item">
                            <a href="show.php?id=<?php echo $task['id']; ?>"><?php echo htmlspecialchars($task['title']); ?></a>
                            <span class="badge bg-<?php echo $task['priority'] === 'high' ? 'danger' : ($task['priority'] === 'medium' ? 'warning' : 'primary'); ?>">
                                <?php echo ucfirst($task['priority']); ?>
                            </span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    <?php endif; ?>

    <?php if (!empty($pastDue)): ?>
        <div class="card mb-4">
            <div class="card-header bg-danger text-white">
                Past Due
            </div>
            <div class="card-body">
                <ul class="list-group">
                    <?php foreach ($pastDue as $task): ?>
                        <li class="list-group-item">
                            <a href="show.php?id=<?php echo $task['id']; ?>"><?php echo htmlspecialchars($task['title']); ?></a>
                            <span class="badge bg-<?php echo $task['priority'] === 'high' ? 'danger' : ($task['priority'] === 'medium' ? 'warning' : 'primary'); ?>">
                                <?php echo ucfirst($task['priority']); ?>
                            </span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    <?php endif; ?>
</div>