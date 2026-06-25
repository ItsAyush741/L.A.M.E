<?php
require_once __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$dotenv->required(['DB_HOST', 'DB_PORT', 'DB_NAME', 'DB_USER', 'DB_PASS']);

function get_db_connection(): mysqli
{
    $conn = new mysqli(
        $_ENV['DB_HOST'],
        $_ENV['DB_USER'],
        $_ENV['DB_PASS'],
        $_ENV['DB_NAME'],
        (int) $_ENV['DB_PORT']
    );
    if ($conn->connect_error) {
        error_log('DB connection failed: ' . $conn->connect_error);
        http_response_code(500);
        die('<p style="color:red;padding:2rem"><strong>Database connection failed.</strong></p>');
    }
    return $conn;
}