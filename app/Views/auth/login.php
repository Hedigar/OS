<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?php echo htmlspecialchars(APP_NAME); ?></title>
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>css/style.css">
    <script>
        (function() {
            const savedTheme = localStorage.getItem('theme') || 'dark';
            document.documentElement.setAttribute('data-theme', savedTheme);
        })();
    </script>
</head>
<body class="login-body">
    <div class="login-container">
        <div class="login-card">
            <!-- CABEÇALHO -->
            <div class="login-header">
                <h1><span>OS</span> Manager</h1>
                <p class="text-muted">Sistema de Gerenciamento de Ordens de Serviço</p>
            </div>

            <!-- ALERTA DE ERRO -->
            <?php if (isset($error)): ?>
                <div class="alert alert-danger">
                    <span>⚠️</span>
                    <span><?php echo htmlspecialchars($error); ?></span>
                </div>
            <?php endif; ?>

            <!-- FORMULÁRIO DE LOGIN -->
            <form action="<?php echo BASE_URL; ?>login" method="POST">
                <div class="form-group">
                    <label for="email">📧 E-mail</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        class="form-control"
                        placeholder="seu@email.com"
                        required
                        autofocus
                    >
                </div>

                <div class="form-group">
                    <label for="senha">🔐 Senha</label>
                    <input
                        type="password"
                        id="senha"
                        name="senha"
                        class="form-control"
                        placeholder="Digite sua senha"
                        required
                    >
                </div>

                <button type="submit" class="btn btn-primary w-100 py-3">
                    ✓ Entrar no Sistema
                </button>
            </form>

            <!-- RODAPÉ -->
            <div class="login-footer">
                <p class="text-muted fs-sm m-0">
                    © <?php echo date('Y'); ?> <?php echo htmlspecialchars(APP_NAME); ?>.
                </p>
            </div>
        </div>
    </div>
</body>
</html>
