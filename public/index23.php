<?php
require_once __DIR__ . '/../app/Core/Autoload.php';
$db = \App\Core\Database::getInstance()->getConnection();
$query = $db->query("SELECT COUNT(*) as total FROM clientes");
$row = $query->fetch();
echo "O sistema encontrou " . $row['total'] . " clientes no banco de dados.";
