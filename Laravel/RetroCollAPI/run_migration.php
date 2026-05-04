<?php
try {
    $pdo = new PDO('mysql:host=127.0.0.1', 'root', '');
    $pdo->exec('CREATE DATABASE IF NOT EXISTS RetroColl2');
    echo shell_exec('php artisan migrate --force');
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}
