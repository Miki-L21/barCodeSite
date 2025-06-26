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
    <link rel="manifest" href="site.webmanifest">
    <link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon.ico">
   
	<!-- CSS here -->
	<link rel="stylesheet" href="assets/css/bootstrap.min.css">
	<link rel="stylesheet" href="assets/css/owl.carousel.min.css">
	<link rel="stylesheet" href="assets/css/slicknav.css">
    <link rel="stylesheet" href="assets/css/flaticon.css">
    <link rel="stylesheet" href="assets/css/progressbar_barfiller.css">
    <link rel="stylesheet" href="assets/css/gijgo.css">
    <link rel="stylesheet" href="assets/css/animate.min.css">
    <link rel="stylesheet" href="assets/css/animated-headline.css">
	<link rel="stylesheet" href="assets/css/magnific-popup.css">
	<link rel="stylesheet" href="assets/css/fontawesome-all.min.css">
	<link rel="stylesheet" href="assets/css/themify-icons.css">
	<link rel="stylesheet" href="assets/css/slick.css">
	<link rel="stylesheet" href="assets/css/nice-select.css">
	<link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="stylesblock.css">

    <style>
    /* CSS existente que você já tem... */
    
    /* NOVO CSS PARA O MOTOR DE BUSCA */
    .search-form-container {
        display: flex;
        background: white;
        border-radius: 50px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        max-width: 600px;
        margin: 0 auto;
    }
    
    .search-input {
        flex: 1;
        border: none;
        padding: 15px 25px;
        font-size: 16px;
        outline: none;
        background: transparent;
    }
    
    .search-input::placeholder {
        color: #999;
    }
    
    .search-btn {
        background: #6f42c1;
        color: white;
        border: none;
        padding: 15px 25px;
        cursor: pointer;
        font-size: 16px;
        transition: background 0.3s;
    }
    
    .search-btn:hover {
        background: #5a359a;
    }
    
    .search-results {
        background: white;
        border-radius: 10px;
        margin-top: 10px;
        max-height: 200px;
        overflow-y: auto;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        display: none;
    }
    
    .search-result-item {
        padding: 10px 20px;
        border-bottom: 1px solid #eee;
        cursor: pointer;
        transition: background 0.2s;
    }
    
    .search-result-item:hover {
        background: #f8f9fa;
    }
    
    .search-result-item:last-child {
        border-bottom: none;
    }
    
    /* Destacar produtos filtrados */
    .properties.hidden {
        display: none !important;
    }
    
    .no-results {
        text-align: center;
        padding: 40px;
        color: #666;
        font-size: 18px;
    }
     .load-more-container {
        text-align: center;
        margin-top: 40px;
        padding: 20px;
    }
    
    .load-more-btn {
        background: #6f42c1;
        color: white;
        border: none;
        padding: 15px 30px;
        border-radius: 50px;
        font-size: 16px;
        cursor: pointer;
        transition: all 0.3s;
        box-shadow: 0 5px 15px rgba(111, 66, 193, 0.3);
    }
    
    .load-more-btn:hover {
        background: #5a359a;
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(111, 66, 193, 0.4);
    }
    
    .load-more-btn:disabled {
        background: #ccc;
        cursor: not-allowed;
        transform: none;
        box-shadow: none;
    }
    
    /* Esconder produtos extras inicialmente */
    .properties.hidden-initially {
        display: none !important;
    }
</style>
</head>
<body>
    <!-- ? Preloader Start -->
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
    <div class="slider-area hero-bg2 hero-overly">
    <div class="single-slider hero-overly slider-height2 d-flex align-items-center">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xl-10 col-lg-10">
                    <!-- Hero Caption -->
                    <div class="hero__caption hero__caption2 pt-200">
                        <h1>O que deseja encontrar?</h1>
                    </div>
                    <!-- Motor de Busca -->
                    <div class="search-box mb-100">
                        <div class="search-form-container">
                            <input type="text" id="searchInput" placeholder="Digite o nome do produto (ex: sal, açúcar...)" class="search-input">
                            <button type="button" id="searchBtn" class="search-btn">
                                <i class="ti-search"></i> Procurar
                            </button>
                        </div>
                        <div id="searchResults" class="search-results"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
   


 <div class="listing-area pt-120 pb-120">
    <div class="container">
        <div class="row">
            <div class="col-xl-12 col-lg-12 col-md-12">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="count mb-35">
                            <span>Produtos mais frequentes</span>
                        </div>
                    </div>
                </div>
                <!--? Popular Directory Start -->
                <div class="popular-directorya-area fix">
                    <div class="row">
                        <div class="col-12">
                            <div id="products-container" style="display: flex; flex-wrap: wrap; justify-content: flex-start; gap: 20px;"></div>
                        </div> 
                    </div>
                    
                    <!-- Botão Ver Mais -->
                    <div class="load-more-container">
                        <button id="loadMoreBtn" class="load-more-btn" style="display: none;">
                            <i class="fas fa-plus"></i> Ver Mais Produtos
                        </button>
                    </div>
                </div>            
                <!--? Popular Directory End -->
            </div>
        </div>
    </div>


