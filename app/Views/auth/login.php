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
                    <div style="position: relative;">
                        <input
                            type="password"
                            id="senha"
                            name="senha"
                            class="form-control"
                            placeholder="Digite sua senha"
                            required
                            style="padding-right: 40px;"
                        >
                        <span class="password-toggle" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer; opacity: 0.6;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                        </span>
                    </div>
                </div>

                <div class="form-group d-flex align-items-center" style="margin-bottom: 1rem; gap: 0.5rem;">
                    <input type="checkbox" id="lembrar_email" style="cursor: pointer;">
                    <label for="lembrar_email" style="cursor: pointer; margin: 0; user-select: none;">Lembrar e-mail</label>
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const emailInput = document.getElementById('email');
            const rememberCheckbox = document.getElementById('lembrar_email');
            const form = document.querySelector('form');
            const passwordInput = document.getElementById('senha');
            const passwordToggle = document.querySelector('.password-toggle');
            
            // Lógica para Lembrar E-mail
            const savedEmail = localStorage.getItem('os_manager_saved_email');
            if (savedEmail) {
                emailInput.value = savedEmail;
                rememberCheckbox.checked = true;
            }

            form.addEventListener('submit', function() {
                if (rememberCheckbox.checked) {
                    localStorage.setItem('os_manager_saved_email', emailInput.value);
                } else {
                    localStorage.removeItem('os_manager_saved_email');
                }
            });

            // Lógica para Toggle Senha
            passwordToggle.addEventListener('click', function() {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                
                // Alternar ícone (Olho Aberto / Fechado)
                if (type === 'text') {
                    // Ícone de olho riscado (senha visível -> ocultar)
                    this.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M1 1l22 22"/><path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/><circle cx="12" cy="12" r="3"/></svg>';
                } else {
                    // Ícone de olho normal (senha oculta -> mostrar)
                    this.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>';
                }
            });
        });
    </script>
</body>
</html>
