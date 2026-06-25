<?php
require_once __DIR__ . '/vendor/autoload.php';

// Load .env file only if it exists (local development)
if (file_exists(__DIR__ . '/.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();
}

function get_db_connection(): mysqli
{
    $host = $_ENV['DB_HOST'] ?? getenv('DB_HOST');
    $port = $_ENV['DB_PORT'] ?? getenv('DB_PORT') ?? 3306;
    $name = $_ENV['DB_NAME'] ?? getenv('DB_NAME');
    $user = $_ENV['DB_USER'] ?? getenv('DB_USER');
    $pass = $_ENV['DB_PASS'] ?? getenv('DB_PASS');

    $conn = new mysqli($host, $user, $pass, $name, (int) $port);

    if ($conn->connect_error) {
        error_log('DB connection failed: ' . $conn->connect_error);
        http_response_code(500);
        die('<p style="color:red;padding:2rem"><strong>Database connection failed.</strong></p>');
    }
    return $conn;
}