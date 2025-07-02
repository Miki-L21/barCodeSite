<?php
session_start();

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

class BarcodeAPI {
    
    public function processBarcode($data) {
        try {
            // Validar dados recebidos
            $required = ['barcode', 'name', 'brand', 'category'];
            foreach ($required as $field) {
                if (!isset($data[$field]) || empty($data[$field])) {
                    return $this->sendResponse(false, "Campo '$field' é obrigatório");
                }
            }

            // Verificar se utilizador está logado
            if (!isset($_SESSION['user_id'])) {
                return $this->sendResponse(false, 'Utilizador deve estar logado para guardar produtos');
            }

            // Limpar preço
            $price = $this->cleanPrice($data['price'] ?? '');

            // Criar/atualizar produto
            $productResult = $this->createProduct($data['barcode'], $data['name'], $data['brand'], $data['category'], $price);
            
            if (!$productResult || !isset($productResult['success']) || !$productResult['success']) {
                return $this->sendResponse(false, 'Erro ao criar produto: ' . ($productResult['message'] ?? 'Erro desconhecido'));
            }

            // Associar produto ao utilizador
            $userProductResult = $this->addProductToUser($productResult['product_id']);
            
            if (!$userProductResult || !isset($userProductResult['success']) || !$userProductResult['success']) {
                return $this->sendResponse(false, 'Produto criado mas erro ao associar: ' . ($userProductResult['message'] ?? 'Erro desconhecido'));
            }

            // Resposta de sucesso
            return $this->sendResponse(true, 'Produto guardado e associado com sucesso', [
                'product' => [
                    'id' => $productResult['product_id'],
                    'barcode' => $data['barcode'],
                    'name' => $data['name'],
                    'brand' => $data['brand'],
                    'category' => $data['category'],
                    'price' => $price,
                    'action' => $productResult['action'] ?? 'unknown'
                ],
                'user_relation' => [
                    'relation_id' => $userProductResult['relation_id'] ?? null,
                    'action' => $userProductResult['action'] ?? 'unknown'
                ]
            ]);

        } catch (Exception $e) {
            return $this->sendResponse(false, 'Erro interno: ' . $e->getMessage());
        }
    }

    private function createProduct($barcode, $name, $brand, $category, $price) {
        $postData = [
            'action' => 'create_or_update',
            'barcode' => $barcode,
            'name' => $name,
            'brand' => $brand,
            'category' => $category,
            'price' => $price
        ];

        return $this->callController('productController.php', $postData);
    }

    private function addProductToUser($productId) {
        $postData = [
            'action' => 'add_to_user',
            'product_id' => $productId
        ];

        return $this->callController('userProductController.php', $postData);
    }

    private function callController($controllerFile, $postData) {
        require_once $controllerFile;

        switch ($controllerFile) {
            case 'productController.php':
                $controller = new ProductController();
                return $controller->createOrUpdate($postData); // método que devolve array

            case 'userProductController.php':
                $controller = new UserProductController();
                return $controller->addProductToUser($postData['product_id']); // método que devolve array

            default:
                return ['success' => false, 'message' => 'Controller não suportado'];
        }
    }


    private function cleanPrice($price) {
        if (empty($price) || $price === 'Preço não disponível') {
            return null;
        }

        $cleanPrice = trim(str_replace(['€', ' '], '', $price));

        return is_numeric($cleanPrice) ? floatval($cleanPrice) : null;
    }

    private function sendResponse($success, $message, $data = []) {
        $response = array_merge([
            'success' => $success,
            'message' => $message
        ], $data);

        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    }
}

// Processar requisição
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $jsonInput = file_get_contents('php://input');
    $data = json_decode($jsonInput, true);

    // Tentativa de fallback
    if (!$data && !empty($_POST)) {
        $data = $_POST;
    }

    if (empty($data)) {
        echo json_encode(['success' => false, 'message' => 'Nenhum dado recebido']);
        exit;
    }

    $api = new BarcodeAPI();
    $api->processBarcode($data);
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
    exit;
}
