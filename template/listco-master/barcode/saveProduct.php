<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Configuração da base de dados PostgreSQL
$host = 'localhost';        // ou o seu host
$port = '5432';            // porta padrão PostgreSQL
$dbname = 'postgres';   // nome da sua base de dados
$user = 'postgres.zhqcasfqhgudhywgcrlj';     // seu usuário PostgreSQL
$password = 'MercadoVirtual1234321';   // sua senha

try {
    // Conexão PostgreSQL
    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";
    $pdo = new PDO($dsn, $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);

    // Receber dados JSON
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        throw new Exception('Dados inválidos recebidos');
    }

    // Verificar se o produto já existe
    $checkSql = "SELECT idproduto FROM produtos WHERE barcode = :barcode";
    $checkStmt = $pdo->prepare($checkSql);
    $checkStmt->execute([':barcode' => $input['barcode']]);
    
    if ($checkStmt->fetch()) {
        // Produto já existe
        echo json_encode([
            'success' => true, 
            'message' => 'Produto já existe na base de dados'
        ]);
    } else {
        // Inserir novo produto
        $insertSql = "INSERT INTO produtos 
                      (barcode, nome, marca, categoria, preco) 
                      VALUES (:barcode, :nome, :marca, :categoria, :preco)";
        
        $insertStmt = $pdo->prepare($insertSql);
        $insertStmt->execute([
            ':barcode' => $input['barcode'],
            ':nome' => $input['name'],
            ':marca' => $input['brand'],
            ':categoria' => $input['category'],
            ':preco' => $input['price']
        ]);
        
        echo json_encode([
            'success' => true, 
            'message' => 'Produto inserido com sucesso',
            'product_id' => $pdo->lastInsertId()
        ]);
    }

} catch (PDOException $e) {
    echo json_encode([
        'success' => false, 
        'message' => 'Erro de base de dados: ' . $e->getMessage()
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false, 
        'message' => 'Erro: ' . $e->getMessage()
    ]);
}
?>