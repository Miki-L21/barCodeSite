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

class ProductController {
    private $pdo;

    public function __construct($config) {
        try {
            $this->pdo = new PDO(
                "pgsql:host={$config['host']};port={$config['port']};dbname={$config['dbname']}", 
                $config['username'], 
                $config['password']
            );
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            error_log("ERRO CONEXÃO BD: " . $e->getMessage());
            $this->sendResponse(false, 'Erro ao conectar à base de dados: ' . $e->getMessage());
            exit;
        }
    }

    // Buscar todos os produtos
    public function getAllProducts() {
        try {
            $stmt = $this->pdo->prepare("SELECT idproduto, barcode, nome, marca, categoria, preco FROM produtos ORDER BY nome");
            $stmt->execute();
            $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

            error_log("PRODUTOS ENCONTRADOS: " . count($products));

            $this->sendResponse(true, 'Produtos encontrados com sucesso', [
                'products' => $products,
                'total' => count($products)
            ]);

        } catch (PDOException $e) {
            error_log("ERRO AO BUSCAR PRODUTOS: " . $e->getMessage());
            $this->sendResponse(false, 'Erro ao buscar produtos: ' . $e->getMessage());
        }
    }

    // Buscar produto por ID
    public function getProductById($id) {
        try {
            $stmt = $this->pdo->prepare("SELECT idproduto, barcode, nome, marca, categoria, preco FROM produtos WHERE idproduto = ?");
            $stmt->execute([$id]);
            $product = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$product) {
                $this->sendResponse(false, 'Produto não encontrado');
                return;
            }

            $this->sendResponse(true, 'Produto encontrado com sucesso', [
                'product' => $product
            ]);

        } catch (PDOException $e) {
            $this->sendResponse(false, 'Erro ao buscar produto: ' . $e->getMessage());
        }
    }

    // Buscar produto por código de barras
    public function getProductByBarcode($barcode) {
        try {
            $stmt = $this->pdo->prepare("SELECT idproduto, barcode, nome, marca, categoria, preco FROM produtos WHERE barcode = ?");
            $stmt->execute([$barcode]);
            $product = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$product) {
                $this->sendResponse(false, 'Produto com este código de barras não encontrado');
                return;
            }

            $this->sendResponse(true, 'Produto encontrado com sucesso', [
                'product' => $product
            ]);

        } catch (PDOException $e) {
            $this->sendResponse(false, 'Erro ao buscar produto por código de barras: ' . $e->getMessage());
        }
    }

    private function sendResponse($success, $message, $data = null) {
        $response = [
            'success' => $success,
            'message' => $message
        ];
        
        if ($data) {
            $response['data'] = $data;
        }
        
        echo json_encode($response);
    }
}

// Processar requisições
try {
    $controller = new ProductController($config);
    
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        // Para requisições GET, verificar ação
        if (isset($_GET['action']) && $_GET['action'] === 'getAll') {
            $controller->getAllProducts();
        } else {
            $controller->getAllProducts(); // Default para todos os produtos
        }
    } else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Para requisições POST
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$input && isset($_POST['action'])) {
            // Fallback para POST tradicional
            $action = $_POST['action'];
        } else {
            $action = $input['action'] ?? 'get_all';
        }

        switch ($action) {
            case 'get_all':
                $controller->getAllProducts();
                break;

            case 'get_by_id':
                $id = $input['id'] ?? $_POST['id'] ?? null;
                if (!$id) {
                    echo json_encode(['success' => false, 'message' => 'ID do produto é obrigatório']);
                    exit;
                }
                $controller->getProductById($id);
                break;

            case 'get_by_barcode':
                $barcode = $input['barcode'] ?? $_POST['barcode'] ?? null;
                if (!$barcode) {
                    echo json_encode(['success' => false, 'message' => 'Código de barras é obrigatório']);
                    exit;
                }
                $controller->getProductByBarcode($barcode);
                break;

            default:
                echo json_encode(['success' => false, 'message' => 'Ação inválida']);
                break;
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Método não permitido']);
    }
} catch (Exception $e) {
    error_log("ERRO GERAL: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Erro interno: ' . $e->getMessage()]);
}
?>