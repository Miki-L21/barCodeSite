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
                    $this->sendResponse(false, 'Utilizador não está logado');
                    return;
                }
                $userId = $_SESSION['user_id'];
            }

            // Verificar se a relação já existe
            $stmt = $this->pdo->prepare("SELECT id FROM produto_user WHERE id_produto = ? AND id_user = ?");
            $stmt->execute([$productId, $userId]);
            $existing = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($existing) {
                $this->sendResponse(true, 'Produto já está associado ao utilizador', [
                    'action' => 'already_exists',
                    'relation_id' => $existing['id']
                ]);
                return;
            }

            // Criar nova relação
            $stmt = $this->pdo->prepare("
                INSERT INTO produto_user (id_produto, id_user) 
                VALUES (?, ?) 
                RETURNING id
            ");
            $stmt->execute([$productId, $userId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            $this->sendResponse(true, 'Produto associado ao utilizador com sucesso', [
                'action' => 'created',
                'relation_id' => $result['id'],
                'product_id' => $productId,
                'user_id' => $userId
            ]);

        } catch (PDOException $e) {
            $this->sendResponse(false, 'Erro ao associar produto ao utilizador: ' . $e->getMessage());
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
    $controller = new UserProductController($config);
    
    if (!isset($_POST['action'])) {
        echo json_encode(['success' => false, 'message' => 'Ação não especificada']);
        exit;
    }

    $action = $_POST['action'];

    switch ($action) {
        case 'add_to_user':
            if (!isset($_POST['product_id'])) {
                echo json_encode(['success' => false, 'message' => 'ID do produto é obrigatório']);
                exit;
            }
            
            $userId = isset($_POST['user_id']) ? $_POST['user_id'] : null;
            $controller->addProductToUser($_POST['product_id'], $userId);
            break;

        case 'get_user_products':
            $userId = isset($_POST['user_id']) ? $_POST['user_id'] : null;
            $controller->getUserProducts($userId);
            break;

        case 'remove_from_user':
            if (!isset($_POST['product_id'])) {
                echo json_encode(['success' => false, 'message' => 'ID do produto é obrigatório']);
                exit;
            }
            
            $userId = isset($_POST['user_id']) ? $_POST['user_id'] : null;
            $controller->removeProductFromUser($_POST['product_id'], $userId);
            break;

        case 'get_product_users':
            if (!isset($_POST['product_id'])) {
                echo json_encode(['success' => false, 'message' => 'ID do produto é obrigatório']);
                exit;
            }
            $controller->getProductUsers($_POST['product_id']);
            break;

        default:
            echo json_encode(['success' => false, 'message' => 'Ação inválida']);
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