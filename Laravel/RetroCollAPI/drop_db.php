<?php
try {
    $pdo = new PDO('mysql:host=127.0.0.1', 'root', '');
    $pdo->exec('DROP DATABASE IF EXISTS RetroColl');
    $pdo->exec('CREATE DATABASE RetroColl');
    echo 'DB Recreated';
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}
