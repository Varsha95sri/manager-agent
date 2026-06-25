<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$reports = App\Models\PerformanceReport::latest()->take(7)->get();
foreach($reports as $r) {
    echo $r->id . ' - ' . $r->report_date . "\n";
}
