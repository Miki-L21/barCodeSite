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
    <title>Home</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" type="image/x-icon" href="assets/img/icon.ico">

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
    <!-- Preloader Start -->
    <?php include("cabecalho.php"); ?>
    <main>
        <!--? Hero Area Start-->
        <div class="slider-area hero-bg1 hero-overly">
            <div class="single-slider hero-overly  slider-height1 d-flex align-items-center">
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-xl-10 col-lg-10">
                            <!-- Hero Caption -->
                            <div class="hero__caption pt-100">
                                <h1>Identifica o produto<br>e faz a tua lista</h1>
                                <p>Faz scan do teu produto clicando no botão aqui embaixo, e adiciona-o na tua lista de compras.</p>
                            </div> 
                            <!--Hero form -->
                            <!-- hero category1 img -->
                            <div class="category-img text-center">
                                <a href="barcode.php"> <img src="assets/img/scanear.png" alt=""></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--Hero Area End-->
        <!--? Want To work 01-->
        <section class="wantToWork-area">
            <div class="container">
                <div class="wants-wrapper w-padding2">
                    <div class="row align-items-center justify-content-between">
                        <div class="col-xl-7 col-lg-9 col-md-8">
                            <div class="wantToWork-caption wantToWork-caption2">
                                <h2>O que fazemos ?</h2>
                                <p>A nossa intenção é ajudar a identificar e fazer a sua lista de compras através dos códigos de barra dos produtos.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- Want To work End -->
        <!--? Our Services Start -->
        <div class="our-services  border-bottom">
            <div class="container">
                <div class="row">
                    <div class=" col-lg-4 col-md-6 col-sm-6">
                        <div class="single-services mb-30">
                            <div class="services-ion">
                                <span>01</span>
                            </div>
                            <div class="services-cap">
                                <h5>Utilidade</h5>
                                <p>É bastante útil, porém pode conter alguma falta de informação, ou informação errada, caso isso aconteça envie esse erro para o nosso serviço de ajuda.</p>
                            </div>
                        </div>
                    </div>
                    <div class=" col-lg-4 col-md-6 col-sm-6">
                        <div class="single-services mb-30">
                            <div class="services-ion">
                                <span>02</span>
                            </div>
                            <div class="services-cap">
                                <h5>Desenvolvimento</h5>
                                <p>O site encontra-se em mudanças, podendo ser melhorado futuramente.</p>
                            </div>
                        </div>
                    </div>
                    <div class=" col-lg-4 col-md-6 col-sm-6">
                        <div class="single-services mb-30">
                            <div class="services-ion">
                                <span>03</span>
                            </div>
                            <div class="services-cap">
                                <h5>Apoio</h5>
                                <p>Pode contar com os nossos serviços de apoio.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
        <!-- Our Services End -->
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

    <script>script>// Adiciona este script no index.php e outras páginas que precisam de autenticação

async function checkUserSession() {
    try {
        const formData = new FormData();
        formData.append('action', 'check_session');
        
        const response = await fetch('/site/controller/controllerUser.php', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success && data.logged_in) {
            // Utilizador está logado
            console.log('Utilizador logado:', data.user.email);
            showUserInfo(data.user);
        } else {
            // Utilizador não está logado - não fazer nada (página é pública)
            console.log('Utilizador não está logado');
            // Não redirecionar
        }
    } catch (error) {
        console.error('Erro ao verificar sessão:', error);
    }
}



async function logout() {
    try {
        const formData = new FormData();
        formData.append('action', 'logout');
        
        const response = await fetch('/site/controller/controllerUser.php', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            alert('Logout realizado com sucesso!');
            window.location.href = '/site/template/listco-master/login.php';
        }
    } catch (error) {
        console.error('Erro no logout:', error);
    }
}

// Verificar sessão quando a página carregar
document.addEventListener('DOMContentLoaded', function() {
    checkUserSession();
});</script>
    
    </body>
</html>