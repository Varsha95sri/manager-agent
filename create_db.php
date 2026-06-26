<?php
try {
    $db = new PDO('mysql:host=127.0.0.1;port=3306', 'root', '');
    $db->exec('CREATE DATABASE IF NOT EXISTS manager_agent_new');
    echo 'DB manager_agent_new created';
} catch (Exception $e) {
    echo $e->getMessage();
}
