<?php
session_start();

// Verificar se o utilizador está logado
$is_logged_in = isset($_SESSION['logged_in']) && $_SESSION['logged_in'];
$user_email = $is_logged_in ? $_SESSION['user_email'] : '';
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
    <link rel="stylesheet" href="assets/css/themify-icons.css">
    <link rel="stylesheet" href="assets/css/slick.css">
    <link rel="stylesheet" href="assets/css/nice-select.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/responsive.css">
    <link rel="stylesheet" href="stylesblock.css">
</head>
<body>
    <!--? Preloader Start -->
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
    <!-- Overlay de Login Obrigatório -->
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
    <!-- Preloader Start -->
    <?php include("cabecalho.php"); ?>
    <main>
        <!--? Hero Start -->
        <div class="slider-area2">
            <div class="slider-height3  hero-overly hero-bg4 d-flex align-items-center">
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
        <!-- Hero End -->
    </main>
    
    <?php include("rodape.php"); ?>
    

    <!-- Scroll Up -->
    <div id="back-top" >
        <a title="Go to Top" href="#"> <i class="fas fa-level-up-alt"></i></a>
    </div>
      <!-- JS here -->

      <script src="./assets/js/vendor/modernizr-3.5.0.min.js"></script>
      <!-- Jquery, Popper, Bootstrap -->
      <script src="./assets/js/vendor/jquery-1.12.4.min.js"></script>
      <script src="./assets/js/popper.min.js"></script>
      <script src="./assets/js/bootstrap.min.js"></script>
      <!-- Jquery Mobile Menu -->
      <script src="./assets/js/jquery.slicknav.min.js"></script>

      <!-- Jquery Slick , Owl-Carousel Plugins -->
      <script src="./assets/js/owl.carousel.min.js"></script>
      <script src="./assets/js/slick.min.js"></script>
      <!-- One Page, Animated-HeadLin -->
      <script src="./assets/js/wow.min.js"></script>
      <script src="./assets/js/animated.headline.js"></script>
      <script src="./assets/js/jquery.magnific-popup.js"></script>

      <!-- Date Picker -->
      <script src="./assets/js/gijgo.min.js"></script>
      <!-- Nice-select, sticky -->
      <script src="./assets/js/jquery.nice-select.min.js"></script>
      <script src="./assets/js/jquery.sticky.js"></script>
      
      <!-- counter , waypoint,Hover Direction -->
      <script src="./assets/js/jquery.counterup.min.js"></script>
      <script src="./assets/js/waypoints.min.js"></script>
      <script src="./assets/js/jquery.countdown.min.js"></script>
      <script src="./assets/js/hover-direction-snake.min.js"></script>

      <!-- contact js -->
      <script src="./assets/js/contact.js"></script>
      <script src="./assets/js/jquery.form.js"></script>
      <script src="./assets/js/jquery.validate.min.js"></script>
      <script src="./assets/js/mail-script.js"></script>
      <script src="./assets/js/jquery.ajaxchimp.min.js"></script>
      
      <!-- Jquery Plugins, main Jquery -->	
      <script src="./assets/js/plugins.js"></script>
      <script src="./assets/js/main.js"></script>


     <script>
        document.addEventListener('DOMContentLoaded', function() {
            displayCartItems();
        });

        function displayCartItems() {
            const cart = JSON.parse(localStorage.getItem('shopping_cart') || '[]');
            
            if (cart.length === 0) {
                // Se não há container específico, pode criar um ou usar o main
                document.querySelector('main').innerHTML += `
                    <div class="container mt-5">
                        <div class="row justify-content-center">
                            <div class="col-md-8 text-center">
                                <h3>Carrinho Vazio</h3>
                                <p>Ainda não adicionou produtos ao carrinho.</p>
                                <a href="barcode/barcode.php" class="btn btn-primary">Escanear Produtos</a>
                            </div>
                        </div>
                    </div>
                `;
                return;
            }
            
            let cartHTML = `
                <div class="container mt-5">
                    <div class="row">
                        <div class="col-12">
                            <h3>Produtos no Carrinho</h3>
                            <div class="cart-items">
            `;
            
            let total = 0;
            cart.forEach((item, index) => {
                const itemPrice = parseFloat(item.price.replace('€', '').replace(',', '.')) || 0;
                const itemTotal = itemPrice * item.quantity;
                total += itemTotal;
                
                cartHTML += `
                    <div class="cart-item card mb-3">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <h5>${item.name}</h5>
                                    <p class="text-muted">Marca: ${item.brand}</p>
                                    <small>Código: ${item.barcode}</small>
                                </div>
                                <div class="col-md-2">
                                    <span class="price">${item.price}</span>
                                </div>
                                <div class="col-md-2">
                                    <div class="quantity-controls">
                                        <button onclick="updateQuantity(${index}, -1)" class="btn btn-sm btn-outline-secondary">-</button>
                                        <span class="mx-2">${item.quantity}</span>
                                        <button onclick="updateQuantity(${index}, 1)" class="btn btn-sm btn-outline-secondary">+</button>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <strong>€${itemTotal.toFixed(2)}</strong>
                                    <br>
                                    <button onclick="removeItem(${index})" class="btn btn-sm btn-danger mt-1">Remover</button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });
            
            cartHTML += `
                            </div>
                            <div class="cart-total mt-4 p-3 bg-light">
                                <h4>Total: €${total.toFixed(2)}</h4>
                                <button class="btn btn-success btn-lg mt-2">Finalizar Compra</button>
                                <button onclick="clearCart()" class="btn btn-warning btn-lg mt-2 ml-2">Limpar Carrinho</button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            document.querySelector('main').innerHTML += cartHTML;
        }

        function updateQuantity(index, change) {
            let cart = JSON.parse(localStorage.getItem('shopping_cart') || '[]');
            cart[index].quantity += change;
            
            if (cart[index].quantity <= 0) {
                cart.splice(index, 1);
            }
            
            localStorage.setItem('shopping_cart', JSON.stringify(cart));
            location.reload(); // Recarregar página para atualizar
        }

        function removeItem(index) {
            let cart = JSON.parse(localStorage.getItem('shopping_cart') || '[]');
            cart.splice(index, 1);
            localStorage.setItem('shopping_cart', JSON.stringify(cart));
            location.reload();
        }

        function clearCart() {
            localStorage.removeItem('shopping_cart');
            location.reload();
        }
        </script>

    </body>
</html>