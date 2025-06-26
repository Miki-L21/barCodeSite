<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Carregar variáveis de ambiente
$config = require __DIR__ . '/env.php';

class ProdutoUserController {
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

    public function getProdutosUserLogado() {
        // Verificar se o utilizador está logado
        if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in'] || !isset($_SESSION['user_id'])) {
            $this->sendResponse(false, 'Utilizador não está logado');
            return;
        }

        try {
            $userId = $_SESSION['user_id'];
            
            $sql = "
                SELECT 
                    pu.id,
                    pu.id_produto,
                    pu.id_user,
                    p.nome AS produto_nome,
                    p.marca AS produto_marca,
                    p.categoria AS produto_categoria,
                    p.preco AS produto_preco,
                    p.barcode AS produto_barcode
                FROM produto_user pu
                INNER JOIN produtos p ON pu.id_produto = p.idproduto
                WHERE pu.id_user = ?
                ORDER BY p.nome ASC
            ";

            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$userId]);
            $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $this->sendResponse(true, 'Produtos do utilizador carregados com sucesso', [
                'produtos' => $produtos,
                'user_info' => [
                    'id' => $_SESSION['user_id'],
                    'username' => $_SESSION['user_username'] ?? 'N/A',
                    'email' => $_SESSION['user_email'] ?? 'N/A'
                ]
            ]);

        } catch (PDOException $e) {
            $this->sendResponse(false, 'Erro ao carregar produtos: ' . $e->getMessage());
        }
    }

    public function checkSession() {
        if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']) {
            $this->sendResponse(true, 'Sessão ativa', [
                'logged_in' => true,
                'user' => [
                    'id' => $_SESSION['user_id'],
                    'username' => $_SESSION['user_username'] ?? 'N/A',
                    'email' => $_SESSION['user_email'] ?? 'N/A'
                ]
            ]);
        } else {
            $this->sendResponse(false, 'Sessão inativa', ['logged_in' => false]);
        }
    }

    public function getProdutoUser($id) {
        try {
            $sql = "
                SELECT 
                    pu.id,
                    pu.id_produto,
                    pu.id_user,
                    p.nome as produto_nome,
                    p.marca as produto_marca,
                    p.categoria as produto_categoria,
                    p.preco as produto_preco,
                    u.username as user_username,
                    u.email as user_email
                FROM produto_user pu
                LEFT JOIN produtos p ON pu.id_produto = p.idproduto
                LEFT JOIN users u ON pu.id_user = u.id
                WHERE pu.id = ?
            ";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id]);
            $produto = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$produto) {
                $this->sendResponse(false, 'Produto não encontrado');
                return;
            }

            $this->sendResponse(true, 'Produto carregado com sucesso', $produto);

        } catch (PDOException $e) {
            $this->sendResponse(false, 'Erro ao carregar produto: ' . $e->getMessage());
        }
    }

    public function addProdutoUser($idProduto, $idUser) {
        try {
            // Verificar se o produto existe
            $stmt = $this->pdo->prepare("SELECT id FROM produtos WHERE id = ?");
            $stmt->execute([$idProduto]);
            if (!$stmt->fetch()) {
                $this->sendResponse(false, 'Produto não encontrado');
                return;
            }

            // Verificar se o utilizador existe
            $stmt = $this->pdo->prepare("SELECT id FROM users WHERE id = ?");
            $stmt->execute([$idUser]);
            if (!$stmt->fetch()) {
                $this->sendResponse(false, 'Utilizador não encontrado');
                return;
            }

            // Verificar se já existe esta relação
            $stmt = $this->pdo->prepare("SELECT id FROM produto_user WHERE id_produto = ? AND id_user = ?");
            $stmt->execute([$idProduto, $idUser]);
            if ($stmt->fetch()) {
                $this->sendResponse(false, 'Este produto já está associado a este utilizador');
                return;
            }

            // Inserir nova relação
            $stmt = $this->pdo->prepare("INSERT INTO produto_user (id_produto, id_user) VALUES (?, ?)");
            $stmt->execute([$idProduto, $idUser]);

            $this->sendResponse(true, 'Produto associado ao utilizador com sucesso');

        } catch (PDOException $e) {
            $this->sendResponse(false, 'Erro ao associar produto: ' . $e->getMessage());
        }
    }

    public function removeProdutoUserLogado($produtoUserId) {
        // Verificar se o utilizador está logado
        if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in'] || !isset($_SESSION['user_id'])) {
            $this->sendResponse(false, 'Utilizador não está logado');
            return;
        }

        try {
            $userId = $_SESSION['user_id'];
            
            // Verificar se o produto pertence ao utilizador logado antes de remover
            $stmt = $this->pdo->prepare("SELECT id FROM produto_user WHERE id = ? AND id_user = ?");
            $stmt->execute([$produtoUserId, $userId]);
            
            if (!$stmt->fetch()) {
                $this->sendResponse(false, 'Produto não encontrado ou não pertence ao utilizador');
                return;
            }

            // Remover a associação
            $stmt = $this->pdo->prepare("DELETE FROM produto_user WHERE id = ? AND id_user = ?");
            $stmt->execute([$produtoUserId, $userId]);

            if ($stmt->rowCount() > 0) {
                $this->sendResponse(true, 'Produto removido da sua lista com sucesso');
            } else {
                $this->sendResponse(false, 'Erro ao remover produto');
            }

        } catch (PDOException $e) {
            $this->sendResponse(false, 'Erro ao remover produto: ' . $e->getMessage());
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
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $controller = new ProdutoUserController($config);
    
    // Verificar se existe parâmetro de ação
    $action = isset($_GET['action']) ? $_GET['action'] : 'getMeusProdutos';
    
    switch ($action) {
        case 'getMeusProdutos':
            $controller->getProdutosUserLogado();
            break;
            
        case 'checkSession':
            $controller->checkSession();
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Ação inválida']);
            break;
    }
    
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller = new ProdutoUserController($config);
    
    if (!isset($_POST['action'])) {
        echo json_encode(['success' => false, 'message' => 'Ação não especificada']);
        exit;
    }

    $action = $_POST['action'];

    switch ($action) {
        case 'removeProduto':
            if (!isset($_POST['id'])) {
                echo json_encode(['success' => false, 'message' => 'ID é obrigatório']);
                exit;
            }
            $controller->removeProdutoUserLogado($_POST['id']);
            break;

        default:
            echo json_encode(['success' => false, 'message' => 'Ação inválida']);
            break;
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
}
?>