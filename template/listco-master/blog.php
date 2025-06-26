<?php
session_start();

// Verificar se o utilizador está logado
$is_logged_in = isset($_SESSION['logged_in']) && $_SESSION['logged_in'];
$user_email = $is_logged_in ? $_SESSION['user_email'] : '';
$user_id = $is_logged_in ? $_SESSION['user_id'] : null;
?>

<!doctype html>
<html class="no-js" lang="zxx">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>DirectoryListing</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon.ico">

    <!-- CSS here -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/owl.carousel.min.css">
    <link rel="stylesheet" href="assets/css/slicknav.css">
    <link rel="stylesheet" href="assets/css/animate.min.css">
    <link rel="stylesheet" href="assets/css/hamburgers.min.css">
    <link rel="stylesheet" href="assets/css/magnific-popup.css">
    <link rel="stylesheet" href="assets/css/fontawesome-all.min.css">
    <link rel="stylesheet" href="assets/css/themify-icons.css">
    <link rel="stylesheet" href="assets/css/slick.css">
    <link rel="stylesheet" href="assets/css/nice-select.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/responsive.css">
    <link rel="stylesheet" href="stylesblock.css">

    <style>
        /* Estilos para Lista de Compras */
        .shopping-list-area {
            background-color: #f8f9fa;
        }
        .shopping-list-wrapper {
            background: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .product-item {
            background: white;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 15px;
            transition: all 0.3s ease;
        }
        .product-item:hover {
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transform: translateY(-2px);
        }
        .product-info h5 {
            color: #333;
            margin-bottom: 5px;
        }
        .product-brand {
            color: #666;
            font-size: 0.9rem;
        }
        .product-code {
            color: #999;
            font-size: 0.8rem;
        }
        .product-price {
            font-size: 1.2rem;
            font-weight: bold;
            color: #28a745;
        }
        .quantity-controls {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .quantity-controls .btn {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .quantity-display {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 8px 15px;
            min-width: 50px;
            text-align: center;
            font-weight: bold;
        }
        .item-total {
            font-size: 1.1rem;
            font-weight: bold;
            color: #333;
        }
        .cart-summary {
            border-top: 2px solid #e0e0e0;
            padding-top: 20px;
        }
        .total-box {
            border: 2px solid #28a745 !important;
        }
        .total-line {
            border-bottom: 1px solid #e0e0e0;
            padding-bottom: 5px;
        }
        .empty-cart-message {
            padding: 60px 20px;
        }
        @media (max-width: 768px) {
            .product-item .row > div {
                margin-bottom: 15px;
            }
            .cart-summary .row > div {
                margin-bottom: 20px;
            }
        }
    </style>
</head>
<body>
    <div id="preloader-active">
        <div class="preloader d-flex align-items-center justify-content-center">
            <div class="preloader-inner position-relative">
                <div class="preloader-circle"></div>
                <div class="preloader-img pere-text">
                    <img src="assets/img/logo/loder.png" alt="">
                </div>
            </div>
        </div>
    </div>

    <?php if (!$is_logged_in): ?>
    <div class="login-overlay" id="loginOverlay">
        <div class="login-modal">
            <div class="lock-icon">
                <i class="fas fa-lock"></i>
            </div>
            <h2>Acesso Restrito</h2>
            <p><strong>Para aceder ao Leitor de Código de Barras é obrigatório estar logado!</strong></p>
            <div class="feature-list">
                <p><strong>Com login terá acesso a:</strong></p>
                <ul>
                    <li><i class="fas fa-check"></i> Scanner de códigos de barras</li>
                    <li><i class="fas fa-check"></i> Histórico de produtos escaneados</li>
                    <li><i class="fas fa-check"></i> Carrinho de compras personalizado</li>
                </ul>
            </div>
            <div style="margin-top: 30px;">
                <a href="login.php" class="login-btn">
                    <i class="fas fa-sign-in-alt"></i> Fazer Login
                </a>
                <a href="registar.php" class="login-btn register-btn">
                    <i class="fas fa-user-plus"></i> Registar-se
                </a>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <?php include("cabecalho.php"); ?>

    <main>
        <div class="slider-area2">
            <div class="slider-height3 hero-overly hero-bg4 d-flex align-items-center">
                <div class="container">
                    <div class="row">
                        <div class="col-xl-12">
                            <div class="hero-cap2 pt-20 text-center">
                                <h2>Carrinho</h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <section class="shopping-list-area section-padding">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="section-tittle text-center mb-55">
                            <h2>Lista de Compras</h2>
                            <p>Gerencie os seus produtos de forma organizada</p>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-12">
                        <div class="shopping-list-wrapper">
                            <div id="empty-cart" class="empty-cart-message text-center" style="display: none;">
                                <div class="empty-cart-icon mb-4">
                                    <i class="fas fa-shopping-cart" style="font-size: 4rem; color: #ddd;"></i>
                                </div>
                                <h4>Carrinho Vazio</h4>
                                <p class="text-muted mb-4">Ainda não adicionou produtos ao carrinho.</p>
                                <a href="barcode.php" class="btn btn-primary btn-lg">
                                    <i class="fas fa-barcode"></i> Escanear Produtos
                                </a>
                            </div>

                            <div id="cart-items" style="display: none;">
                                <div class="cart-header mb-4">
                                    <div class="row align-items-center">
                                        <div class="col-md-6">
                                            <h4><i class="fas fa-list"></i> Produtos no Carrinho</h4>
                                        </div>
                                        <div class="col-md-6 text-right">
                                            <button onclick="clearCart()" class="btn btn-outline-danger">
                                                <i class="fas fa-trash"></i> Limpar Carrinho
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div id="products-list" class="products-list"></div>
                                <div class="cart-summary mt-4">
                                    <div class="row mt-3">
                                        <div class="col text-right">
                                            <p>Subtotal: <strong id="subtotal">€0.00</strong></p>
                                            <p>Total: <strong id="total-amount">€0.00</strong></p>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="cart-actions">
                                                <a href="barcode.php" class="btn btn-outline-primary">
                                                    <i class="fas fa-plus"></i> Adicionar Mais Produtos
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <?php include("rodape.php"); ?>

    <?php
var_dump($_SESSION['user_id']); // deve mostrar algo como int(5)
?>


    <div id="back-top">
        <a title="Go to Top" href="#"> <i class="fas fa-level-up-alt"></i></a>
    </div>

    <!-- Scripts -->
    <script src="./assets/js/vendor/modernizr-3.5.0.min.js"></script>
    <script src="./assets/js/vendor/jquery-1.12.4.min.js"></script>
    <script src="./assets/js/jquery.barfiller.js"></script>
    <script src="./assets/js/popper.min.js"></script>
    <script src="./assets/js/bootstrap.min.js"></script>
    <script src="./assets/js/jquery.slicknav.min.js"></script>
    <script src="./assets/js/owl.carousel.min.js"></script>
    <script src="./assets/js/slick.min.js"></script>
    <script src="./assets/js/wow.min.js"></script>
    <script src="./assets/js/animated.headline.js"></script>
    <script src="./assets/js/jquery.magnific-popup.js"></script>
    <script src="./assets/js/gijgo.min.js"></script>
    <script src="./assets/js/jquery.nice-select.min.js"></script>
    <script src="./assets/js/jquery.sticky.js"></script>
    <script src="./assets/js/jquery.counterup.min.js"></script>
    <script src="./assets/js/waypoints.min.js"></script>
    <script src="./assets/js/jquery.countdown.min.js"></script>
    <script src="./assets/js/hover-direction-snake.min.js"></script>
    <script src="./assets/js/contact.js"></script>
    <script src="./assets/js/jquery.form.js"></script>
    <script src="./assets/js/jquery.validate.min.js"></script>
    <script src="./assets/js/mail-script.js"></script>
    <script src="./assets/js/jquery.ajaxchimp.min.js"></script>
    <script src="./assets/js/plugins.js"></script>
    <script src="./assets/js/main.js"></script>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        displayCartItems();
    });

    function displayCartItems() {
        const cart = JSON.parse(localStorage.getItem('shopping_cart') || '[]');
        const emptyCart = document.getElementById('empty-cart');
        const cartItems = document.getElementById('cart-items');
        const productsList = document.getElementById('products-list');

        if (cart.length === 0) {
            emptyCart.style.display = 'block';
            cartItems.style.display = 'none';
            return;
        }

        emptyCart.style.display = 'none';
        cartItems.style.display = 'block';

        let productsHTML = '';
        let total = 0;

        cart.forEach((item, index) => {
            const itemPrice = parseFloat(item.price.replace('€', '').replace(',', '.')) || 0;
            const itemTotal = itemPrice * item.quantity;
            total += itemTotal;

            productsHTML += `
                <div class="product-item">
                    <div class="row align-items-center">
                        <div class="col-md-5">
                            <div class="product-info">
                                <h5>${item.name}</h5>
                                <p class="product-brand mb-1">Marca: ${item.brand}</p>
                                <small class="product-code">Código: ${item.barcode}</small>
                            </div>
                        </div>
                        <div class="col-md-2 text-center">
                            <div class="product-price">${item.price}</div>
                        </div>
                        <div class="col-md-3 text-center">
                            <div class="quantity-controls">
                                <button onclick="updateQuantity(${index}, -1)" class="btn btn-sm btn-outline-secondary">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <div class="quantity-display">${item.quantity}</div>
                                <button onclick="updateQuantity(${index}, 1)" class="btn btn-sm btn-outline-secondary">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-2 text-center">
                            <div class="item-total mb-2">€${itemTotal.toFixed(2)}</div>
                            <button onclick="removeItem(${index})" class="btn btn-sm btn-outline-danger">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            `;
        });

        productsList.innerHTML = productsHTML;
        document.getElementById('subtotal').textContent = `€${total.toFixed(2)}`;
        document.getElementById('total-amount').textContent = `€${total.toFixed(2)}`;
    }

    function updateQuantity(index, change) {
        let cart = JSON.parse(localStorage.getItem('shopping_cart') || '[]');
        cart[index].quantity += change;
        if (cart[index].quantity <= 0) cart.splice(index, 1);
        localStorage.setItem('shopping_cart', JSON.stringify(cart));
        displayCartItems();
    }

    function removeItem(index) {
        let cart = JSON.parse(localStorage.getItem('shopping_cart') || '[]');
        cart.splice(index, 1);
        localStorage.setItem('shopping_cart', JSON.stringify(cart));
        displayCartItems();
    }

    function clearCart() {
        if (confirm('Tem certeza que deseja limpar o carrinho?')) {
            localStorage.removeItem('shopping_cart');
            displayCartItems();
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
    displayCartItems();

    // Apenas busca os produtos se o utilizador estiver logado
    <?php if ($is_logged_in): ?>
        fetchProdutosUserLogado();
    <?php endif; ?>
});


    // Função comentada até que seja usada corretamente
    
    async function fetchProdutosUserLogado() {
    try {
        const response = await fetch('http://localhost/site/controller/apiProdutosUser.php'); // Caminho da nova API
        const json = await response.json();

        console.log('Resposta da API:', json);

        if (!json.success) {
            alert('Erro: ' + (json.message || 'Erro desconhecido'));
            return;
        }

        const produtos = json.data.produtos;

        if (produtos.length === 0) {
            document.getElementById('empty-cart').style.display = 'block';
            document.getElementById('cart-items').style.display = 'none';
            return;
        }

        document.getElementById('empty-cart').style.display = 'none';
        document.getElementById('cart-items').style.display = 'block';

        const productsList = document.getElementById('products-list');
        let html = '';
        let total = 0;

        produtos.forEach((item, index) => {
            const preco = parseFloat(item.produto_preco.replace(',', '.')) || 0;
            const quantidade = 1;
            const subtotal = preco * quantidade;
            total += subtotal;

            html += `
                <div class="product-item">
                    <div class="row align-items-center">
                        <div class="col-md-5">
                            <div class="product-info">
                                <h5>${item.produto_nome}</h5>
                                <p class="product-brand mb-1">Marca: ${item.produto_marca}</p>
                                <small class="product-code">Código: ${item.produto_barcode}</small>
                            </div>
                        </div>
                        <div class="col-md-2 text-center">
                            <div class="product-price">€${preco.toFixed(2)}</div>
                        </div>
                        <div class="col-md-3 text-center">
                            <div class="quantity-display">1</div>
                        </div>
                        <div class="col-md-2 text-center">
                            <div class="item-total mb-2">€${subtotal.toFixed(2)}</div>
                        </div>
                    </div>
                </div>
            `;
        });

        productsList.innerHTML = html;
        document.getElementById('subtotal').textContent = `€${total.toFixed(2)}`;
        document.getElementById('total-amount').textContent = `€${total.toFixed(2)}`;
    } catch (err) {
        console.error('Erro ao buscar produtos:', err);
        alert('Erro ao conectar com a API');
    }
}

    
    </script>
</body>
</html>
