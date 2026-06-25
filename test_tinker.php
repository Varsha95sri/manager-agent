<?php
$controller = app()->make(\App\Http\Controllers\ManagerAgentController::class);
try {
    $view = $controller->taskEntry();
    $html = $view->render();
    echo "SUCCESS\n";
    echo substr($html, 0, 100);
} catch (\Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
