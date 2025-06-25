<?php
session_start(); // Iniciar sessão

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Carregar variáveis de ambiente
$config = require __DIR__ . '/env.php';

class UserController {
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

   public function login($email, $password) {
    try {
        // Modifique a query para buscar também o username
        $stmt = $this->pdo->prepare("SELECT id, email, password, username FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            $this->sendResponse(false, 'Email não encontrado ou utilizador inativo');
            return;
        }


            // Verificar password (compatível com encriptada e texto simples)
            $passwordCorrect = false;
            
            // Primeiro tenta verificar se é password encriptada
            if (password_verify($password, $user['password'])) {
                $passwordCorrect = true;
            } 
            // Se não funcionar, verifica se é texto simples
            else if ($password === $user['password']) {
                $passwordCorrect = true;
                
                // Aproveita para encriptar a password agora
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $updateStmt = $this->pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
                $updateStmt->execute([$hashedPassword, $user['id']]);
            }
            
             if ($passwordCorrect) {
            $this->updateLastLogin($user['id']);

            // Armazenar também o username na sessão
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_username'] = $user['username']; // Adicionado esta linha
            $_SESSION['logged_in'] = true;

            $this->sendResponse(true, 'Login realizado com sucesso', [
                'user_id' => $user['id'],
                'email' => $user['email'],
                'username' => $user['username'] // Adicione esta linha
            ]);
        
            } else {
                $this->sendResponse(false, 'Password incorreta');
            }

        } catch (PDOException $e) {
            $this->sendResponse(false, 'Erro ao processar login: ' . $e->getMessage());
        }
    }

   public function register($email, $password, $username = null) {  // Adicione $username como parâmetro
    try {
        // Verificar se email já existe
        $stmt = $this->pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        
        if ($stmt->fetch()) {
            $this->sendResponse(false, 'Email já está registado');
            return;
        }

        // Inserir novo utilizador (agora com username)
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->pdo->prepare("INSERT INTO users (email, password, username) VALUES (?, ?, ?)");  // Note username
        $stmt->execute([$email, $hashedPassword, $username]);

        $this->sendResponse(true, 'Utilizador registado com sucesso');

    } catch (PDOException $e) {
        $this->sendResponse(false, 'Erro ao registar utilizador: ' . $e->getMessage());
    }
}

    private function updateLastLogin($userId) {
        try {
            $stmt = $this->pdo->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
            $stmt->execute([$userId]);
        } catch (PDOException $e) {
            // Log do erro mas não interromper o login
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
// Processar requisições
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Criar instância do controller
    $controller = new UserController($config);
    
    // Verificar se a ação foi especificada
    if (!isset($_POST['action'])) {
        echo json_encode(['success' => false, 'message' => 'Ação não especificada']);
        exit;
    }

    $action = $_POST['action'];

    switch ($action) {
        case 'login':
            if (!isset($_POST['email']) || !isset($_POST['password'])) {
                echo json_encode(['success' => false, 'message' => 'Email e password são obrigatórios']);
                exit;
            }
            $controller->login($_POST['email'], $_POST['password']);
            break;

        case 'register':
            if (!isset($_POST['email']) || !isset($_POST['password'])) {
                echo json_encode(['success' => false, 'message' => 'Email e password são obrigatórios']);
                exit;
            }
            
            // Se não existir username no POST, usar parte do email como username
            $username = isset($_POST['username']) ? $_POST['username'] : explode('@', $_POST['email'])[0];
            
            $controller->register($_POST['email'], $_POST['password'], $username);
            break;

        case 'logout':
            session_destroy();
            echo json_encode(['success' => true, 'message' => 'Logout realizado com sucesso']);
            break;

        case 'check_session':
            if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']) {
                echo json_encode([
                    'success' => true, 
                    'logged_in' => true,
                    'user' => [
                        'id' => $_SESSION['user_id'],
                        'email' => $_SESSION['user_email']
                    ]
                ]);
            } else {
                echo json_encode(['success' => false, 'logged_in' => false]);
            }
            break;

        default:
            echo json_encode(['success' => false, 'message' => 'Ação inválida']);
            break;
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
}
?>