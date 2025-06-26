<?php
session_start();
header('Content-Type: application/json');

$config = require __DIR__ . '/env.php';
require_once __DIR__ . '/ProdutoUserController.php';

$controller = new ProdutoUserController($config);
$controller->getProdutosUserLogado();
