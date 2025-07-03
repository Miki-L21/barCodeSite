<?php
session_start();

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Carregar configuração da base de dados
$config = require __DIR__ . '/env.php';

// Debug básico
error_log("=== API ADD PRODUTO ===");
error_log("METHOD: " . $_SERVER['REQUEST_METHOD']);
error_log("SESSION: " . print_r($_SESSION, true));

// Verificar método
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Método não permitido'
    ]);
    exit();
}

// Verificar se utilizador está logado
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'message' => 'Utilizador não está logado'
    ]);
    exit();
}

// Obter dados do POST
$input = json_decode(file_get_contents('php://input'), true);
error_log("INPUT RECEBIDO: " . print_r($input, true));

// Validar dados
if (!isset($input['produto_id']) || empty($input['produto_id'])) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'ID do produto é obrigatório',
        'debug' => [
            'input_received' => $input,
            'produto_id_value' => $input['produto_id'] ?? 'NOT_SET'
        ]
    ]);
    exit();
}

$produto_id = (int)$input['produto_id'];
$user_id = (int)$_SESSION['user_id'];

error_log("DADOS PROCESSADOS - User ID: $user_id, Produto ID: $produto_id");

// Validar IDs
if ($produto_id <= 0 || $user_id <= 0) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'IDs inválidos',
        'debug' => [
            'produto_id' => $produto_id,
            'user_id' => $user_id
        ]
    ]);
    exit();
}

// Conexão à base de dados
try {
    $pdo = new PDO(
        "pgsql:host={$config['host']};port={$config['port']};dbname={$config['dbname']}", 
        $config['username'], 
        $config['password']
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    error_log("CONEXÃO BD: OK");
    
    // Verificar se produto existe
    $stmt = $pdo->prepare("SELECT idproduto, nome FROM produtos WHERE idproduto = ?");
    $stmt->execute([$produto_id]);
    $produto = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$produto) {
        error_log("PRODUTO NÃO ENCONTRADO: ID $produto_id");
        echo json_encode([
            'success' => false,
            'message' => 'Produto não encontrado'
        ]);
        exit();
    }
    
    error_log("PRODUTO ENCONTRADO: " . print_r($produto, true));
    
    // Verificar se utilizador existe
    $stmt = $pdo->prepare("SELECT id FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        error_log("UTILIZADOR NÃO ENCONTRADO: ID $user_id");
        echo json_encode([
            'success' => false,
            'message' => 'Utilizador não encontrado'
        ]);
        exit();
    }
    
    error_log("UTILIZADOR ENCONTRADO: " . print_r($user, true));
    
    // Verificar se já existe a relação
    $stmt = $pdo->prepare("SELECT id FROM produto_user WHERE id_produto = ? AND id_user = ?");
    $stmt->execute([$produto_id, $user_id]);
    $existe = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($existe) {
        error_log("RELAÇÃO JÁ EXISTE");
        echo json_encode([
            'success' => false,
            'message' => 'Produto já foi adicionado anteriormente'
        ]);
        exit();
    }
    
    // Inserir na tabela produto_user
    $stmt = $pdo->prepare("INSERT INTO produto_user (id_produto, id_user) VALUES (?, ?)");
    $result = $stmt->execute([$produto_id, $user_id]);
    
    if ($result) {
        error_log("PRODUTO ADICIONADO COM SUCESSO");
        echo json_encode([
            'success' => true,
            'message' => 'Produto adicionado com sucesso!',
            'data' => [
                'produto_id' => $produto_id,
                'produto_nome' => $produto['nome'],
                'user_id' => $user_id
            ]
        ]);
    } else {
        error_log("ERRO AO INSERIR NA BD");
        echo json_encode([
            'success' => false,
            'message' => 'Erro ao adicionar produto'
        ]);
    }
    
} catch (PDOException $e) {
    error_log("ERRO BD: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erro de base de dados: ' . $e->getMessage()
    ]);
} catch (Exception $e) {
    error_log("ERRO GERAL: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erro interno: ' . $e->getMessage()
    ]);
}
?>