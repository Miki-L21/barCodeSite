<?php
session_start();
header('Content-Type: application/json');

$config = require __DIR__ . '/env.php';
require_once __DIR__ . '/ProdutoUserController.php';

$controller = new ProdutoUserController($config);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (isset($input['action'])) {
        switch ($input['action']) {
            case 'remove':
                if (isset($input['produto_user_id'])) {
                    $produtoUserId = $input['produto_user_id'];
                    $controller->removeProdutoUserLogado($produtoUserId);
                    exit;
                }
                break;

            case 'update_quantity':
                if (isset($input['produto_user_id']) && isset($input['quantidade'])) {
                    $produtoUserId = $input['produto_user_id'];
                    $quantidade = intval($input['quantidade']);
                    $controller->updateQuantidade($produtoUserId, $quantidade);
                    exit;
                }
                break;

            case 'update_status_comprado':
                if (isset($input['produto_user_id'])) {
                    $produtoUserId = $input['produto_user_id'];

                    // Verifica se 'comprado' existe e não está vazio
                    if (!isset($input['comprado']) || $input['comprado'] === '') {
                        echo json_encode(['success' => false, 'message' => 'Campo comprado não pode estar vazio']);
                        exit;
                    }

                    $statusComprado = filter_var($input['comprado'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

                    if (!is_bool($statusComprado)) {
                        echo json_encode(['success' => false, 'message' => 'Valor inválido para status comprado']);
                        exit;
                    }

                    // Força booleano puro
                    $statusComprado = $statusComprado ? true : false;

                    $controller->updateStatusComprado($produtoUserId, $statusComprado);
                    exit;
                } else {
                    echo json_encode(['success' => false, 'message' => 'Parâmetro produto_user_id é obrigatório']);
                    exit;
                }
                break;

            case 'clear_comprado_status':
                $controller->clearCompradoStatus();
                exit;

            case 'mark_all_purchased':
                $controller->markAllAsPurchased();
                exit;

            case 'clear_all':
                $controller->clearAllProdutosUser();
                exit;

            default:
                echo json_encode(['success' => false, 'message' => 'Ação não reconhecida']);
                exit;
        }
    }
}

$controller->getProdutosUserLogado();
