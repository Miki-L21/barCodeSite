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
    <title>Scaner</title>
    <meta name="description" content="Leitor de código de barras integrado">
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
        // Verificação de sessão de utilizador
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
                    console.log('Utilizador logado:', data.user.email);
                    showUserInfo(data.user);
                } else {
                    console.log('Utilizador não está logado');
                }
            } catch (error) {
                console.error('Erro ao verificar sessão:', error);
            }
        }

        function showUserInfo() {
            const userDiv = document.getElementById("user-info");
            if (userDiv) userDiv.innerText = "Sessão iniciada.";
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

        // Classe principal do leitor de códigos de barras
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

                console.log('🎥 Iniciando scanner...');
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
                        console.error('❌ Erro ao inicializar scanner:', err);
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
                        
                        console.log('✅ Scanner iniciado com sucesso');
                        this.detectionCount = {};
                    } catch (startError) {
                        console.error('❌ Erro ao iniciar Quagga:', startError);
                        this.showStatusMessage('Erro ao iniciar o scanner.', 'error');
                        this.resetButtons();
                    }
                });

                // Remover listeners anteriores
                Quagga.offDetected();
                
                // Adicionar listener para detecção
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

                console.log('🛑 Parando scanner...');
                
                try {
                    Quagga.stop();
                    this.cleanupQuagga();
                    
                    this.isScanning = false;
                    this.resetButtons();
                    this.barcodeResult.style.display = 'none';
                    this.scanLine.style.display = 'none';
                    this.showStatusMessage('Scanner parado', 'info');
                    
                    this.detectionCount = {};
                    this.lastScannedCode = null;
                    
                    console.log('✅ Scanner parado com sucesso');
                    
                } catch (error) {
                    console.error('❌ Erro ao parar scanner:', error);
                    this.forceReset();
                }
            }

            cleanupQuagga() {
                try {
                    Quagga.offDetected();
                    Quagga.offProcessed();
                    
                    const container = document.querySelector('#interactive');
                    if (container) {
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
                    console.log('⚠️ Erro na limpeza do Quagga (não crítico):', error);
                }
            }

            resetButtons() {
                this.startBtn.disabled = false;
                this.stopBtn.disabled = true;
            }

            forceReset() {
                this.isScanning = false;
                this.resetButtons();
                this.barcodeResult.style.display = 'none';
                this.scanLine.style.display = 'none';
                this.detectionCount = {};
                this.lastScannedCode = null;
                
                const container = document.querySelector('#interactive');
                if (container) {
                    container.innerHTML = '';
                }
                
                this.showStatusMessage('Scanner resetado', 'info');
            }

            handleBarcodeDetection(barcode, detectionData) {
                const now = Date.now();
                
                if (!this.isValidBarcode(barcode)) {
                    console.log(`❌ Código inválido rejeitado: ${barcode}`);
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
                
                // Verificar se já foi escaneado
                if (this.products.some(p => p.barcode === barcode)) {
                    this.showStatusMessage('ℹ️ Produto já foi escaneado anteriormente!', 'info');
                    return;
                }

                this.showLoading(true);
                this.showStatusMessage('🔍 Buscando informações do produto...', 'info');
                
                let productInfo = null;
                
                try {
                    // Buscar informações do produto
                    productInfo = await this.getProductInfo(barcode);
                    console.log('📦 Informações do produto:', productInfo);
                    
                    // Adicionar à lista local primeiro
                    this.addProduct(productInfo);
                    
                    // Mostrar que foi adicionado localmente
                    this.showStatusMessage('📱 Produto adicionado à lista. Guardando na base de dados...', 'info');
                    
                } catch (error) {
                    console.error('❌ Erro ao buscar informações:', error);
                    
                    // Criar produto básico mesmo se não encontrar informações
                    productInfo = {
                        barcode: barcode,
                        name: `Produto ${barcode.slice(-4)}`,
                        price: 'Preço não disponível',
                        brand: 'Marca não identificada',
                        category: 'Categoria não identificada',
                        description: 'Produto não identificado pelas APIs',
                        source: 'Scanner Local',
                        scannedAt: new Date().toLocaleString('pt-PT')
                    };
                    
                    this.addProduct(productInfo);
                    this.showStatusMessage('⚠️ Produto adicionado com dados básicos. Tentando guardar...', 'warning');
                }
                
                // Tentar guardar na base de dados
                try {
                productInfo.action = "saveUserProduct";  // 🔧 <== Linha nova que resolve o erro

                const saveResult = await this.saveProductToDatabase(productInfo);
                
                if (saveResult && saveResult.success === false) {
                    console.log('⚠️ Produto mantido na lista local apesar do erro na base de dados');
                }
                    
                } catch (saveError) {
                    console.error('❌ Erro crítico ao guardar:', saveError);
                    this.showStatusMessage('💾 Produto mantido na lista local. Erro ao sincronizar com base de dados.', 'warning');
                } finally {
                    this.showLoading(false);
                }
            }

            async getProductInfo(barcode) {
                console.log(`🔍 Buscando informações para código: ${barcode}`);
                
                if (this.localProducts[barcode]) {
                    const product = this.localProducts[barcode];
                    console.log('✅ Produto encontrado na base local:', product);
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
                            console.log('✅ Produto encontrado via API:', result);
                            return {
                                ...result,
                                barcode: barcode,
                                scannedAt: new Date().toLocaleString('pt-PT')
                            };
                        }
                    } catch (error) {
                        console.log('⚠️ API falhou, tentando próxima...', error.message);
                        continue;
                    }
                }

                console.log('⚠️ Produto não encontrado, usando dados básicos');
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

            cleanPriceForDatabase(price) {
                if (!price || typeof price !== 'string') {
                    return '0.00';
                }
                
                // Remover símbolos de moeda e espaços
                let cleanPrice = price.replace(/[€$£¥₹]/g, '').trim();
                
                // Substituir vírgula por ponto para formato decimal
                cleanPrice = cleanPrice.replace(',', '.');
                
                // Extrair apenas números e ponto decimal
                cleanPrice = cleanPrice.match(/[\d.]+/);
                
                if (!cleanPrice) {
                    return '0.00';
                }
                
                // Converter para float e formatar com 2 casas decimais
                const numericPrice = parseFloat(cleanPrice[0]);
                
                if (isNaN(numericPrice)) {
                    return '0.00';
                }
                
                return numericPrice.toFixed(2);
            }

            async saveProductToDatabase(productData) {
                try {
                    if (!productData || !productData.name || !productData.price) {
                        throw new Error("Dados do produto incompletos");
                    }

                    console.log('📤 Enviando dados para a base de dados:', productData);

                    // Criar FormData em vez de JSON para compatibilidade com PHP $_POST
                    const formData = new FormData();
                    formData.append('action', 'add_to_user');
                    formData.append('barcode', productData.barcode);
                    formData.append('name', productData.name);
                    formData.append('brand', productData.brand || 'Marca não identificada');
                    formData.append('category', productData.category || 'Categoria não identificada');
                    formData.append('price', this.cleanPriceForDatabase(productData.price));
                    formData.append('description', productData.description || '');
                    formData.append('source', productData.source || 'Scanner');

                    // Primeira tentativa: usar barcodeApi.php
                    try {
                        const response = await fetch('../../controller/barcodeApi.php', {
                            method: 'POST',
                            body: formData
                        });

                        if (!response.ok) {
                            throw new Error(`HTTP Error: ${response.status}`);
                        }

                        const result = await response.json();
                        console.log('📥 Resposta da API:', result);

                        if (result.success) {
                            this.showStatusMessage('💾 Produto salvo com sucesso na base de dados!', 'success');
                            return result;
                        } else {
                            throw new Error(result.message || 'Erro na API');
                        }

                    } catch (apiError) {
                        console.warn('⚠️ Erro na barcodeApi.php, tentando controlador direto:', apiError);
                        
                        // Segunda tentativa: usar controlador direto
                        try {
                            // Primeiro criar/atualizar o produto
                            const productFormData = new FormData();
                            productFormData.append('action', 'create_or_update');
                            productFormData.append('barcode', productData.barcode);
                            productFormData.append('name', productData.name);
                            productFormData.append('brand', productData.brand || 'Marca não identificada');
                            productFormData.append('category', productData.category || 'Categoria não identificada');
                            productFormData.append('price', this.cleanPriceForDatabase(productData.price));

                            const productResponse = await fetch('../../controller/productController.php', {
                                method: 'POST',
                                body: productFormData
                            });

                            if (!productResponse.ok) {
                                throw new Error(`Erro HTTP no productController: ${productResponse.status}`);
                            }

                            const productResult = await productResponse.json();
                            console.log('📦 Resposta do productController:', productResult);

                            if (!productResult.success) {
                                throw new Error(productResult.message || 'Erro ao criar produto');
                            }

                            // Depois associar ao utilizador
                            const userFormData = new FormData();
                            userFormData.append('action', 'add_to_user');
                            userFormData.append('product_id', productResult.product_id);

                            const userResponse = await fetch('../../controller/userProductController.php', {
                                method: 'POST',
                                body: userFormData
                            });

                            if (!userResponse.ok) {
                                throw new Error(`Erro HTTP no userProductController: ${userResponse.status}`);
                            }

                            const userResult = await userResponse.json();
                            console.log('👤 Resposta do userProductController:', userResult);

                            if (userResult.success) {
                                this.showStatusMessage('💾 Produto salvo e associado com sucesso!', 'success');
                                return {
                                    success: true,
                                    message: 'Produto salvo via controlador direto',
                                    product: productResult,
                                    userRelation: userResult
                                };
                            } else {
                                throw new Error(userResult.message || 'Erro ao associar produto ao utilizador');
                            }

                        } catch (directError) {
                            console.error('❌ Erro também no controlador direto:', directError);
                            
                            // Terceira tentativa: modo de compatibilidade com JSON
                            try {
                                const jsonPayload = {
                                    action: 'add_to_user',
                                    barcode: productData.barcode,
                                    name: productData.name,
                                    brand: productData.brand || 'Marca não identificada',
                                    category: productData.category || 'Categoria não identificada',
                                    price: this.cleanPriceForDatabase(productData.price),
                                    description: productData.description || '',
                                    source: productData.source || 'Scanner'
                                };

                                const jsonResponse = await fetch('../../controller/userProductController.php', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                    },
                                    body: JSON.stringify(jsonPayload)
                                });

                                if (!jsonResponse.ok) {
                                    throw new Error(`Erro HTTP JSON: ${jsonResponse.status}`);
                                }

                                const jsonResult = await jsonResponse.json();
                                console.log('🔄 Resposta JSON:', jsonResult);

                                if (jsonResult.success) {
                                    this.showStatusMessage('💾 Produto salvo via JSON!', 'success');
                                    return jsonResult;
                                } else {
                                    throw new Error(jsonResult.message || 'Erro no modo JSON');
                                }

                            } catch (jsonError) {
                                console.error('❌ Todas as tentativas falharam:', jsonError);
                                this.showStatusMessage('⚠️ Produto mantido localmente. Erro ao sincronizar com base de dados.', 'warning');
                                
                                return {
                                    success: false,
                                    message: `Erro ao salvar: ${jsonError.message}`,
                                    keepLocal: true
                                };
                            }
                        }
                    }

                } catch (error) {
                    console.error('❌ Erro crítico no saveProductToDatabase:', error);
                    this.showStatusMessage('💾 Produto mantido na lista local.', 'warning');
                    
                    return {
                        success: false,
                        message: error.message,
                        keepLocal: true
                    };
                }
            }
        }

        // Funções globais para o carrinho (sem usar localStorage)
        let shoppingCart = [];

        function addToCart(barcode, name, price, brand) {
            // Verificar se produto já existe no carrinho
            const existingItem = shoppingCart.find(item => item.barcode === barcode);
            
            if (existingItem) {
                existingItem.quantity += 1;
                showCartMessage(`Quantidade de "${name}" aumentada para ${existingItem.quantity}`, 'success');
            } else {
                // Adicionar novo produto
                shoppingCart.push({
                    barcode: barcode,
                    name: name,
                    price: price,
                    brand: brand,
                    quantity: 1,
                    addedAt: new Date().toLocaleString('pt-PT')
                });
                showCartMessage(`"${name}" adicionado ao carrinho!`, 'success');
            }
            
            // Atualizar contador do carrinho
            updateCartCounter();
        }

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

        function updateCartCounter() {
            const totalItems = shoppingCart.reduce((sum, item) => sum + item.quantity, 0);
            
            // Atualizar texto do botão "Ver carrinho" se existir
            const cartButton = document.querySelector('.btn');
            if (cartButton && cartButton.textContent.includes('carrinho')) {
                cartButton.textContent = `Ver carrinho (${totalItems})`;
            }
        }

        // Inicializar aplicação quando o DOM estiver pronto
        document.addEventListener('DOMContentLoaded', () => {
            new BarcodeReader();
            updateCartCounter();
        });
    </script>
</body>
</html>