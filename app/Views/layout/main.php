<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title ?? APP_NAME); ?></title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/style.css">
</head>
<body>
    <!-- HEADER -->
    <header class="header">
        <div class="container">
            <span><?php echo htmlspecialchars(APP_NAME); ?></span>
            <a href="<?php echo BASE_URL; ?>logout" class="btn btn-secondary btn-sm">Sair</a>
        </div>
    </header>

    <!-- WRAPPER PRINCIPAL -->
    <div class="main-wrapper">
        <!-- SIDEBAR / NAVEGAÇÃO -->
        <nav class="sidebar">
            <ul>
                <li class="<?php echo (isset($current_page) && $current_page === 'dashboard') ? 'active' : ''; ?>">
                    <a href="<?php echo BASE_URL; ?>dashboard">📊 Dashboard</a>
                </li>
                <li class="<?php echo (isset($current_page) && $current_page === 'clientes') ? 'active' : ''; ?>">
                    <a href="<?php echo BASE_URL; ?>clientes">👥 Clientes</a>
                </li>
                <li class="<?php echo (isset($current_page) && $current_page === 'ordens') ? 'active' : ''; ?>">
                    <a href="<?php echo BASE_URL; ?>ordens">📋 Ordens de Serviço</a>
                </li>
                <li class="<?php echo (isset($current_page) && $current_page === 'usuarios') ? 'active' : ''; ?>">
                    <a href="<?php echo BASE_URL; ?>usuarios">👤 Usuários</a>
                </li>
                <li class="<?php echo (isset($current_page) && $current_page === 'despesas') ? 'active' : ''; ?>">
                    <a href="<?php echo BASE_URL; ?>despesas">💰 Despesas</a>
                </li>
                <li class="<?php echo (isset($current_page) && $current_page === 'configuracoes') ? 'active' : ''; ?>">
                    <a href="<?php echo BASE_URL; ?>configuracoes">⚙️ Configurações</a>
                </li>
            </ul>
        </nav>

        <!-- CONTEÚDO PRINCIPAL -->
        <main class="content">
            <?php 
            // O conteúdo específico da página será incluído aqui
            if (isset($content)) {
                require $content;
            }
            ?>
        </main>
    </div>

    <!-- FOOTER -->
    <?php require_once __DIR__ . '/footer.php'; ?>
</body>
</html>
