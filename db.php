<?php
/**
 * db.php — Centralized database connection for L.A.M.E.
 *
 * All pages should require_once this file and call get_db_connection().
 * Change credentials here once and it applies everywhere.
 */

define('DB_HOST', 'localhost');
define('DB_NAME', 'l.a.m.e');
define('DB_USER', 'root');
define('DB_PASS', '');

/**
 * Returns a live mysqli connection, or exits with an error message on failure.
 */
function get_db_connection(): mysqli {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error) {
        // In production, log this instead of exposing details
        http_response_code(500);
        die('<p style="color:red;font-family:Arial,sans-serif;padding:2rem;">'
            . '<strong>Database connection failed.</strong><br>'
            . 'Please check that MySQL is running and the credentials in db.php are correct.<br>'
            . '<em>Error: ' . htmlspecialchars($conn->connect_error) . '</em>'
            . '</p>');
    }
    return $conn;
}
