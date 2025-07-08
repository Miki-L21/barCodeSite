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
    <title>Lista</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" type="image/x-icon" href="assets/img/icon.ico">

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
    <link rel="stylesheet" href="assets/css/carrinho.css">
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
                                        <div class="col-md-6 mb-3 mb-md-0">
                                            <h4><i class="fas fa-list"></i> Produtos no Carrinho</h4>
                                        </div>
                                        <div class="col-md-6 text-md-right text-center">
                                            <button onclick="clearPurchasedStatus()" class="btn btn-outline-warning">
                                                <i class="bi bi-clipboard-x-fill"></i> Limpar Comprados
                                            </button>
                                            <button onclick="markAllAsPurchased()" class="btn btn-outline-danger ml-2">
                                                <i class="bi bi-clipboard-check-fill"></i> Tudo comprado
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div id="products-list" class="products-list"></div>
                                <div class="cart-summary mt-4">
                                    <div class="row mt-3">
                                        <div class="col text-right">
                                            <p>Total de Produtos: <strong id="total-products">0</strong></p>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="cart-actions">
                                                <a href="barcode.php" class="btn">
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
    // Apenas busca os produtos se o utilizador estiver logado
    if (<?php echo $is_logged_in ? 'true' : 'false'; ?>) {
        fetchProdutosUserLogado();
    } else {
        displayCartItems();
    }
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
    let totalProducts = 0;
    let totalPrice = 0;
    let produtosGlobal = []; 

    cart.forEach((item, index) => {
        const itemPrice = parseFloat(item.price.replace('€', '').replace(',', '.')) || 0;
        const itemTotal = itemPrice * item.quantity;
        totalProducts += item.quantity;
        totalPrice += itemTotal;

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
                    <div class="col-md-3 text-center">
                        <div class="quantity-controls">
                            <button onclick="updateQuantity(${index}, -1)" class="quantity-btn">-</button>
                            <div class="quantity-display">${item.quantity}</div>
                            <button onclick="updateQuantity(${index}, 1)" class="quantity-btn">+</button>
                        </div>
                    </div>
                    <div class="col-md-2 text-center">
                        <button onclick="removeItem(${index})" class="btn btn-sm btn-outline-danger">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
    });

    productsList.innerHTML = productsHTML;
    document.getElementById('total-products').textContent = totalProducts;
    document.getElementById('total-amount').textContent = `€${totalPrice.toFixed(2)}`;
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

async function clearCart() {
    if (!confirm('Tem certeza que deseja limpar o carrinho? Isso removerá todos os produtos da base de dados.')) {
        return;
    }

    try {
        localStorage.removeItem('shopping_cart');
        localStorage.removeItem('purchased_items');

        if (<?php echo $is_logged_in ? 'true' : 'false'; ?>) {
            const response = await fetch('http://localhost/site/controller/apiProdutosUser.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'clear_all'
                })
            });

            const result = await response.json();

            if (result.success) {
                console.log('Todos os produtos foram removidos da base de dados!');
                fetchProdutosUserLogado();
            } else {
                alert('Erro ao limpar carrinho na base de dados: ' + (result.message || 'Erro desconhecido'));
                displayCartItems();
            }
        } else {
            displayCartItems();
        }

    } catch (error) {
        console.error('Erro ao limpar carrinho:', error);
        alert('Erro ao conectar com a API para limpar o carrinho');
        displayCartItems();
    }
}

async function fetchProdutosUserLogado() {
    try {
        const response = await fetch('http://localhost/site/controller/apiProdutosUser.php');
        const json = await response.json();

        console.log('Resposta da API:', json);

        if (!json.success) {
            alert('Erro: ' + (json.message || 'Erro desconhecido'));
            return;
        }

        const produtos = json.data.produtos;
        produtosGlobal = produtos;

        if (produtos.length === 0) {
            document.getElementById('empty-cart').style.display = 'block';
            document.getElementById('cart-items').style.display = 'none';
            return;
        }

        document.getElementById('empty-cart').style.display = 'none';
        document.getElementById('cart-items').style.display = 'block';

        const productsList = document.getElementById('products-list');
        let html = '';
        let totalProducts = 0;
        let totalPrice = 0;

        produtos.forEach((item, index) => {
            const preco = parseFloat(item.produto_preco.replace(',', '.')) || 0;
            const quantidade = parseInt(item.quantidade) || 1;
            totalProducts += quantidade;

            // Usar o valor do campo 'comprado' da base de dados
            const isPurchased = item.comprado === true || item.comprado === 't';
            const purchasedClass = isPurchased ? 'purchased' : '';
            const buttonClass = isPurchased ? 'btn-purchased' : 'btn-not-purchased';
            const buttonText = isPurchased ? 'Comprado' : 'Não Comprado';
            const buttonIcon = isPurchased ? 'fas fa-check' : 'fas fa-times';

            html += `
                <div class="product-item ${purchasedClass}" id="product-${item.id}">
                    <div class="row align-items-center">
                        <div class="col-lg-3 col-md-4 col-sm-12">
                            <div class="product-info">
                                <h5>${item.produto_nome}</h5>
                                <p class="product-brand mb-1">Marca: ${item.produto_marca}</p>
                                <small class="product-code">Código: ${item.produto_barcode}</small>
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-6 col-6 text-center">
                            <div class="quantity-controls">
                                <button onclick="updateQuantidadeAPI(${item.id}, ${quantidade - 1})" 
                                        class="quantity-btn" 
                                        ${quantidade <= 1 ? 'disabled' : ''}>
                                    -
                                </button>
                                <div class="quantity-display">${quantidade}</div>
                                <button onclick="updateQuantidadeAPI(${item.id}, ${quantidade + 1})" 
                                        class="quantity-btn">
                                    +
                                </button>
                            </div>
                        </div>
                        <div class="col-lg-1 col-md-1 col-sm-6 col-6 text-center">
                            <button onclick="removeProductFromAPI(${item.id})" 
                                    class="btn btn-sm " 
                                    title="Remover produto">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                        <div class="col-lg-2 col-md-1 col-sm-6 col-6 text-center">
                            <button onclick="togglePurchasedAPI(${item.id})" 
                                    class="btn purchase-toggle ${buttonClass}" 
                                    id="btn-${item.id}">
                                <i class="${buttonIcon}"></i>
                            </button>
                        </div>
                    </div>
                </div>
            `;
        });

        productsList.innerHTML = html;
        document.getElementById('total-products').textContent = totalProducts;
    } catch (err) {
        console.error('Erro ao buscar produtos:', err);
        alert('Erro ao conectar com a API');
    }
}

