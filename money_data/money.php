<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

define('TOKEN', 'secure123');
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', 'Goodgod123!');
define('DB_NAME', 'moneydb');

if (!isset($_REQUEST['token']) || $_REQUEST['token'] !== TOKEN) {
    http_response_code(403);
    echo "Access denied: Invalid token.";
    exit;
}

$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($mysqli->connect_errno) {
    http_response_code(500);
    echo "Database connection failed: " . $mysqli->connect_error;
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['data'])) {
        http_response_code(400);
        echo "Missing 'data' field.";
        exit;
    }

    $stmt = $mysqli->prepare("INSERT INTO money (data) VALUES (?)");
    $stmt->bind_param("s", $_POST['data']);

    if ($stmt->execute()) {
        echo "Money data saved.";
    } else {
        http_response_code(500);
        echo "Failed to save data.";
    }

    $stmt->close();
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $result = $mysqli->query("SELECT timestamp, data FROM money ORDER BY timestamp DESC");

    if (!$result) {
        http_response_code(500);
        echo "Database query failed.";
        exit;
    }

    header('Content-Type: text/plain');
    while ($row = $result->fetch_assoc()) {
        echo "[" . $row['timestamp'] . "] " . $row['data'] . "\n";
    }

    $result->free();
    exit;
}

http_response_code(405);
echo "Method not allowed.";
