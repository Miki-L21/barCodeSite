<header>
    <!-- Header Start -->
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
                                <li><a href="barcode.php">Scaner</a></li>
                                <li><a href="contact.php">Contactos</a></li>
                            </ul>
                        </nav>
                    </div>          
                    <!-- Header-btn -->
                    <div class="header-btns d-none d-lg-block f-right">
                        <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']): ?>
                            <span class="welcome-text">Bem-vindo, <?php echo htmlspecialchars($_SESSION['user_username']); ?></span>
                            <a href="logout.php" class="btn ml-3">Logout</a>
                        <?php else: ?>
                            <a href="login.php" class="mr-40"><i class="ti-user"></i> Log in</a>
                        <?php endif; ?>
                        
                        <a href="blog.php" class="btn">Ver Lista</a>
                    </div>
                    <!-- Mobile Menu -->
                    <div class="col-12">
                        <div class="mobile_menu d-block d-lg-none"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Header End -->
</header>

<style>
.welcome-text {
    color: white !important;
    margin-right: 15px;
}
</style>