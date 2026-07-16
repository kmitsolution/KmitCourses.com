<?php
// db_test.php - simple connection test for the configured database.
require_once __DIR__ . '/config.php';

try {
    $pdo = getPDO();
    $stmt = $pdo->query('SELECT VERSION()');
    $version = $stmt->fetchColumn();
    echo "✅ Database connected successfully. MySQL version: " . htmlspecialchars($version);
} catch (PDOException $e) {
    // Show a clear error message for debugging
    echo "❌ Database connection failed.\n";
    echo "Error: " . htmlspecialchars($e->getMessage()) . "\n";
    echo "DSN: " . htmlspecialchars($dsn) . "\n";
}
