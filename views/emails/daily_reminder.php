<h1>Task Reminder for <?= htmlspecialchars($user['name']) ?></h1>

<?php if (!empty($dueToday)): ?>
<h2>Tasks Due Today</h2>
<ul>
    <?php foreach ($dueToday as $task): ?>
    <li>
        <strong><?= htmlspecialchars($task['title']) ?></strong>
        (Priority: <?= ucfirst($task['priority']) ?>)
        - <a href="<?= APP_URL ?>/tasks/view/<?= $task['id'] ?>">View Task</a>
    </li>
    <?php endforeach; ?>
</ul>
<?php endif; ?>

<?php if (!empty($pastDue)): ?>
<h2>Overdue Tasks</h2>
<ul>
    <?php foreach ($pastDue as $task): 
        $daysLate = (new DateTime($task['due_date']))->diff(new DateTime())->days;
    ?>
    <li>
        <strong><?= htmlspecialchars($task['title']) ?></strong>
        - <?= $daysLate ?> day(s) late
        - <a href="<?= APP_URL ?>/tasks/view/<?= $task['id'] ?>">View Task</a>
    </li>
    <?php endforeach; ?>
</ul>
<?php endif; ?>

<p>
    <a href="<?= APP_URL ?>/tasks">View All Tasks</a> | 
    <a href="<?= APP_URL ?>/settings/notifications">Notification Settings</a>
</p>