// Função para atualizar quantidade via API
async function updateQuantidadeAPI(produtoUserId, novaQuantidade) {
    if (novaQuantidade < 0) return;

    try {
        const response = await fetch('http://localhost/site/controller/apiProdutosUser.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: 'update_quantity',
                produto_user_id: produtoUserId,
                quantidade: novaQuantidade
            })
        });

        const result = await response.json();

        if (result.success) {
            fetchProdutosUserLogado(); // Recarregar a lista
        } else {
            alert('Erro ao atualizar quantidade: ' + (result.message || 'Erro desconhecido'));
        }
    } catch (error) {
        console.error('Erro ao atualizar quantidade:', error);
        alert('Erro ao conectar com a API para atualizar a quantidade');
    }
}

async function removeProductFromAPI(produtoUserId) {
    if (!confirm('Tem certeza que deseja remover este produto do carrinho?')) {
        return;
    }

    try {
        const response = await fetch('http://localhost/site/controller/apiProdutosUser.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: 'remove',
                produto_user_id: produtoUserId
            })
        });

        const result = await response.json();

        if (result.success) {
            fetchProdutosUserLogado();
            console.log('Produto removido com sucesso!');
        } else {
            alert('Erro ao remover produto: ' + (result.message || 'Erro desconhecido'));
        }
    } catch (error) {
        console.error('Erro ao remover produto:', error);
        alert('Erro ao conectar com a API para remover o produto');
    }
}

// Nova função para atualizar status comprado via API
async function togglePurchasedAPI(produtoUserId) {
    try {
        // Primeiro, obter o status atual
        const productElement = document.getElementById(`product-${produtoUserId}`);
        const isPurchased = productElement.classList.contains('purchased');

        console.log('Enviando status comprado:', !isPurchased);
        
        const response = await fetch('http://localhost/site/controller/apiProdutosUser.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: 'update_status_comprado',
                produto_user_id: produtoUserId,
                comprado: !isPurchased
            })
        });

        const result = await response.json();

        if (result.success) {
            // Atualizar a interface imediatamente
            const buttonElement = document.getElementById(`btn-${produtoUserId}`);
            
            if (!isPurchased) {
                productElement.classList.add('purchased');
                buttonElement.className = 'btn purchase-toggle btn-purchased';
                buttonElement.innerHTML = '<i class="fas fa-check"></i>';
            } else {
                productElement.classList.remove('purchased');
                buttonElement.className = 'btn purchase-toggle btn-not-purchased';
                buttonElement.innerHTML = '<i class="fas fa-times"></i>';
            }
            
            console.log('Status comprado atualizado com sucesso!');
        } else {
            alert('Erro ao atualizar status comprado: ' + (result.message || 'Erro desconhecido'));
        }
    } catch (error) {
        console.error('Erro ao atualizar status comprado:', error);
        alert('Erro ao conectar com a API para atualizar o status comprado');
    }
}

// Função para limpar status comprado de todos os produtos
async function clearPurchasedStatus() {
    if (!confirm('Deseja limpar o estado de todos os produtos comprados?')) {
        return;
    }

    try {
        const response = await fetch('http://localhost/site/controller/apiProdutosUser.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: 'clear_comprado_status'
            })
        });

        const result = await response.json();

        if (result.success) {
            fetchProdutosUserLogado(); // Recarregar a lista
            console.log('Status comprado limpo para todos os produtos!');
        } else {
            alert('Erro ao limpar status comprado: ' + (result.message || 'Erro desconhecido'));
        }
    } catch (error) {
        console.error('Erro ao limpar status comprado:', error);
        alert('Erro ao conectar com a API para limpar o status comprado');
    }
}

// Função para marcar todos os produtos como comprados
async function markAllAsPurchased() {
    if (!confirm('Deseja marcar todos os produtos como comprados?')) {
        return;
    }

    try {
        const response = await fetch('http://localhost/site/controller/apiProdutosUser.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: 'mark_all_purchased'
            })
        });

        const result = await response.json();

        if (result.success) {
            fetchProdutosUserLogado(); // Recarregar a lista
            console.log('Todos os produtos marcados como comprados!');
        } else {
            alert('Erro ao marcar todos como comprados: ' + (result.message || 'Erro desconhecido'));
        }
    } catch (error) {
        console.error('Erro ao marcar todos como comprados:', error);
        alert('Erro ao conectar com a API para marcar todos como comprados');
    }
}
    </script>
</body>
</html>