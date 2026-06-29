<?php
/**
 * Database Configuration (SECURED)
 * Ministry of Health and Population - Nepal
 *
 * Security controls applied:
 * 1. Uses a least-privilege application account (not root).
 * 2. Uses PDO with exception mode and utf8mb4.
 * 3. Never exposes raw database errors to users.
 */

$db_host = 'localhost';
$db_port = 3306;
$db_user = 'moh_app_user';
$db_pass = 'Str0ng!AppPass';
$db_name = 'moh_nepal_lab';

try {
    $dsn = "mysql:host={$db_host};port={$db_port};dbname={$db_name};charset=utf8mb4";
    $pdo = new PDO($dsn, $db_user, $db_pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
} catch (PDOException $e) {
    error_log('DB Connection failed: ' . $e->getMessage());
    die('System error. Please contact IT support.');
}
?>
