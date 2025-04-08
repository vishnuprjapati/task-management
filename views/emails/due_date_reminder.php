<?php

// views/emails/daily_reminder.php
// Ensure these variables are defined at the top
$name = $name ?? 'User';
$dueToday = $dueToday ?? [];
$pastDue = $pastDue ?? [];
$appUrl = $appUrl ?? '#';
?>

<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #f8f9fa; padding: 15px; text-align: center; }
        .task-list { margin: 20px 0; }
        .task-item { padding: 10px; border-bottom: 1px solid #eee; }
        .high { color: #dc3545; font-weight: bold; }
        .medium { color: #ffc107; }
        .low { color: #0d6efd; }
        .late { color: #dc3545; font-style: italic; }
        .footer { margin-top: 20px; text-align: center; font-size: 0.9em; color: #6c757d; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Task Reminder</h2>
        </div>
        
        <p>Hello <?= htmlspecialchars($name) ?>,</p>
        
        <?php if (!empty($dueToday)): ?>
        <h3>Tasks Due Today</h3>
        <div class="task-list">
            <?php foreach ($dueToday as $task): ?>
            <div class="task-item <?= $task['priority'] ?>">
                <?= htmlspecialchars($task['title']) ?>
                <span class="priority">(<?= ucfirst($task['priority']) ?> priority)</span>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
        
        <?php if (!empty($pastDue)): ?>
        <h3>Past Due Tasks</h3>
        <div class="task-list">
            <?php foreach ($pastDue as $task): ?>
            <div class="task-item late">
                <?= htmlspecialchars($task['title']) ?>
                <span class="days-late">(<?= $task['days_late'] ?> days late)</span>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
        
        <div class="footer">
            <p><a href="<?= APP_URL ?>/tasks">View all tasks in your dashboard</a></p>
            <p>This is an automated notification. Please do not reply to this email.</p>
        </div>
    </div>
</body>
</html>