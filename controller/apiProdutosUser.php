<?php
session_start();
header('Content-Type: application/json');

$config = require __DIR__ . '/env.php';
require_once __DIR__ . '/ProdutoUserController.php';

$controller = new ProdutoUserController($config);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (isset($input['action']) && $input['action'] === 'remove' && isset($input['produto_user_id'])) {
        $produtoUserId = $input['produto_user_id'];
        $controller->removeProdutoUserLogado($produtoUserId);
        exit;
    }
}

$controller->getProdutosUserLogado();