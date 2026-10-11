<?php
require __DIR__ . '/../vendor/autoload.php';
use App\Core\Database;

header('Content-Type: application/json');

$db = Database::getConnection();
$stmt = $db->query("SELECT api_key FROM api_keys WHERE activo = 1 LIMIT 1");
$key = $stmt->fetchColumn();

echo json_encode(['api_key' => $key]);
?>