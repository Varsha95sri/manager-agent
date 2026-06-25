<?php
$url = 'http://127.0.0.1:8000/manager-agent/tasks';
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
if ($httpcode >= 400) {
    if (preg_match('/<title>(.*?)<\/title>/s', $response, $matches)) {
        echo "Error: " . trim($matches[1]) . "\n";
    }
    // Also extract the first exception message if possible
    if (preg_match('/<div class="exception-message">\s*([^<]+)\s*<\/div>/s', $response, $matches)) {
        echo "Message: " . trim($matches[1]) . "\n";
    } else {
        echo "Full response: " . substr(strip_tags($response), 0, 500) . "\n";
    }
} else {
    echo "Success: $httpcode\n";
}
curl_close($ch);
