<?php
session_start();

header('Content-Type: application/json');
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
                    $this->sendResponse(false, "Campo '$field' é obrigatório");
                    return;
                }
            }

            // Verificar se utilizador está logado
            if (!isset($_SESSION['user_id'])) {
                $this->sendResponse(false, 'Utilizador deve estar logado para guardar produtos');
                return;
            }

            // Limpar preço
            $price = $this->cleanPrice($data['price'] ?? '');

            // Criar/atualizar produto
            $productResult = $this->createProduct($data['barcode'], $data['name'], $data['brand'], $data['category'], $price);
            
            if (!$productResult['success']) {
                $this->sendResponse(false, 'Erro ao criar produto: ' . $productResult['message']);
                return;
            }

            // Associar produto ao utilizador
            $userProductResult = $this->addProductToUser($productResult['product_id']);
            
            if (!$userProductResult['success']) {
                $this->sendResponse(false, 'Produto criado mas erro ao associar: ' . $userProductResult['message']);
                return;
            }

            // Resposta de sucesso
            $this->sendResponse(true, 'Produto guardado e associado com sucesso', [
                'product' => [
                    'id' => $productResult['product_id'],
                    'barcode' => $data['barcode'],
                    'name' => $data['name'],
                    'brand' => $data['brand'],
                    'category' => $data['category'],
                    'price' => $price,
                    'action' => $productResult['action']
                ],
                'user_relation' => [
                    'relation_id' => $userProductResult['relation_id'] ?? null,
                    'action' => $userProductResult['action']
                ]
            ]);

        } catch (Exception $e) {
            $this->sendResponse(false, 'Erro interno: ' . $e->getMessage());
        }
    }

    private function createProduct($barcode, $name, $brand, $category, $price) {
        // Simular chamada ao ProductController
        $postData = [
            'action' => 'create_or_update',
            'barcode' => $barcode,
            'name' => $name,
            'brand' => $brand,
            'category' => $category,
            'price' => $price
        ];

        return $this->callController('ProductController.php', $postData);
    }

    private function addProductToUser($productId) {
        // Simular chamada ao UserProductController
        $postData = [
            'action' => 'add_to_user',
            'product_id' => $productId
        ];

        return $this->callController('UserProductController.php', $postData);
    }

    private function callController($controllerFile, $postData) {
        // Simular requisição POST interna
        $tempPost = $_POST;
        $_POST = $postData;
        
        ob_start();
        include $controllerFile;
        $response = ob_get_clean();
        
        $_POST = $tempPost;
        
        return json_decode($response, true);
    }

    private function cleanPrice($price) {
        if (empty($price) || $price === 'Preço não disponível') {
            return null;
        }
        
        // Remover símbolo € e espaços
        $cleanPrice = trim(str_replace('€', '', $price));
        
        // Verificar se é numérico
        if (is_numeric($cleanPrice)) {
            return floatval($cleanPrice);
        }
        
        return null;
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
    // Obter dados JSON
    $jsonInput = file_get_contents('php://input');
    $data = json_decode($jsonInput, true);
    
    // Se não conseguiu decodificar JSON, tentar $_POST
    if (!$data) {
        $data = $_POST;
    }
    
    if (empty($data)) {
        echo json_encode(['success' => false, 'message' => 'Nenhum dado recebido']);
        exit;
    }

    $api = new BarcodeAPI();
    $api->processBarcode($data);
    
} else {
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
}
?>