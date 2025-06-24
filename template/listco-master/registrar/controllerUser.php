<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Carregar variáveis de ambiente
$config = require __DIR__ . '/env.php';

class UserController {
    private $pdo;

    public function __construct($host, $dbname, $username, $password, $port) {
        try {
            $this->pdo = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $username, $password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            $this->sendResponse(false, 'Erro ao conectar à base de dados: ' . $e->getMessage());
            exit;
        }
    }

    public function register($username, $email, $password) {
        try {
            // Verificar se o email já existe
            $stmt = $this->pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $existingUser = $stmt->fetch();
            
            if ($existingUser) {
                $this->sendResponse(false, 'Este email já está registado');
                return;
            }
            
            // Verificar se o nome de utilizador já existe
            $stmt = $this->pdo->prepare("SELECT id FROM users WHERE username = ?");
            $stmt->execute([$username]);
            $existingUsername = $stmt->fetch();
            
            if ($existingUsername) {
                $this->sendResponse(false, 'Este nome de utilizador já existe');
                return;
            }
            
            // Inserir novo utilizador (password sem encriptação)
            $stmt = $this->pdo->prepare("INSERT INTO users (username, email, password, created_at) VALUES (?, ?, ?, NOW())");
            $result = $stmt->execute([$username, $email, $password]);
            
            if ($result) {
                $userId = $this->pdo->lastInsertId();
                
                $this->sendResponse(true, 'Conta criada com sucesso!', [
                    'user_id' => $userId,
                    'username' => $username,
                    'email' => $email
                ]);
            } else {
                $this->sendResponse(false, 'Erro ao criar conta');
            }
            
        } catch (PDOException $e) {
            $this->sendResponse(false, 'Erro ao processar registo: ' . $e->getMessage());
        }
    }

    private function sendResponse($success, $message, $data = null) {
        $response = [
            'success' => $success,
            'message' => $message
        ];

        if ($data !== null) {
            $response['data'] = $data;
        }

        echo json_encode($response);
    }
}

// Processar requisição
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    if (!$input || !isset($input['action'])) {
        echo json_encode(['success' => false, 'message' => 'Requisição mal formatada']);
        exit;
    }

    $controller = new UserController(
        $config['host'],
        $config['dbname'],
        $config['username'],
        $config['password'],
        $config['port']
    );

    switch ($input['action']) {
        case 'register':
            if (empty($input['username']) || empty($input['email']) || empty($input['password'])) {
                echo json_encode(['success' => false, 'message' => 'Nome de utilizador, email e password são obrigatórios']);
                exit;
            }
            $controller->register($input['username'], $input['email'], $input['password']);
            break;

        default:
            echo json_encode(['success' => false, 'message' => 'Ação não reconhecida']);
            break;
    }

} else {
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
}
?>