</div>
<!-- listing-area Area End -->



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
    <!-- Progress -->
    <script src="./assets/js/jquery.barfiller.js"></script>
    
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
            async function carregarProdutos() {
        try {
            const response = await fetch('http://localhost/site/controller/controllerProdutos.php?action=getAll');
            const json = await response.json();
            


            if (json.success) {
                const container = document.getElementById('products-container');
                json.data.products.forEach(produto => {
                    const card = document.createElement('div');
                    card.classList.add('properties', 'properties2', 'mb-30');

                    card.innerHTML = `
                        <div class="properties__card">
                            <div class="properties__img overlay1">
                                <img src="assets/img/gallery/properties1.png" alt="">
                                <div class="img-text">
                                    <span>€${produto.preco}</span>
                                    
                                </div>
                                <div class="icon">
                                    <img src="assets/img/gallery/categori_icon1.png" alt="">
                                </div>
                            </div>
                            <div class="properties__caption">
                                <h3>${produto.nome}</h3>
                                <p>${produto.marca}</p>
                            </div>
                            <div class="properties__footer">
                                <div class="restaurant-name">
                                    <img src="assets/img/gallery/restaurant-icon.png" alt="" width="20">
                                    <h4>${produto.categoria}</h4>
                                </div>
                                <div class="heart">
                                    <img src="assets/img/gallery/heart1.png" alt="" width="20">
                                </div>
                            </div>
                        </div>
                    `;

                    container.appendChild(card);
                });
            } else {
                   alert('Erro: ' + json.message);
            }

        } catch (err) {
            console.error('Erro ao buscar produtos:', err);
            alert('Erro ao conectar com a API');
        }
    }

    // Chamada da função ao carregar a página
    window.onload = carregarProdutos;


        
            document.addEventListener('DOMContentLoaded', function() {
                const searchInput = document.getElementById('searchInput');
                const searchBtn = document.getElementById('searchBtn');
                const productsContainer = document.getElementById('products-container');
                const loadMoreBtn = document.getElementById('loadMoreBtn');
                
                let productsPerPage = 9; // Mostrar 9 produtos inicialmente
                let currentlyShowing = 0;
                let allProducts = [];
                let filteredProducts = [];
                let isSearching = false;
                
                // Função para mostrar produtos com paginação
                function showProducts(products, append = false) {
                    if (!append) {
                        currentlyShowing = 0;
                    }
                    
                    let productsToShow = products.slice(0, currentlyShowing + productsPerPage);
                    currentlyShowing = productsToShow.length;
                    
                    // Esconder todos os produtos primeiro
                    document.querySelectorAll('.properties').forEach(product => {
                        product.style.display = 'none';
                    });
                    
                    // Mostrar apenas os produtos que devem aparecer
                    productsToShow.forEach(product => {
                        product.style.display = 'block';
                    });
                    
                    // Controlar visibilidade do botão "Ver Mais"
                    if (currentlyShowing >= products.length) {
                        loadMoreBtn.style.display = 'none';
                    } else {
                        loadMoreBtn.style.display = 'block';
                        loadMoreBtn.innerHTML = `<i class="fas fa-plus"></i> Ver Mais Produtos (${products.length - currentlyShowing} restantes)`;
                    }
                }
                
                // Função para carregar mais produtos
                function loadMoreProducts() {
                    const products = isSearching ? filteredProducts : allProducts;
                    showProducts(products, true);
                }
                
                // Função para filtrar produtos (atualizada)
                function filterProducts(searchTerm) {
                    const products = document.querySelectorAll('.properties');
                    filteredProducts = [];
                    
                    products.forEach(product => {
                        const productName = product.querySelector('h3').textContent.toLowerCase();
                        const productBrand = product.querySelector('.properties__caption p') ? 
                                            product.querySelector('.properties__caption p').textContent.toLowerCase() : '';
                        
                        if (searchTerm === '' || 
                            productName.includes(searchTerm.toLowerCase()) || 
                            productBrand.includes(searchTerm.toLowerCase())) {
                            filteredProducts.push(product);
                        }
                    });
                    
                    isSearching = searchTerm !== '';
                    
                    if (isSearching) {
                        showProducts(filteredProducts);
                        showNoResultsMessage(filteredProducts.length === 0);
                    } else {
                        showProducts(allProducts);
                        showNoResultsMessage(false);
                    }
                }
                
                // Função para mostrar mensagem de "sem resultados"
                function showNoResultsMessage(show) {
                    let noResultsDiv = document.querySelector('.no-results');
                    
                    if (show && !noResultsDiv) {
                        noResultsDiv = document.createElement('div');
                        noResultsDiv.className = 'no-results';
                        noResultsDiv.innerHTML = '<p>Nenhum produto encontrado com esse nome.</p>';
                        productsContainer.appendChild(noResultsDiv);
                    } else if (!show && noResultsDiv) {
                        noResultsDiv.remove();
                    }
                }
                
                // Inicializar quando a página carregar
                function initializeProducts() {
                    const products = document.querySelectorAll('.properties');
                    allProducts = Array.from(products);
                    
                    if (allProducts.length > 0) {
                        showProducts(allProducts);
                    }
                }
                
                // Event listeners
                loadMoreBtn.addEventListener('click', loadMoreProducts);
                
                searchBtn.addEventListener('click', function() {
                    const searchTerm = searchInput.value.trim();
                    filterProducts(searchTerm);
                });
                
                searchInput.addEventListener('input', function() {
                    const searchTerm = this.value.trim();
                    filterProducts(searchTerm);
                });
                
                searchInput.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        const searchTerm = this.value.trim();
                           filterProducts(searchTerm);
                    }  
                });
                
                // Inicializar quando os produtos carregarem
                // Você pode chamar esta função depois de carregar os produtos via AJAX
                setTimeout(initializeProducts, 1000); // Aguardar 1 segundo para garantir que produtos carregaram
            });



        </script>
    
    </body>
</html>