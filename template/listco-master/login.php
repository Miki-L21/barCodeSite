<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login</title>
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

     <!-- Hero Area com fundo igual ao site -->
    <section class="login-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
               <div class="card shadow login-card">
                    <h2 class="text-center mb-4">Login</h2>
                    
                    <div id="message" class="alert d-none"></div>
                    
                    <form id="loginForm">
                        <div class="form-group mb-3">
                            <label for="email">Email:</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="password">Password:</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <button type="submit" id="loginBtn" class="btn btn-primary w-100">Entrar</button>
                    </form>
                    
                    <!-- Botão "Criar" FORA do formulário -->
                    <div class="text-center mt-3">
                        <p>Não tens conta? Queres criar?</p>
                        <a href="registar.php" class="btn btn-secondary w-100">Criar</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
    
    <!-- Inclui rodapé -->
    <?php include 'rodape.php'; ?>

<script>
    document.getElementById('loginForm').addEventListener('submit', async function(e) {
        e.preventDefault();

        const email = document.getElementById('email').value.trim();
        const password = document.getElementById('password').value.trim();
        const loginBtn = document.getElementById('loginBtn');
        const messageDiv = document.getElementById('message');

        messageDiv.classList.add('d-none');
        loginBtn.disabled = true;
        loginBtn.textContent = 'Aguarde...';

        try {
            const formData = new FormData();
            formData.append('action', 'login');
            formData.append('email', email);
            formData.append('password', password);

            const response = await fetch('/site/controller/controllerUser.php', {
                method: 'POST',
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                // Guarda o email como identificador da sessão (ou token, se tiveres)
                localStorage.setItem('utilizador', email); // ou sessionStorage.setItem()

                messageDiv.textContent = data.message;
                messageDiv.className = 'alert alert-success';
                messageDiv.classList.remove('d-none');

                 setTimeout(() => {
                    window.location.href = 'index.php'; // Redireciona para index.php
                }, 1000);

            } else {
                messageDiv.textContent = data.message;
                messageDiv.className = 'alert alert-danger';
                messageDiv.classList.remove('d-none');
            }

        } catch (error) {
            messageDiv.textContent = 'Erro de conexão. Tente novamente.';
            messageDiv.className = 'alert alert-danger';
            messageDiv.classList.remove('d-none');
        } finally {
            loginBtn.disabled = false;
            loginBtn.textContent = 'Entrar';
        }
    });
</script>
</body>
</html>