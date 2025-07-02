<div class="header-area header-transparent">
    <div class="main-header header-sticky">
        <div class="container-fluid">
            <div class="menu-wrapper d-flex align-items-center justify-content-between">
                <!-- Logo -->
                <div class="logo">
                    <a href="index.php"><img src="assets/img/logo/new_logo.png" alt=""></a>
                </div>
                <!-- Main-menu -->
                <div class="main-menu f-right d-none d-lg-block">
                    <nav>
                        <ul id="navigation">
                            <li><a href="index.php">Home</a></li>
                            <li><a href="listing.php">Produtos</a></li> 
                            <li><a href="barcode.php">Scanner</a></li>
                            <li><a href="contact.php">Contactos</a></li>
                        </ul>
                    </nav>
                </div>          
                <!-- Header-btn - Sempre visível -->
                <div class="header-btns">
                    <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']): ?>
                        <div class="user-info">
                            <span class="welcome-text">
                                <i class="ti-user"></i>
                                <span class="username">Olá, <?php echo htmlspecialchars($_SESSION['user_username']); ?>!</span>
                            </span>
                        </div>
                        <div class="btn-group">
                            <a href="blog.php" class="btn btn-primary">
                                <i class="bi bi-bag-check"></i>
                                <span class="btn-text">Ver Lista</span>
                            </a>
                            <a href="logout.php" class="btn btn-logout">
                                <i class="ti-power-off"></i>
                                <span class="btn-text">Sair</span>
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="btn-group">
                            <a href="login.php" class="btn btn-login">
                                <i class="ti-user"></i>
                                <span class="btn-text">Entrar</span>
                            </a>
                            <a href="blog.php" class="btn btn-primary">
                                <i class="bi bi-bag-check"></i>
                                <span class="btn-text">Ver Lista</span>
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
                <!-- Mobile Menu -->
                <div class="col-12">
                    <div class="mobile_menu d-block d-lg-none" id="mobileMenuToggle"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="assets/css/cabecalho.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

<script src="assets/js/cabecalho.js"></script>
