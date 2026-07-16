<?php
require_once __DIR__ . '/config.php';
try {
    $pdo = getPDO();
    $sql = "CREATE TABLE IF NOT EXISTS inquiry_status (
        id INT AUTO_INCREMENT PRIMARY KEY,
        status VARCHAR(50) NOT NULL UNIQUE
    )";
    $pdo->exec($sql);

    // Insert default statuses
    $insertSql = "INSERT IGNORE INTO inquiry_status (status) VALUES ('on hold'), ('close'), ('not interested')";
    $pdo->exec($insertSql);

    echo "Inquiry status table created successfully.";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>