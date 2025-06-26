<?php
require_once 'controllerLista.php';

// Definir headers para JSON
header('Content-Type: application/json');

use controller\ControllerLista;

try {
    $controller = new ControllerLista();
    
    // Verificar qual ação foi solicitada
    $action = $_GET['action'] ?? null;
    
    if (!$action) {
        http_response_code(400);
        echo json_encode([
            'error' => 'Parâmetro action em falta',
            'available_actions' => ['getAll', 'getCompras', 'testConnection'],
            'examples' => [
                'getAll' => '?action=getAll',
                'getCompras' => '?action=getCompras&id=1',
                'testConnection' => '?action=testConnection'
            ]
        ]);
        exit;
    }
    
    switch ($action) {
        case 'testConnection':
            $result = $controller->testConnection();
            echo json_encode($result);
            break;
            
        case 'getAll':
            $result = $controller->getAll();
            if (!$result['success']) {
                http_response_code($result['status'] ?? 500);
            }
            echo json_encode($result);
            break;
            
        case 'getCompras':
            $id = $_GET['id'] ?? null;
            if (!$id) {
                http_response_code(400);
                echo json_encode([
                    'error' => 'Parâmetro ID em falta',
                    'example' => '?action=getCompras&id=1'
                ]);
                break;
            }
            
            $result = $controller->getCompras($id);
            if (!$result['success']) {
                http_response_code($result['status'] ?? 500);
            }
            echo json_encode($result);
            break;
            
        default:
            http_response_code(404);
            echo json_encode([
                'error' => 'Ação não encontrada',
                'available_actions' => ['getAll', 'getCompras', 'testConnection']
            ]);
            break;
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Erro interno do servidor',
        'message' => $e->getMessage()
    ]);
}
?>