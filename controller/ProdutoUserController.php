<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

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

    public function removeProdutoUserLogado($produtoUserId) {
        if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in'] || !isset($_SESSION['user_id'])) {
            $this->sendResponse(false, 'Utilizador não está logado');
            return;
        }

        try {
            $userId = $_SESSION['user_id'];

            $stmt = $this->pdo->prepare("SELECT id FROM produto_user WHERE id = ? AND id_user = ?");
            $stmt->execute([$produtoUserId, $userId]);

            if (!$stmt->fetch()) {
                $this->sendResponse(false, 'Produto não encontrado ou não pertence ao utilizador');
                return;
            }

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
