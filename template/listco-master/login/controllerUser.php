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

    public function login($email, $password) {
    try {
        $stmt = $this->pdo->prepare("SELECT id, email, password FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            $this->sendResponse(false, 'Email não encontrado ou utilizador inativo');
            return;
        }

        // Verificar password encriptada
        if (password_verify($password, $user['password'])) {
            // Atualizar último login
            $this->updateLastLogin($user['id']);

            // Login bem-sucedido
            $this->sendResponse(true, 'Login realizado com sucesso', [
                'user_id' => $user['id'],
                'email' => $user['email']
                // 'nome' removido
            ]);
        } else {
            $this->sendResponse(false, 'Password incorreta');
        }

    } catch (PDOException $e) {
        $this->sendResponse(false, 'Erro ao processar login');
    }
}

public function getUserByEmail($email) {
    try {
        $stmt = $this->pdo->prepare("SELECT id, email, created_at FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $this->sendResponse(true, 'Utilizador encontrado', $user);
        } else {
            $this->sendResponse(false, 'Utilizador não encontrado');
        }

    } catch (PDOException $e) {
        $this->sendResponse(false, 'Erro ao buscar utilizador');
    }
}


    private function updateLastLogin($userId) {
        try {
            $stmt = $this->pdo->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
            $stmt->execute([$userId]);
        } catch (PDOException $e) {
            error_log("Erro ao atualizar último login: " . $e->getMessage());
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
        case 'login':
            if (empty($input['email']) || empty($input['password'])) {
                echo json_encode(['success' => false, 'message' => 'Email e password são obrigatórios']);
                exit;
            }
            $controller->login($input['email'], $input['password']);
            break;

        case 'getUser':
            if (empty($input['email'])) {
                echo json_encode(['success' => false, 'message' => 'Email é obrigatório']);
                exit;
            }
            $controller->getUserByEmail($input['email']);
            break;

        default:
            echo json_encode(['success' => false, 'message' => 'Ação não reconhecida']);
            break;
    }

} else {
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
}
