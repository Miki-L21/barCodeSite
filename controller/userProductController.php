<?php
session_start();

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Carregar variáveis de ambiente
$config = require __DIR__ . '/env.php';

class UserProductController {
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

    public function addProductToUser($productId, $userId = null) {
        try {
            // Se userId não foi fornecido, usar da sessão
            if ($userId === null) {
                if (!isset($_SESSION['user_id'])) {
                    return ['success' => false, 'message' => 'Utilizador não está logado'];
                }
                $userId = $_SESSION['user_id'];
            }

            // Verificar se a relação já existe
            $stmt = $this->pdo->prepare("SELECT id FROM produto_user WHERE id_produto = ? AND id_user = ?");
            $stmt->execute([$productId, $userId]);
            $existing = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($existing) {
                return [
                    'success' => true, 
                    'message' => 'Produto já está associado ao utilizador',
                    'action' => 'already_exists',
                    'relation_id' => $existing['id']
                ];
            }

            // Criar nova relação
            $stmt = $this->pdo->prepare("
                INSERT INTO produto_user (id_produto, id_user) 
                VALUES (?, ?) 
                RETURNING id
            ");
            $stmt->execute([$productId, $userId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            return [
                'success' => true, 
                'message' => 'Produto associado ao utilizador com sucesso',
                'action' => 'created',
                'relation_id' => $result['id'],
                'product_id' => $productId,
                'user_id' => $userId
            ];

        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Erro ao associar produto ao utilizador: ' . $e->getMessage()];
        }
    }

    public function createProductAndAssociate($data) {
        try {
            // Verificar se utilizador está logado
            if (!isset($_SESSION['user_id'])) {
                return ['success' => false, 'message' => 'Utilizador não está logado'];
            }

            $userId = $_SESSION['user_id'];

            // Validar dados obrigatórios
            $required = ['barcode', 'name'];
            foreach ($required as $field) {
                if (!isset($data[$field]) || empty($data[$field])) {
                    return ['success' => false, 'message' => "Campo '$field' é obrigatório"];
                }
            }

            // Limpar preço
            $price = $this->cleanPrice($data['price'] ?? '');

            // Verificar se produto já existe
            $stmt = $this->pdo->prepare("SELECT idproduto FROM produtos WHERE barcode = ?");
            $stmt->execute([$data['barcode']]);
            $existingProduct = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($existingProduct) {
                $productId = $existingProduct['idproduto'];
                $productAction = 'exists';
            } else {
                // Criar novo produto
                $stmt = $this->pdo->prepare("
                    INSERT INTO produtos (barcode, nome, marca, categoria, preco, descricao) 
                    VALUES (?, ?, ?, ?, ?, ?) 
                    RETURNING idproduto
                ");
                $stmt->execute([
                    $data['barcode'],
                    $data['name'],
                    $data['brand'] ?? 'Marca não identificada',
                    $data['category'] ?? 'Categoria não identificada',
                    $price,
                    $data['description'] ?? ''
                ]);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                $productId = $result['idproduto'];
                $productAction = 'created';
            }

            // Associar produto ao utilizador
            $userResult = $this->addProductToUser($productId, $userId);

            return [
                'success' => true,
                'message' => 'Produto processado e associado com sucesso',
                'product' => [
                    'id' => $productId,
                    'barcode' => $data['barcode'],
                    'name' => $data['name'],
                    'action' => $productAction
                ],
                'user_relation' => $userResult
            ];

        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Erro na base de dados: ' . $e->getMessage()];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Erro interno: ' . $e->getMessage()];
        }
    }

    public function getUserProducts($userId = null) {
        try {
            // Se userId não foi fornecido, usar da sessão
            if ($userId === null) {
                if (!isset($_SESSION['user_id'])) {
                    $this->sendResponse(false, 'Utilizador não está logado');
                    return;
                }
                $userId = $_SESSION['user_id'];
            }

            $stmt = $this->pdo->prepare("
                SELECT p.*, pu.id as relation_id
                FROM produtos p
                INNER JOIN produto_user pu ON p.idproduto = pu.id_produto
                WHERE pu.id_user = ?
                ORDER BY pu.id DESC
            ");
            $stmt->execute([$userId]);
            $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $this->sendResponse(true, 'Produtos do utilizador encontrados', [
                'products' => $products,
                'count' => count($products)
            ]);

        } catch (PDOException $e) {
            $this->sendResponse(false, 'Erro ao buscar produtos do utilizador: ' . $e->getMessage());
        }
    }

    public function removeProductFromUser($productId, $userId = null) {
        try {
            // Se userId não foi fornecido, usar da sessão
            if ($userId === null) {
                if (!isset($_SESSION['user_id'])) {
                    $this->sendResponse(false, 'Utilizador não está logado');
                    return;
                }
                $userId = $_SESSION['user_id'];
            }

            $stmt = $this->pdo->prepare("DELETE FROM produto_user WHERE id_produto = ? AND id_user = ?");
            $stmt->execute([$productId, $userId]);

            if ($stmt->rowCount() > 0) {
                $this->sendResponse(true, 'Produto removido do utilizador com sucesso');
            } else {
                $this->sendResponse(false, 'Relação produto-utilizador não encontrada');
            }

        } catch (PDOException $e) {
            $this->sendResponse(false, 'Erro ao remover produto do utilizador: ' . $e->getMessage());
        }
    }

    public function getProductUsers($productId) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT u.id, u.username, u.email, pu.id as relation_id
                FROM users u
                INNER JOIN produto_user pu ON u.id = pu.id_user
                WHERE pu.id_produto = ?
                ORDER BY pu.id DESC
            ");
            $stmt->execute([$productId]);
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $this->sendResponse(true, 'Utilizadores do produto encontrados', [
                'users' => $users,
                'count' => count($users)
            ]);

        } catch (PDOException $e) {
            $this->sendResponse(false, 'Erro ao buscar utilizadores do produto: ' . $e->getMessage());
        }
    }

    private function cleanPrice($price) {
        if (empty($price) || $price === 'Preço não disponível') {
            return null;
        }

        $cleanPrice = trim(str_replace(['€', ' '], '', $price));
        return is_numeric($cleanPrice) ? floatval($cleanPrice) : null;
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

// Função para obter dados da requisição (POST ou JSON)
function getRequestData() {
    // Primeiro, tentar obter dados do $_POST
    if (!empty($_POST)) {
        return $_POST;
    }
    
    // Se não há $_POST, tentar JSON
    $jsonInput = file_get_contents('php://input');
    if (!empty($jsonInput)) {
        $jsonData = json_decode($jsonInput, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            return $jsonData;
        }
    }
    
    return [];
}

// Processar requisições
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller = new UserProductController($config);
    
    // Obter dados da requisição
    $data = getRequestData();
    
    if (empty($data)) {
        echo json_encode(['success' => false, 'message' => 'Nenhum dado recebido']);
        exit;
    }
    
    if (!isset($data['action'])) {
        echo json_encode(['success' => false, 'message' => 'Ação não especificada']);
        exit;
    }

    $action = $data['action'];

    switch ($action) {
        case 'add_to_user':
            // Modo completo: criar produto e associar ao utilizador
            if (isset($data['barcode']) && isset($data['name'])) {
                $result = $controller->createProductAndAssociate($data);
                echo json_encode($result);
            } else if (isset($data['product_id'])) {
                // Modo simples: apenas associar produto existente
                $userId = isset($data['user_id']) ? $data['user_id'] : null;
                $result = $controller->addProductToUser($data['product_id'], $userId);
                echo json_encode($result);
            } else {
                echo json_encode(['success' => false, 'message' => 'Dados insuficientes (barcode+name ou product_id)']);
            }
            break;

        case 'get_user_products':
            $userId = isset($data['user_id']) ? $data['user_id'] : null;
            $controller->getUserProducts($userId);
            break;

        case 'remove_from_user':
            if (!isset($data['product_id'])) {
                echo json_encode(['success' => false, 'message' => 'ID do produto é obrigatório']);
                exit;
            }
            
            $userId = isset($data['user_id']) ? $data['user_id'] : null;
            $controller->removeProductFromUser($data['product_id'], $userId);
            break;

        case 'get_product_users':
            if (!isset($data['product_id'])) {
                echo json_encode(['success' => false, 'message' => 'ID do produto é obrigatório']);
                exit;
            }
            $controller->getProductUsers($data['product_id']);
            break;

        default:
            echo json_encode(['success' => false, 'message' => 'Ação inválida: ' . $action]);
            break;
    }
} else if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $controller = new UserProductController($config);
    
    if (isset($_GET['product_id'])) {
        $controller->getProductUsers($_GET['product_id']);
    } else {
        $controller->getUserProducts();
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
}
?>