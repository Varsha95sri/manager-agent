<?php
try {
    $db = new PDO('mysql:host=127.0.0.1;port=3306', 'root', '');
    $db->exec('DROP DATABASE IF EXISTS manager_agent');
    echo 'Old database deleted successfully.';
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}
