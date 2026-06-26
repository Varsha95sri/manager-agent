<?php

function processFile($path, $isGuest) {
    $content = file_get_contents($path);
    
    // Replace CSS variables
    $content = str_replace('--bg-color: #0f172a;', '--bg-color: #f8fafc;', $content);
    $content = str_replace('--card-bg: #1e293b;', '--card-bg: #ffffff;', $content);
    $content = str_replace('--text-color: #ffffff;', '--text-color: #1e293b;', $content);
    $content = str_replace('--accent-color: #a855f7;', '--accent-color: #4f46e5;', $content);
    $content = str_replace('--accent-hover: #b55fe6;', '--accent-hover: #4338ca;', $content);
    $content = str_replace('--border-color: #334155;', '--border-color: #e2e8f0;', $content);
    $content = str_replace('--text-muted: #94a3b8;', '--text-muted: #64748b;', $content);
    
    if ($isGuest) {
        $content = str_replace('text-white', 'text-dark', $content);
        $content = str_replace('rgba(255, 255, 255, 0.05)', 'rgba(0, 0, 0, 0.05)', $content);
        $content = str_replace('box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.5), 0 10px 10px -5px rgba(0, 0, 0, 0.5);', 'box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);', $content);
        $content = str_replace('background-color: #0b0f19 !important;', 'background-color: #f1f5f9 !important;', $content);
        $content = str_replace('color: #ffffff !important;', 'color: #1e293b !important;', $content);
    } else {
        // Welcome.blade.php
        $content = str_replace('text-white', 'text-dark', $content);
        $content = str_replace('navbar-dark', 'navbar-light', $content);
        $content = str_replace('rgba(15, 23, 42, 0.8)', 'rgba(255, 255, 255, 0.8)', $content);
        $content = str_replace('rgba(255, 255, 255, 0.05)', 'rgba(0, 0, 0, 0.05)', $content);
        $content = str_replace('rgba(255, 255, 255, 0.04)', 'rgba(0, 0, 0, 0.04)', $content);
        $content = str_replace('background-color: #0b0f19;', 'background-color: #ffffff;', $content);
        $content = str_replace('background-color: #0d1321;', 'background-color: #f8fafc;', $content);
        $content = str_replace('background-color: #1e293b;', 'background-color: #f1f5f9;', $content);
        $content = str_replace('color: #e2e8f0;', 'color: #1e293b;', $content);
        $content = str_replace('border: 1px solid #334155;', 'border: 1px solid #e2e8f0;', $content);
        $content = str_replace('color: #ffffff;', 'color: #ffffff;', $content); // keeping white for accent buttons
        $content = str_replace('linear-gradient(to right, #ffffff, #e2e8f0)', 'linear-gradient(to right, #1e293b, #4f46e5)', $content);
        $content = str_replace('bg-slate-950/20', 'bg-slate-50', $content);
    }

    file_put_contents($path, $content);
}

processFile('d:\manager Agent\manager-agent\resources\views\welcome.blade.php', false);
processFile('d:\manager Agent\manager-agent\resources\views\layouts\guest.blade.php', true);
echo 'done';
