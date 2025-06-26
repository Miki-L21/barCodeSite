<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Carregar variáveis de ambiente
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
            $this->sendResponse(false, 'Erro ao conectar à base de dados: ' . $e->getMessage());
            exit;
        }
    }

    public function createOrUpdateProduct($barcode, $name, $brand, $category, $price) {
        try {
            // Verificar se produto já existe
            $stmt = $this->pdo->prepare("SELECT idproduto FROM produtos WHERE barcode = ?");
            $stmt->execute([$barcode]);
            $existingProduct = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($existingProduct) {
                // Atualizar produto existente
                $stmt = $this->pdo->prepare("
                    UPDATE produtos 
                    SET nome = ?, marca = ?, categoria = ?, preco = ? 
                    WHERE barcode = ?
                ");
                $stmt->execute([$name, $brand, $category, $price, $barcode]);
                
                $this->sendResponse(true, 'Produto atualizado com sucesso', [
                    'action' => 'updated',
                    'product_id' => $existingProduct['idproduto']
                ]);
            } else {
                // Inserir novo produto
                $stmt = $this->pdo->prepare("
                    INSERT INTO produtos (barcode, nome, marca, categoria, preco) 
                    VALUES (?, ?, ?, ?, ?) 
                    RETURNING idproduto
                ");
                $stmt->execute([$barcode, $name, $brand, $category, $price]);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                
                $this->sendResponse(true, 'Produto criado com sucesso', [
                    'action' => 'created',
                    'product_id' => $result['idproduto']
                ]);
            }

        } catch (PDOException $e) {
            $this->sendResponse(false, 'Erro ao processar produto: ' . $e->getMessage());
        }
    }

    public function getProductByBarcode($barcode) {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM produtos WHERE barcode = ?");
            $stmt->execute([$barcode]);
            $product = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($product) {
                $this->sendResponse(true, 'Produto encontrado', ['product' => $product]);
            } else {
                $this->sendResponse(false, 'Produto não encontrado');
            }

        } catch (PDOException $e) {
            $this->sendResponse(false, 'Erro ao buscar produto: ' . $e->getMessage());
        }
    }

    public function getAllProducts() {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM produtos ORDER BY idproduto DESC");
            $stmt->execute();
            $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $this->sendResponse(true, 'Produtos encontrados', ['products' => $products]);

        } catch (PDOException $e) {
            $this->sendResponse(false, 'Erro ao buscar produtos: ' . $e->getMessage());
        }
    }

    private function sendResponse($success, $message, $data = null) {
        $response = [
            'success' => $success,
            'message' => $message
        ];
        
        if ($data) {
            $response = array_merge($response, $data);
        }
        
        echo json_encode($response);
    }
}

// Processar requisições
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller = new ProductController($config);
    
    // Verificar se a ação foi especificada
    if (!isset($_POST['action'])) {
        echo json_encode(['success' => false, 'message' => 'Ação não especificada']);
        exit;
    }

    $action = $_POST['action'];

    switch ($action) {
        case 'create_or_update':
            $required = ['barcode', 'name', 'brand', 'category', 'price'];
            foreach ($required as $field) {
                if (!isset($_POST[$field])) {
                    echo json_encode(['success' => false, 'message' => "Campo '$field' é obrigatório"]);
                    exit;
                }
            }
            
            $controller->createOrUpdateProduct(
                $_POST['barcode'],
                $_POST['name'],
                $_POST['brand'],
                $_POST['category'],
                $_POST['price']
            );
            break;

        case 'get_by_barcode':
            if (!isset($_POST['barcode'])) {
                echo json_encode(['success' => false, 'message' => 'Barcode é obrigatório']);
                exit;
            }
            $controller->getProductByBarcode($_POST['barcode']);
            break;

        case 'get_all':
            $controller->getAllProducts();
            break;

        default:
            echo json_encode(['success' => false, 'message' => 'Ação inválida']);
            break;
    }
} else if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $controller = new ProductController($config);
    
    if (isset($_GET['barcode'])) {
        $controller->getProductByBarcode($_GET['barcode']);
    } else {
        $controller->getAllProducts();
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
}
?>