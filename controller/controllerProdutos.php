<?php
session_start(); // Iniciar sessão

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

    // Buscar todos os produtos
    public function getAllProducts() {
        try {
            $stmt = $this->pdo->prepare("SELECT idproduto, barcode, nome, marca, categoria, preco FROM produtos ORDER BY nome");
            $stmt->execute();
            $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $this->sendResponse(true, 'Produtos encontrados com sucesso', [
                'products' => $products,
                'total' => count($products)
            ]);

        } catch (PDOException $e) {
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

    // Buscar produtos por categoria
    public function getProductsByCategory($category) {
        try {
            $stmt = $this->pdo->prepare("SELECT idproduto, barcode, nome, marca, categoria, preco FROM produtos WHERE categoria = ? ORDER BY nome");
            $stmt->execute([$category]);
            $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $this->sendResponse(true, 'Produtos da categoria encontrados com sucesso', [
                'products' => $products,
                'category' => $category,
                'total' => count($products)
            ]);

        } catch (PDOException $e) {
            $this->sendResponse(false, 'Erro ao buscar produtos por categoria: ' . $e->getMessage());
        }
    }

    // Buscar produtos por marca
    public function getProductsByBrand($brand) {
        try {
            $stmt = $this->pdo->prepare("SELECT idproduto, barcode, nome, marca, categoria, preco FROM produtos WHERE marca = ? ORDER BY nome");
            $stmt->execute([$brand]);
            $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $this->sendResponse(true, 'Produtos da marca encontrados com sucesso', [
                'products' => $products,
                'brand' => $brand,
                'total' => count($products)
            ]);

        } catch (PDOException $e) {
            $this->sendResponse(false, 'Erro ao buscar produtos por marca: ' . $e->getMessage());
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
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Criar instância do controller
    $controller = new ProductController($config);
    
    // Verificar se a ação foi especificada
    if (!isset($_POST['action'])) {
        echo json_encode(['success' => false, 'message' => 'Ação não especificada']);
        exit;
    }

    $action = $_POST['action'];

    switch ($action) {
        case 'get_all':
            $controller->getAllProducts();
            break;

        case 'get_by_id':
            if (!isset($_POST['id'])) {
                echo json_encode(['success' => false, 'message' => 'ID do produto é obrigatório']);
                exit;
            }
            $controller->getProductById($_POST['id']);
            break;

        case 'get_by_category':
            if (!isset($_POST['category'])) {
                echo json_encode(['success' => false, 'message' => 'Categoria é obrigatória']);
                exit;
            }
            $controller->getProductsByCategory($_POST['category']);
            break;

        case 'get_by_brand':
            if (!isset($_POST['brand'])) {
                echo json_encode(['success' => false, 'message' => 'Marca é obrigatória']);
                exit;
            }
            $controller->getProductsByBrand($_POST['brand']);
            break;

        case 'get_by_barcode':
            if (!isset($_POST['barcode'])) {
                echo json_encode(['success' => false, 'message' => 'Código de barras é obrigatório']);
                exit;
            }
            $controller->getProductByBarcode($_POST['barcode']);
            break;

        default:
            echo json_encode(['success' => false, 'message' => 'Ação inválida']);
            break;
    }
} else if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Para requisições GET, apenas retornar todos os produtos
    $controller = new ProductController($config);
    $controller->getAllProducts();
} else {
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
}



?>