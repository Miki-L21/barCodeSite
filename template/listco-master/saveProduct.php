<?php
session_start();

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Verificar se é uma requisição POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
    exit;
}

try {
    // Carregar configurações da base de dados
    $config = require_once('../../controller/env.php');
    
    // Conectar à base de dados PostgreSQL
    $dsn = "pgsql:host={$config['host']};port={$config['port']};dbname={$config['dbname']}";
    $pdo = new PDO($dsn, $config['username'], $config['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false
    ]);
    
    // Ler dados JSON do corpo da requisição
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    
    // Log para debug
    error_log("Dados recebidos: " . print_r($data, true));
    
    if (!$data) {
        throw new Exception('Dados JSON inválidos');
    }
    
    // Validar campos obrigatórios
    $requiredFields = ['barcode', 'name'];
    foreach ($requiredFields as $field) {
        if (empty($data[$field])) {
            throw new Exception("Campo obrigatório em falta: {$field}");
        }
    }
    
    // Obter user_id da sessão (se disponível)
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    
    // Limpar e converter preço
    $price = null;
    if (!empty($data['price']) && $data['price'] !== 'Preço não disponível') {
        $price = floatval(str_replace(['€', ','], ['', '.'], $data['price']));
    }
    
    // Verificar se já existe um produto com este código de barras
    $checkStmt = $pdo->prepare("SELECT idproduto FROM produtos WHERE barcode = ?");
    $checkStmt->execute([$data['barcode']]);
    
    if ($checkStmt->fetch()) {
        // Se já existe, atualizar
        $updateStmt = $pdo->prepare("
            UPDATE produtos 
            SET nome = ?, marca = ?, categoria = ?, preco = ?, estado = ?
            WHERE barcode = ?
        ");
        
        $updateStmt->execute([
            $data['name'] ?? '',
            $data['brand'] ?? '',
            $data['category'] ?? '',
            $price,
            'ativo',
            $data['barcode']
        ]);
        
        echo json_encode([
            'success' => true, 
            'message' => 'Produto atualizado com sucesso',
            'action' => 'updated'
        ]);
        
    } else {
        // Se não existe, inserir novo
        $insertStmt = $pdo->prepare("
            INSERT INTO produtos (barcode, nome, marca, categoria, preco, user_id, estado)
            VALUES (?, ?, ?, ?, ?, ?, ?) 
            RETURNING idproduto
        ");
        
        $insertStmt->execute([
            $data['barcode'],
            $data['name'] ?? '',
            $data['brand'] ?? '',
            $data['category'] ?? '',
            $price,
            $user_id,
            'ativo'
        ]);
        
        // Para PostgreSQL, usar RETURNING para obter o ID
        $result = $insertStmt->fetch();
        $newId = $result['idproduto'] ?? null;
        
        echo json_encode([
            'success' => true, 
            'message' => 'Produto inserido com sucesso',
            'action' => 'inserted',
            'id' => $newId
        ]);
    }
    
} catch (PDOException $e) {
    error_log("Erro na base de dados: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'message' => 'Erro na base de dados: ' . $e->getMessage()
    ]);
    
} catch (Exception $e) {
    error_log("Erro geral: " . $e->getMessage());
    http_response_code(400);
    echo json_encode([
        'success' => false, 
        'message' => $e->getMessage()
    ]);
}
?>