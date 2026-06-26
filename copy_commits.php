<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

DB::statement("INSERT INTO git_commits (team_member_id, commit_hash, message, repository_name, committed_at, created_at, updated_at) SELECT employee_id, commit_sha, message, 'Enterprise Repo', committed_at, created_at, updated_at FROM commits WHERE employee_id IS NOT NULL");

echo "Git commits cloned successfully.";
