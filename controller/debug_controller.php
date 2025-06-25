<?php
// Arquivo para debug: debug_controller.php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Log de debug
error_log("=== DEBUG CONTROLLER INICIADO ===");

try {
    // Testar se o env.php existe e pode ser carregado
    $configPath = __DIR__ . '/env.php';
    error_log("Tentando carregar config de: " . $configPath);
    
    if (!file_exists($configPath)) {
        throw new Exception("Arquivo env.php não encontrado em: " . $configPath);
    }
    
    $config = require $configPath;
    error_log("Config carregado: " . print_r($config, true));
    
    // Testar conexão
    error_log("Tentando conectar à base de dados...");
    $pdo = new PDO(
        "pgsql:host={$config['host']};port={$config['port']};dbname={$config['dbname']}", 
        $config['username'], 
        $config['password']
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    error_log("Conexão bem-sucedida!");
    
    // Testar se recebe dados POST
    error_log("Método da requisição: " . $_SERVER['REQUEST_METHOD']);
    error_log("Dados POST: " . print_r($_POST, true));
    
    // Resposta de sucesso
    echo json_encode([
        'success' => true,
        'message' => 'Debug bem-sucedido',
        'data' => [
            'config_loaded' => true,
            'db_connected' => true,
            'method' => $_SERVER['REQUEST_METHOD'],
            'post_data' => $_POST
        ]
    ]);
    
} catch (Exception $e) {
    error_log("ERRO: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Erro de debug: ' . $e->getMessage(),
        'file' => __FILE__,
        'line' => __LINE__
    ]);
} catch (PDOException $e) {
    error_log("ERRO PDO: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Erro de base de dados: ' . $e->getMessage(),
        'code' => $e->getCode()
    ]);
}
?>