
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
    <!--? Hero Area Start-->
    <div class="slider-area hero-bg2 hero-overly">
        <div class="single-slider hero-overly  slider-height2 d-flex align-items-center">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-xl-10 col-lg-10">
                        <!-- Hero Caption -->
                        <div class="hero__caption hero__caption2 pt-200">
                            <h1>Explore what you are finding</h1>
                        </div>
                        <!--Hero form -->
                        <form action="#" class="search-box mb-100">
                            <div class="input-form">
                                <input type="text" placeholder="What are you finding?">
                            </div>
                            <div class="select-form">
                                <div class="select-itms">
                                    <select name="select" id="select1">
                                        <option value="">In where?</option>
                                        <option value="">Catagories One</option>
                                        <option value="">Catagories Two</option>
                                        <option value="">Catagories Three</option>
                                        <option value="">Catagories Four</option>
                                    </select>
                                </div>
                            </div>
                            <div class="search-form">
                                <a href="#"><i class="ti-search"></i> Search</a>
                            </div>	
                        </form>	
                    </div>
                </div>
            </div>
        </div>
    </div>
   


  <!-- listing Area Start -->
  <div class="listing-area pt-120 pb-120">
    <div class="container">
        <div class="row">
            <!--? Left content -->
            <div class="col-xl-4 col-lg-4 col-md-6">
                <div class="row">
                    <div class="col-12">
                            <div class="small-section-tittle2 mb-45">
                            <h4>Advanced Filter</h4>
                        </div>
                    </div>
                </div>
                <!-- Job Category Listing start -->
                <div class="category-listing mb-50">
                    <!-- single one -->
                    <div class="single-listing">
                        <!-- Select City items start -->
                        <div class="select-job-items2">
                            <select name="select2">
                                <option value="">City</option>
                                <option value="">Dhaka</option>
                                <option value="">india</option>
                                <option value="">UK</option>
                                <option value="">US</option>
                                <option value="">Pakistan</option>
                            </select>
                        </div>
                        <!--  Select City items End-->
                        <!-- Select State items start -->
                        <div class="select-job-items2">
                            <select name="select2">
                                <option value="">State</option>
                                <option value="">Dhaka</option>
                                <option value="">Mirpur</option>
                                <option value="">Danmondi</option>
                                <option value="">Rampura</option>
                                <option value="">Htizill</option>
                            </select>
                        </div>
                        <!--  Select State items End-->
                        <!-- Select km items start -->
                        <div class="select-job-items2">
                            <select name="select2">
                                <option value="">Near 1 km</option>
                                <option value="">2 km</option>
                                <option value="">3 km</option>
                                <option value="">4 km</option>
                                <option value="">5 km</option>
                                <option value="">6 km</option>
                            </select>
                        </div>
                        <!--  Select km items End-->
                        <!-- select-Categories start -->
                        <div class="select-Categories pt-80 pb-30">
                            <div class="small-section-tittle2 mb-20">
                                <h4>Price range</h4>
                            </div>
                            <label class="container">$50 - $150
                                <input type="checkbox">
                                <span class="checkmark"></span>
                            </label>
                            <label class="container">$150 - $300
                                <input type="checkbox" checked="checked active">
                                <span class="checkmark"></span>
                            </label>
                            <label class="container">$300 - $500
                                <input type="checkbox">
                                <span class="checkmark"></span>
                            </label>
                            <label class="container">$500 - $1000
                                <input type="checkbox">
                                <span class="checkmark"></span>
                            </label>
                            <label class="container">$1000 - Above
                                <input type="checkbox">
                                <span class="checkmark"></span>
                            </label>
                        </div>
                        <!-- select-Categories End -->
                        <!-- select-Categories start -->
                        <div class="select-Categories">
                            <div class="small-section-tittle2 mb-20">
                                <h4>Tags</h4>
                            </div>
                            <label class="container">Wireless Internet
                                <input type="checkbox">
                                <span class="checkmark"></span>
                            </label>
                            <label class="container">Accepts Credit Cards
                                <input type="checkbox" checked="checked active">
                                <span class="checkmark"></span>
                            </label>
                            <label class="container">Smoking Allowed
                                <input type="checkbox">
                                <span class="checkmark"></span>
                            </label>
                            <label class="container">Parking Street
                                <input type="checkbox">
                                <span class="checkmark"></span>
                            </label>
                            <label class="container">Coupons
                                <input type="checkbox">
                                <span class="checkmark"></span>
                            </label>
                        </div>
                        <!-- select-Categories End -->
                    </div>
                </div>
                <!-- Job Category Listing End -->
            </div>
            <!--?  Right content -->
            <div class="col-xl-8 col-lg-8 col-md-6">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="count mb-35">
                            <span>5432 Listings are available</span>
                        </div>
                    </div>
                </div>
                <!--? Popular Directory Start -->
                <div class="popular-directorya-area fix">
                        <div class="row">
                            <div class="col-lg-6">
                                <!-- Single -->
                                <div class="properties properties2 mb-30">
                                    <div class="properties__card">
                                        <div class="properties__img overlay1">
                                            <a href="#"><img src="assets/img/gallery/properties1.png" alt=""></a>
                                            <div class="img-text">
                                                <span>$$$</span>
                                                <span>Closed</span>
                                            </div>
                                            <div class="icon">
                                                <img src="assets/img/gallery/categori_icon1.png" alt=""> 
                                            </div>
                                        </div>
                                        <div class="properties__caption">
                                            <h3><a href="#">Urban areas</a></h3>
                                            <p>Let's uncover the best places to eat, drink</p>
                                        </div>
                                        <div class="properties__footer d-flex justify-content-between align-items-center">
                                            <div class="restaurant-name">
                                                <img src="assets/img/gallery/restaurant-icon.png" alt="">
                                                <h3>Food & Restaurant</h3>
                                            </div>
                                            <div class="heart">
                                                <img src="assets/img/gallery/heart1.png" alt="">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <!-- Single -->
                                <div class="properties properties2 mb-30">
                                    <div class="properties__card">
                                        <div class="properties__img overlay1">
                                            <a href="#"><img src="assets/img/gallery/properties2.png" alt=""></a>
                                            <div class="img-text">
                                                <span>$$$</span>
                                                <span>Closed</span>
                                            </div>
                                            <div class="icon">
                                                <img src="assets/img/gallery/categori_icon1.png" alt=""> 
                                            </div>
                                        </div>
                                        <div class="properties__caption">
                                            <h3><a href="#">Urban areas</a></h3>
                                            <p>Let's uncover the best places to eat, drink</p>
                                        </div>
                                        <div class="properties__footer d-flex justify-content-between align-items-center">
                                            <div class="restaurant-name">
                                                <img src="assets/img/gallery/restaurant-icon.png" alt="">
                                                <h3>Food & Restaurant</h3>
                                            </div>
                                            <div class="heart">
                                                <img src="assets/img/gallery/heart1.png" alt="">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <!-- Single -->
                                <div class="properties properties2 mb-30">
                                    <div class="properties__card">
                                        <div class="properties__img overlay1">
                                            <a href="#"><img src="assets/img/gallery/properties3.png" alt=""></a>
                                            <div class="img-text">
                                                <span>$$$</span>
                                                <span>Closed</span>
                                            </div>
                                            <div class="icon">
                                                <img src="assets/img/gallery/categori_icon1.png" alt=""> 
                                            </div>
                                        </div>
                                        <div class="properties__caption">
                                            <h3><a href="#">Urban areas</a></h3>
                                            <p>Let's uncover the best places to eat, drink</p>
                                        </div>
                                        <div class="properties__footer d-flex justify-content-between align-items-center">
                                            <div class="restaurant-name">
                                                <img src="assets/img/gallery/restaurant-icon.png" alt="">
                                                <h3>Food & Restaurant</h3>
                                            </div>
                                            <div class="heart">
                                                <img src="assets/img/gallery/heart1.png" alt="">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <!-- Single -->
                                <div class="properties properties2 mb-30">
                                    <div class="properties__card">
                                        <div class="properties__img overlay1">
                                            <a href="#"><img src="assets/img/gallery/properties3.png" alt=""></a>
                                            <div class="img-text">
                                                <span>$$$</span>
                                                <span>Closed</span>
                                            </div>
                                            <div class="icon">
                                                <img src="assets/img/gallery/categori_icon1.png" alt=""> 
                                            </div>
                                        </div>
                                        <div class="properties__caption">
                                            <h3><a href="#">Urban areas</a></h3>
                                            <p>Let's uncover the best places to eat, drink</p>
                                        </div>
                                        <div class="properties__footer d-flex justify-content-between align-items-center">
                                            <div class="restaurant-name">
                                                <img src="assets/img/gallery/restaurant-icon.png" alt="">
                                                <h3>Food & Restaurant</h3>
                                            </div>
                                            <div class="heart">
                                                <img src="assets/img/gallery/heart1.png" alt="">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                </div>
                <!--? Popular Directory End -->
                <!--Pagination Start  -->
                <div class="pagination-area text-center">
                    <div class="container">
                        <div class="row">
                            <div class="col-xl-12">
                                <div class="single-wrap d-flex justify-content-center">
                                    <nav aria-label="Page navigation example">
                                        <ul class="pagination justify-content-start " id="myDIV">
                                            <li class="page-item"><a class="page-link" href="#"><span class="ti-angle-left"></span></a></li>
                                            <li class="page-item active"><a class="page-link" href="#">01</a></li>
                                            <li class="page-item"><a class="page-link" href="#">02</a></li>
                                            <li class="page-item"><a class="page-link" href="#">03</a></li>
                                            <li class="page-item"><a class="page-link" href="#"><span class="ti-angle-right"></span></a></li>
                                        </ul>
                                    </nav>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--Pagination End  -->
            </div>
        </div>
    </div>
</div>
<!-- listing-area Area End -->



    <!--? Popular Locations Start 01-->
    <div class="popular-location border-bottom section-padding40">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <!-- Section Tittle -->
                    <div class="section-tittle text-center mb-80">
                        <h2>News & Updates</h2>
                        <p>Let's uncover the best places to eat, drink, and shop nearest to you.</p>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-6 col-md-6 col-sm-12">
                    <div class="single-location mb-30">
                        <div class="location-img">
                            <img src="assets/img/gallery/home-blog1.png" alt="">
                        </div>
                        <div class="location-details">
                            <a href="#" class="location-btn">Tips</a>
                            <ul>
                                <li>12 March   I   by Alan</li>
                            </ul>
                            <p><a href="blog_details.html">The Best SPA Salons For Your Relaxation</a></p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12">
                    <div class="single-location mb-30">
                        <div class="location-img">
                            <img src="assets/img/gallery/home-blog2.png" alt="">
                        </div>
                        <div class="location-details">
                            <a href="#" class="location-btn">Tips</a>
                            <ul>
                                <li>12 March   I   by Alan</li>
                            </ul>
                            <p><a href="blog_details.html">The Best SPA Salons For Your Relaxation</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Popular Locations End -->
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

    
    </body>
</html>