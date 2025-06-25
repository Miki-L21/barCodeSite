<?php
try {
    $config = require_once('../../controller/env.php');
    $dsn = "pgsql:host={$config['host']};port={$config['port']};dbname={$config['dbname']}";
    $pdo = new PDO($dsn, $config['username'], $config['password']);
    echo "✅ Ligação à base de dados OK!";
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage();
}
?>