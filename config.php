<?php

$configFile = __DIR__ . '/config.local.php';

if (!is_file($configFile)) {
    error_log('Citypharma database configuration missing.');
    http_response_code(500);
    exit('Database configuration unavailable.');
}

$config = require $configFile;

try {
    $conn = new mysqli(
        $config['host'],
        $config['username'],
        $config['password'],
        $config['database']
    );

    $conn->set_charset('utf8mb4');

} catch (mysqli_sql_exception $e) {
    error_log('Citypharma database connection failed: ' . $e->getMessage());
    http_response_code(500);
    exit('Database connection unavailable.');
}
