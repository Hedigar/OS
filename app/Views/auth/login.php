<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?php echo htmlspecialchars(APP_NAME); ?></title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/style.css">
    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background: linear-gradient(135deg, var(--dark-bg) 0%, var(--dark-secondary) 100%);
        }

        .login-container {
            width: 100%;
            max-width: 420px;
            padding: 2rem;
        }

        .login-card {
            background-color: var(--dark-secondary);
            border: 1px solid var(--dark-tertiary);
            border-radius: 12px;
            padding: 2.5rem;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.6);
            animation: slideUp 0.5s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .login-header h1 {
            font-size: 2rem;
            background: linear-gradient(135deg, var(--primary-red) 0%, var(--primary-red-light) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 0.5rem;
        }

        .login-header p {
            color: var(--text-secondary);
            font-size: 0.95rem;
            margin: 0;
        }

        .login-form {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .form-group label {
            color: var(--text-primary);
            font-weight: 600;
            font-size: 0.9rem;
            letter-spacing: 0.3px;
        }

        .form-group input {
            padding: 0.9rem 1.2rem;
            border: 1px solid var(--dark-tertiary);
            border-radius: 8px;
            background-color: var(--dark-tertiary);
            color: var(--text-primary);
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }

        .form-group input::placeholder {
            color: var(--text-muted);
        }

        .form-group input:focus {
            outline: none;
            border-color: var(--primary-red);
            box-shadow: 0 0 0 3px rgba(231, 76, 60, 0.1);
            background-color: var(--dark-secondary);
        }

        .login-button {
            padding: 1rem;
            border: none;
            border-radius: 8px;
            background: linear-gradient(135deg, var(--primary-red) 0%, var(--primary-red-light) 100%);
            color: var(--text-primary);
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            letter-spacing: 0.5px;
            box-shadow: 0 4px 12px rgba(231, 76, 60, 0.3);
        }

        .login-button:hover {
            background: linear-gradient(135deg, var(--primary-red-dark) 0%, var(--primary-red) 100%);
            box-shadow: 0 8px 20px rgba(231, 76, 60, 0.4);
            transform: translateY(-2px);
        }

        .login-button:active {
            transform: translateY(0);
        }

        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            border-left: 4px solid;
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            animation: shake 0.5s ease-in-out;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }

        .alert-danger {
            background-color: rgba(231, 76, 60, 0.1);
            border-left-color: var(--danger);
            color: var(--danger);
        }

        .alert-icon {
            font-size: 1.2rem;
            flex-shrink: 0;
        }

        .alert-message {
            flex: 1;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <!-- CABEÇALHO -->
            <div class="login-header">
                <h1><?php echo htmlspecialchars(APP_NAME); ?></h1>
                <p>Sistema de Gerenciamento de Ordens de Serviço</p>
            </div>

            <!-- ALERTA DE ERRO -->
            <?php if (isset($error)): ?>
                <div class="alert alert-danger">
                    <span class="alert-icon">⚠️</span>
                    <span class="alert-message"><?php echo htmlspecialchars($error); ?></span>
                </div>
            <?php endif; ?>

            <!-- FORMULÁRIO DE LOGIN -->
            <form action="<?php echo BASE_URL; ?>login" method="POST" class="login-form">
                <div class="form-group">
                    <label for="email">📧 E-mail</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
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
                        placeholder="Digite sua senha"
                        required
                    >
                </div>

                <button type="submit" class="login-button">
                    ✓ Entrar no Sistema
                </button>
            </form>

            <!-- RODAPÉ -->
            <div style="text-align: center; margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid var(--dark-tertiary);">
                <p style="color: var(--text-muted); font-size: 0.85rem; margin: 0;">
                    © 2025 <?php echo htmlspecialchars(APP_NAME); ?>. Todos os direitos reservados.
                </p>
            </div>
        </div>
    </div>
</body>
</html>
