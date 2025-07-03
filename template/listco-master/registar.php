<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cadastrar</title>
    <link rel="manifest" href="site.webmanifest">
    <link rel="shortcut icon" type="image/x-icon" href="assets/img/icon.ico">

    <!-- CSS principal do template -->
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
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/loginEregistrar.css">
</head>
<body>

    <!-- Inclui cabeçalho -->
    <?php include 'cabecalho.php'; ?>

   <section class="login-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card shadow login-card">
                    <h2 class="text-center mb-4">Criar Conta</h2>
                    
                    <div id="message" class="alert d-none"></div>
                    
                    <form id="registerForm">
                        <div class="form-group mb-3">
                            <label for="username">Username:</label>
                            <input type="text" class="form-control" id="username" name="username" required minlength="3">
                        </div>
                        <div class="form-group mb-3">
                            <label for="email">Email:</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="password">Password:</label>
                            <input type="password" class="form-control" id="password" name="password" required minlength="6">
                        </div>
                        <div class="form-group mb-3">
                            <label for="confirm_password">Confirmar Password:</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                        </div>
                        <button type="submit" id="registerBtn" class="btn btn-primary w-100">Registar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

    <!-- Inclui rodapé -->
    <?php include 'rodape.php'; ?>

    <script>
     document.getElementById('registerForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    const email = document.getElementById('email').value.trim();
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirm_password').value;
    const username = document.getElementById('username').value.trim();
    const registerBtn = document.getElementById('registerBtn');
    const messageDiv = document.getElementById('message');

    // Resetar mensagens
    messageDiv.classList.add('d-none');
    messageDiv.textContent = '';

    // Validações
    if (!username || username.length < 3) {
        showError('Username deve ter pelo menos 3 caracteres');
        return;
    }

    if (!email || !validateEmail(email)) {
        showError('Por favor insira um email válido');
        return;
    }

    if (password.length < 6) {
        showError('A password deve ter pelo menos 6 caracteres');
        return;
    }

    if (password !== confirmPassword) {
        showError('As passwords não coincidem');
        return;
    }

    registerBtn.disabled = true;
    registerBtn.textContent = 'A registar...';

    try {
        const formData = new FormData();
        formData.append('action', 'register');
        formData.append('email', email);
        formData.append('password', password);
        formData.append('username', username);

        const response = await fetch('/site/controller/controllerUser.php', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();

        if (data.success) {
            showSuccess(data.message);
            document.getElementById('registerForm').reset();
            
            setTimeout(() => {
                window.location.href = 'login.php';
            }, 2000);
        } else {
            showError(data.message);
        }
    } catch (error) {
        console.error('Erro:', error);
        showError('Erro de conexão. Tente novamente.');
    } finally {
        registerBtn.disabled = false;
        registerBtn.textContent = 'Registar';
    }

    function showError(message) {
        messageDiv.textContent = message;
        messageDiv.className = 'alert alert-danger';
        messageDiv.classList.remove('d-none');
    }

    function showSuccess(message) {
        messageDiv.textContent = message;
        messageDiv.className = 'alert alert-success';
        messageDiv.classList.remove('d-none');
    }

    function validateEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }
});
</script>
</body>
</html>

