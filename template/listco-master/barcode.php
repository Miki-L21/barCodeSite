<?php
session_start();

// Verificar se o utilizador está logado
$is_logged_in = isset($_SESSION['logged_in']) && $_SESSION['logged_in'];
$user_email = $is_logged_in ? $_SESSION['user_email'] : '';
?>


<!doctype html>
<html class="no-js" lang="pt">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Leitor de Código de Barras - DirectoryListing</title>
    <meta name="description" content="Leitor de código de barras integrado">
    <meta name="viewport" content="width=device-width, initial-scale=1">
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
    <link rel="stylesheet" href="styleqrcode.css">
    <link rel="stylesheet" href="stylesblock.css">

    
    <!-- Quagga.js for barcode scanning -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/quagga/0.12.1/quagga.min.js"></script>
</head>

<body>
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
            
            <div class="button-container">
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
            <div class="single-slider hero-overly slider-height2 d-flex align-items-center">
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-xl-10 col-lg-10">
                            <!-- Hero Caption -->
                            <div class="hero__caption pt-100">
                                <h1>Leitor de Código de Barras</h1>
                                <p>Escaneie códigos de barras e descubra informações detalhadas dos produtos</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--Hero Area End-->

        <!--? Scanner Section Start -->
        <section class="wantToWork-area">
            <div class="container">
                <div class="wants-wrapper w-padding2">
                    <div class="row">
                        <div class="col-xl-12">
                            <div class="section-title">
                                 Scanner
                            </div>
                            <div class="section-subtitle">
                                Mantenha o código de barras centrado e estável para melhor leitura
                            </div>
                            
                            <div class="scanner-container">
                                <div id="interactive"></div>
                                <div class="scan-line" id="scan-line" style="display: none;"></div>
                                <div class="controls">
                                    <button id="start-scanner" class="start-btn">Iniciar Scanner</button>
                                    <button id="stop-scanner" class="stop-btn" disabled>Parar Scanner</button>
                                </div>
                            </div>
                            
                            <div id="status-message" class="status-message" style="display: none;"></div>
                            <div id="barcode-result" class="barcode-display" style="display: none;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- Scanner Section End -->

        <!--? Products Section Start -->
        <div class="our-services border-bottom">
            <div class="container">
                <div class="row">
                    <div class="col-xl-12">
                        <div class="section-title">
                            🛍️ Produtos Escaneados
                        </div>
                        <div class="section-subtitle">
                            Histórico de produtos identificados pelo scanner
                        </div>
                        
                        <div class="loading" id="loading">
                            <div class="spinner"></div>
                            <p>Buscando informações do produto...</p>
                        </div>
                        
                        <div class="product-list" id="product-list">
                            <div class="no-products">
                                Nenhum produto escaneado ainda.<br>
                                Use o scanner acima para adicionar produtos!
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Products Section End -->
    </main>

    <?php include("rodape.php"); ?>

    <!-- Scroll Up -->
    <div id="back-top">
        <a title="Go to Top" href="#"> <i class="fas fa-level-up-alt"></i></a>
    </div>

    <script>

       // Adiciona este script no index.php e outras páginas que precisam de autenticação

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
});
        class BarcodeReader {
            constructor() {
                this.isScanning = false;
                this.products = [];
                this.lastScannedCode = null;
                this.lastScannedTime = 0;
                this.scanCooldown = 5000;
                this.detectionCount = {};
                this.minDetections = 5;
                this.initializeElements();
                this.bindEvents();
                this.initializeDatabase();
            }

            initializeElements() {
                this.startBtn = document.getElementById('start-scanner');
                this.stopBtn = document.getElementById('stop-scanner');
                this.barcodeResult = document.getElementById('barcode-result');
                this.productList = document.getElementById('product-list');
                this.loading = document.getElementById('loading');
                this.statusMessage = document.getElementById('status-message');
                this.scanLine = document.getElementById('scan-line');
            }

            bindEvents() {
                this.startBtn.addEventListener('click', () => this.startScanner());
                this.stopBtn.addEventListener('click', () => this.stopScanner());
            }

            initializeDatabase() {
                this.localProducts = {
                    '5601012001': {
                        name: 'Leite Mimosa Meio Gordo UHT',
                        brand: 'Mimosa',
                        category: 'Laticínios > Leite',
                        price: '€1.25',
                        description: 'Leite UHT meio gordo 1L'
                    },
                    '5601234567890': {
                        name: 'Álcool Gel Desinfetante',
                        brand: 'Genérico',
                        category: 'Higiene > Desinfetante',
                        price: '€2.50',
                        description: 'Álcool gel para desinfeção das mãos'
                    },
                    '8410076472120': {
                        name: 'Leite Condensado La Lechera',
                        brand: 'La Lechera',
                        category: 'Laticínios > Leite Condensado',
                        price: '€1.89',
                        description: 'Leite condensado açucarado'
                    },
                    '5601051931070': {
                        name: 'Azeite Gallo Extra Virgem',
                        brand: 'Gallo',
                        category: 'Condimentos > Azeite',
                        price: '€4.99',
                        description: 'Azeite extra virgem português'
                    }
                };
            }

            showStatusMessage(message, type = 'info') {
                this.statusMessage.textContent = message;
                this.statusMessage.className = `status-message ${type}`;
                this.statusMessage.style.display = 'block';
                
                setTimeout(() => {
                    this.statusMessage.style.display = 'none';
                }, 3000);
            }

            startScanner() {
                if (this.isScanning) return;

                // Limpar qualquer instância anterior do Quagga
                this.cleanupQuagga();

                this.showStatusMessage('A inicializar câmera...', 'info');

                Quagga.init({
                    inputStream: {
                        name: "Live",
                        type: "LiveStream",
                        target: document.querySelector('#interactive'),
                        constraints: {
                            width: 640,
                            height: 480,
                            facingMode: "environment"
                        }
                    },
                    locator: {
                        patchSize: "large",
                        halfSample: false
                    },
                    numOfWorkers: 4,
                    frequency: 3,
                    decoder: {
                        readers: [
                            "ean_reader",
                            "ean_8_reader"
                        ],
                        debug: {
                            drawBoundingBox: false,
                            showFrequency: false,
                            drawScanline: false,
                            showPattern: false
                        }
                    },
                    locate: true
                }, (err) => {
                    if (err) {
                        console.error('Erro ao inicializar scanner:', err);
                        this.showStatusMessage('Erro ao acessar a câmera. Verifique as permissões.', 'error');
                        this.resetButtons();
                        return;
                    }
                    
                    try {
                        Quagga.start();
                        this.isScanning = true;
                        this.startBtn.disabled = true;
                        this.stopBtn.disabled = false;
                        this.scanLine.style.display = 'block';
                        this.showStatusMessage('Scanner ativo. Mantenha o código de barras centrado e estável.', 'success');
                        
                        // Reset detection count
                        this.detectionCount = {};
                    } catch (startError) {
                        console.error('Erro ao iniciar Quagga:', startError);
                        this.showStatusMessage('Erro ao iniciar o scanner.', 'error');
                        this.resetButtons();
                    }
                });

                // Remover listeners anteriores para evitar duplicação
                Quagga.offDetected();
                
                Quagga.onDetected((data) => {
                    const code = data.codeResult.code;
                    const confidence = data.codeResult.decodedCodes.reduce((sum, code) => sum + (code.error || 0), 0) / data.codeResult.decodedCodes.length;
                    
                    if (confidence < 0.1) {
                        this.handleBarcodeDetection(code, data);
                    }
                });
            }

            stopScanner() {
                if (!this.isScanning) return;

                try {
                    // Parar o Quagga completamente
                    Quagga.stop();
                    
                    // Limpar completamente
                    this.cleanupQuagga();
                    
                    // Reset estados
                    this.isScanning = false;
                    this.resetButtons();
                    this.barcodeResult.style.display = 'none';
                    this.scanLine.style.display = 'none';
                    this.showStatusMessage('Scanner parado', 'info');
                    
                    // Reset detection states
                    this.detectionCount = {};
                    this.lastScannedCode = null;
                    
                } catch (error) {
                    console.error('Erro ao parar scanner:', error);
                    // Forçar reset mesmo com erro
                    this.forceReset();
                }
            }

            cleanupQuagga() {
                try {
                    // Remover todos os event listeners
                    Quagga.offDetected();
                    Quagga.offProcessed();
                    
                    // Limpar o canvas/video do container
                    const container = document.querySelector('#interactive');
                    if (container) {
                        // Remover elementos de vídeo e canvas criados pelo Quagga
                        const videos = container.querySelectorAll('video');
                        const canvases = container.querySelectorAll('canvas');
                        
                        videos.forEach(video => {
                            if (video.srcObject) {
                                const tracks = video.srcObject.getTracks();
                                tracks.forEach(track => track.stop());
                            }
                            video.remove();
                        });
                        
                        canvases.forEach(canvas => canvas.remove());
                    }
                } catch (error) {
                    console.log('Erro na limpeza do Quagga (não crítico):', error);
                }
            }

            resetButtons() {
                this.startBtn.disabled = false;
                this.stopBtn.disabled = true;
            }

            forceReset() {
                // Reset forçado em caso de erro
                this.isScanning = false;
                this.resetButtons();
                this.barcodeResult.style.display = 'none';
                this.scanLine.style.display = 'none';
                this.detectionCount = {};
                this.lastScannedCode = null;
                
                // Limpar container completamente
                const container = document.querySelector('#interactive');
                if (container) {
                    container.innerHTML = '';
                }
                
                this.showStatusMessage('Scanner resetado', 'info');
            }

            handleBarcodeDetection(barcode, detectionData) {
                const now = Date.now();
                
                if (!this.isValidBarcode(barcode)) {
                    console.log(`Código inválido rejeitado: ${barcode}`);
                    return;
                }
                
                if (!this.detectionCount[barcode]) {
                    this.detectionCount[barcode] = {
                        count: 0,
                        firstSeen: now,
                        lastSeen: now,
                        detections: []
                    };
                }
                
                this.detectionCount[barcode].count++;
                this.detectionCount[barcode].lastSeen = now;
                this.detectionCount[barcode].detections.push({
                    time: now,
                    confidence: detectionData ? this.calculateConfidence(detectionData) : 1
                });
                
                this.showStatusMessage(`Detectando: ${barcode} (${this.detectionCount[barcode].count}/${this.minDetections})`, 'info');
                
                if (this.detectionCount[barcode].count >= this.minDetections) {
                    const timeSpan = this.detectionCount[barcode].lastSeen - this.detectionCount[barcode].firstSeen;
                    if (timeSpan < 5000) {
                        if (this.lastScannedCode === barcode && 
                            (now - this.lastScannedTime) < this.scanCooldown) {
                            return;
                        }
                        
                        this.lastScannedCode = barcode;
                        this.lastScannedTime = now;
                        this.detectionCount = {};
                        this.onBarcodeConfirmed(barcode);
                    } else {
                        this.detectionCount[barcode] = {
                            count: 1,
                            firstSeen: now,
                            lastSeen: now,
                            detections: [{ time: now, confidence: 1 }]
                        };
                    }
                }
            }

            calculateConfidence(detectionData) {
                if (!detectionData || !detectionData.codeResult) return 0.5;
                
                const decodedCodes = detectionData.codeResult.decodedCodes || [];
                if (decodedCodes.length === 0) return 0.5;
                
                const avgError = decodedCodes.reduce((sum, code) => sum + (code.error || 0), 0) / decodedCodes.length;
                return Math.max(0, 1 - avgError);
            }

            isValidBarcode(barcode) {
                if (barcode.length === 13) {
                    return this.validateEAN13(barcode);
                } else if (barcode.length === 8) {
                    return this.validateEAN8(barcode);
                }
                return barcode.length >= 6;
            }

            validateEAN13(barcode) {
                if (!/^\d{13}$/.test(barcode)) return false;
                
                let sum = 0;
                for (let i = 0; i < 12; i++) {
                    const digit = parseInt(barcode[i]);
                    sum += (i % 2 === 0) ? digit : digit * 3;
                }
                
                const checkDigit = (10 - (sum % 10)) % 10;
                return checkDigit === parseInt(barcode[12]);
            }

            validateEAN8(barcode) {
                if (!/^\d{8}$/.test(barcode)) return false;
                
                let sum = 0;
                for (let i = 0; i < 7; i++) {
                    const digit = parseInt(barcode[i]);
                    sum += (i % 2 === 0) ? digit * 3 : digit;
                }
                
                const checkDigit = (10 - (sum % 10)) % 10;
                return checkDigit === parseInt(barcode[7]);
            }

            async onBarcodeConfirmed(barcode) {
                this.barcodeResult.innerHTML = `✅ Código confirmado: <strong>${barcode}</strong>`;
                this.barcodeResult.style.display = 'block';
                
                if (this.products.some(p => p.barcode === barcode)) {
                    this.showStatusMessage('Produto já escaneado!', 'info');
                    return;
                }

                this.showLoading(true);
                this.showStatusMessage('A buscar informações do produto...', 'info');
                
                try {
                    const productInfo = await this.getProductInfo(barcode);
                    this.addProduct(productInfo);
                    
                    // Guardar automaticamente na base de dados
                    await this.saveProductToDatabase(productInfo);
                    
                    this.showStatusMessage('Produto adicionado e guardado na base de dados!', 'success');
                } catch (error) {
                    console.error('Erro ao buscar produto:', error);
                    this.showStatusMessage('Erro ao buscar informações. Produto adicionado com dados básicos.', 'error');
                    
                    const basicProduct = {
                        barcode: barcode,
                        name: 'Produto não identificado',
                        price: 'Preço não disponível',
                        brand: 'Marca não identificada',
                        category: 'Categoria não identificada',
                        source: 'Local'
                    };
                    
                    this.addProduct(basicProduct);
                    
                    // Tentar guardar mesmo com dados básicos
                    try {
                        await this.saveProductToDatabase(basicProduct);
                    } catch (saveError) {
                        console.error('Erro ao guardar produto básico:', saveError);
                    }
                } finally {
                    this.showLoading(false);
                }
            }

            async getProductInfo(barcode) {
                console.log(`Buscando informações para código: ${barcode}`);
                
                if (this.localProducts[barcode]) {
                    const product = this.localProducts[barcode];
                    console.log('Produto encontrado na base local:', product);
                    return {
                        barcode: barcode,
                        name: product.name,
                        brand: product.brand,
                        category: product.category,
                        price: product.price,
                        description: product.description,
                        scannedAt: new Date().toLocaleString('pt-PT'),
                        source: 'Base Local Portuguesa'
                    };
                }

                const apis = [
                    () => this.searchOpenFoodFacts(barcode),
                    () => this.searchUPCItemDB(barcode),
                    () => this.searchBarcodeSpider(barcode)
                ];

                for (const apiCall of apis) {
                    try {
                        const result = await apiCall();
                        if (result && result.found) {
                            console.log('Produto encontrado via API:', result);
                            return {
                                ...result,
                                barcode: barcode,
                                scannedAt: new Date().toLocaleString('pt-PT')
                            };
                        }
                    } catch (error) {
                        console.log('API falhou, tentando próxima...', error.message);
                        continue;
                    }
                }

                console.log('Produto não encontrado, usando dados básicos');
                return {
                    barcode: barcode,
                    name: this.generateProductName(barcode),
                    brand: 'Marca não identificada',
                    category: this.guessCategory(barcode),
                    price: this.estimatePrice(barcode),
                    description: 'Produto não encontrado nas bases de dados disponíveis',
                    scannedAt: new Date().toLocaleString('pt-PT'),
                    source: 'Dados estimados'
                };
            }

            async searchOpenFoodFacts(barcode) {
                const response = await fetch(`https://world.openfoodfacts.org/api/v0/product/${barcode}.json`);
                if (!response.ok) throw new Error('OpenFoodFacts API error');
                
                const data = await response.json();
                if (data.status !== 1) return { found: false };

                const product = data.product;
                return {
                    found: true,
                    name: product.product_name || product.product_name_pt || 'Nome não disponível',
                    brand: product.brands || 'Marca não disponível',
                    category: this.formatCategory(product.categories),
                    price: this.estimatePrice(barcode, product.categories),
                    description: product.ingredients_text || 'Ingredientes não disponíveis',
                    source: 'Open Food Facts'
                };
            }

            async searchUPCItemDB(barcode) {
                try {
                    const response = await fetch(`https://api.upcitemdb.com/prod/trial/lookup?upc=${barcode}`);
                    if (!response.ok) throw new Error('UPCItemDB API error');
                    
                    const data = await response.json();
                    if (!data.items || data.items.length === 0) return { found: false };

                    const item = data.items[0];
                    return {
                        found: true,
                        name: item.title || 'Nome não disponível',
                        brand: item.brand || 'Marca não disponível',
                        category: item.category || 'Categoria não disponível',
                        price: this.estimatePrice(barcode),
                        description: item.description || 'Descrição não disponível',
                        source: 'UPC Item DB'
                    };
                } catch (error) {
                    return { found: false };
                }
            }

            async searchBarcodeSpider(barcode) {
                try {
                    const response = await fetch(`https://api.barcodespider.com/v1/lookup?upc=${barcode}`);
                    if (!response.ok) throw new Error('BarcodeSpider API error');
                    
                    const data = await response.json();
                    if (!data.item_response || data.item_response.code !== 200) return { found: false };

                    const item = data.item_response.item_attributes;
                    return {
                        found: true,
                        name: item.title || 'Nome não disponível',
                        brand: item.brand || 'Marca não disponível',
                        category: item.category || 'Categoria não disponível',
                        price: this.estimatePrice(barcode),
                        description: item.description || 'Descrição não disponível',
                        source: 'Barcode Spider'
                    };
                } catch (error) {
                    return { found: false };
                }
            }

            generateProductName(barcode) {
                const prefix = barcode.substring(0, 3);
                const suffix = barcode.slice(-4);
                
                const countryPrefixes = {
                    '560': 'Produto Português',
                    '841': 'Produto Espanhol',
                    '380': 'Produto Búlgaro',
                    '400': 'Produto Alemão',
                    '690': 'Produto Chinês'
                };
                
                const countryName = countryPrefixes[prefix] || 'Produto';
                return `${countryName} #${suffix}`;
            }

            guessCategory(barcode) {
                const prefix = barcode.substring(0, 3);
                
                if (prefix === '560') {
                    const sequence = barcode.substring(3, 6);
                    if (sequence.startsWith('104') || sequence.startsWith('105')) {
                        return 'Bebidas';
                    } else if (sequence.startsWith('101') || sequence.startsWith('102')) {
                        return 'Laticínios';
                    }
                }
                
                return 'Categoria não identificada';
            }

            formatCategory(categories) {
                if (!categories) return 'Categoria não identificada';
                
                const categoryList = categories.split(',');
                return categoryList[0]?.trim() || 'Categoria não identificada';
            }

            estimatePrice(barcode, categories = '') {
                const category = (categories || '').toLowerCase();
                const code = barcode.toString();
                
                if (category.includes('water') || category.includes('água') || 
                    code.includes('105900')) {
                    return `€${(Math.random() * 1.5 + 0.5).toFixed(2)}`;
                } else if (category.includes('milk') || category.includes('leite')) {
                    return `€${(Math.random() * 2 + 1).toFixed(2)}`;
                } else if (category.includes('bread') || category.includes('pão')) {
                    return `€${(Math.random() * 3 + 1).toFixed(2)}`;
                } else if (category.includes('alcohol') || category.includes('gel') || 
                        category.includes('hygiene') || category.includes('higiene')) {
                    return `€${(Math.random() * 3 + 1.5).toFixed(2)}`;
                } else if (category.includes('oil') || category.includes('azeite')) {
                    return `€${(Math.random() * 8 + 3).toFixed(2)}`;
                } else {
                    const lastDigit = parseInt(code.slice(-1));
                    const basePrice = 2 + (lastDigit * 0.5);
                    return `€${(basePrice + Math.random() * 2).toFixed(2)}`;
                }
            }

            addProduct(product) {
                this.products.unshift(product);
                this.updateProductList();
            }

            updateProductList() {
                if (this.products.length === 0) {
                    this.productList.innerHTML = `
                        <div class="no-products">
                            Nenhum produto escaneado ainda.<br>
                            Use o scanner para adicionar produtos!
                        </div>
                    `;
                    return;
                }

                this.productList.innerHTML = this.products.map((product, index) => `
                    <div class="product-item" style="animation-delay: ${index * 0.1}s">
                        <div class="product-name">${product.name}</div>
                        <div class="product-details">
                            <div class="product-detail">
                                <strong>Código:</strong> ${product.barcode}
                            </div>
                            <div class="product-detail">
                                <strong>Preço:</strong> ${product.price}
                            </div>
                            <div class="product-detail">
                                <strong>Marca:</strong> ${product.brand}
                            </div>
                            <div class="product-detail">
                                <strong>Categoria:</strong> ${product.category}
                            </div>
                            <div class="product-detail">
                                <strong>Fonte:</strong> ${product.source}
                            </div>
                            <div class="product-detail">
                                <strong>Escaneado:</strong> ${product.scannedAt}
                            </div>
                            <div class="product-actions" style="margin-top: 15px; text-align: center;">
                                <button class="add-to-cart-btn" onclick="addToCart('${product.barcode}', '${product.name.replace(/'/g, "\\'")}', '${product.price}', '${product.brand.replace(/'/g, "\\'")}')">
                                    Adicionar +
                                </button>
                            </div>
                        </div>
                    </div>
                `).join('');
            }

            showLoading(show) {
                if (show) {
                    this.loading.classList.add('show');
                } else {
                    this.loading.classList.remove('show');
                }
            }

            async saveProductToDatabase(product) {
                try {
                    // Limpar preço para enviar apenas o valor numérico
                    let cleanPrice = '';
                    if (product.price && product.price !== 'Preço não disponível') {
                        cleanPrice = product.price.replace('€', '').trim();
                    }
                    
                    const response = await fetch('saveProduct.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            barcode: product.barcode,
                            name: product.name || 'Produto não identificado',
                            brand: product.brand || 'Marca não identificada',
                            category: product.category || 'Categoria não identificada',
                            price: cleanPrice,
                        })
                    });

                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }

                    const result = await response.json();
                    
                    if (result.success) {
                        console.log('✅ Produto guardado na base de dados:', result);
                        if (result.action === 'updated') {
                            this.showStatusMessage('📝 Produto atualizado na base de dados', 'success');
                        } else {
                            this.showStatusMessage('💾 Produto guardado na base de dados', 'success');
                        }
                    } else {
                        throw new Error(result.message || 'Erro desconhecido ao guardar');
                    }
                    
                } catch (error) {
                    console.error('❌ Erro ao guardar produto na BD:', error);
                    this.showStatusMessage('⚠️ Erro ao guardar na base de dados', 'error');
                    
                    // Log detalhado para debug
                    console.error('Detalhes do erro:', {
                        message: error.message,
                        product: product
                    });
                }
            }
        }

        // Função para adicionar produto ao carrinho
        function addToCart(barcode, name, price, brand) {
            // Obter carrinho existente do localStorage ou criar novo
            let cart = JSON.parse(localStorage.getItem('shopping_cart') || '[]');
            
            // Verificar se produto já existe no carrinho
            const existingItem = cart.find(item => item.barcode === barcode);
            
            if (existingItem) {
                existingItem.quantity += 1;
                showCartMessage(`Quantidade de "${name}" aumentada para ${existingItem.quantity}`, 'success');
            } else {
                // Adicionar novo produto
                cart.push({
                    barcode: barcode,
                    name: name,
                    price: price,
                    brand: brand,
                    quantity: 1,
                    addedAt: new Date().toLocaleString('pt-PT')
                });
                showCartMessage(`"${name}" adicionado ao carrinho!`, 'success');
            }
            
            // Guardar carrinho atualizado
            localStorage.setItem('shopping_cart', JSON.stringify(cart));
            
            // Atualizar contador do carrinho (se existir)
            updateCartCounter();
        }

        // Função para mostrar mensagens do carrinho
        function showCartMessage(message, type = 'success') {
            const statusMessage = document.getElementById('status-message');
            if (statusMessage) {
                statusMessage.textContent = message;
                statusMessage.className = `status-message ${type}`;
                statusMessage.style.display = 'block';
                
                setTimeout(() => {
                    statusMessage.style.display = 'none';
                }, 3000);
            }
        }

        // Função para atualizar contador do carrinho
        function updateCartCounter() {
            const cart = JSON.parse(localStorage.getItem('shopping_cart') || '[]');
            const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
            
            // Atualizar texto do botão "Ver carrinho" se existir
            const cartButton = document.querySelector('.btn');
            if (cartButton && cartButton.textContent.includes('carrinho')) {
                cartButton.textContent = `Ver carrinho (${totalItems})`;
            }
        }

        // Inicializar aplicação quando o DOM estiver pronto
        document.addEventListener('DOMContentLoaded', () => {
            new BarcodeReader();
            updateCartCounter(); // Atualizar contador ao carregar página
        });
    </script>
</body>
</html>