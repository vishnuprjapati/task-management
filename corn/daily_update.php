<?php
// cron/daily_update.php
require_once __DIR__.'/../config/bootstrap.php';

$logFile = __DIR__.'/task_updates.log';
$service = new TaskStatusService();

try {
    $updated = $service->runDailyUpdate();
    $message = date('Y-m-d H:i:s')." - ".($updated ? "Successfully updated tasks" : "Update failed");
    file_put_contents($logFile, $message.PHP_EOL, FILE_APPEND);
} catch (Exception $e) {
    $error = date('Y-m-d H:i:s')." - ERROR: ".$e->getMessage();
    file_put_contents($logFile, $error.PHP_EOL, FILE_APPEND);
}
?>