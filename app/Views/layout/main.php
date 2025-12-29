<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title ?? APP_NAME); ?></title>
    <!-- Bootstrap CSS (carregado primeiro para permitir sobrescrever com style.css) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/style.css">
</head>
<body>
    <!-- HEADER -->
    <header class="header">
        <div class="container">
            <span><?php echo htmlspecialchars(APP_NAME); ?></span>
            <a href="<?php echo BASE_URL; ?>logout" class="btn btn-primary">Sair</a>
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


                <?php if (\App\Core\Auth::isTecnico()): ?>
                <li class="<?php echo (isset($current_page) && $current_page === 'despesas') ? 'active' : ''; ?>">
                    <a href="<?php echo BASE_URL; ?>despesas">💰 Despesas</a>
                </li>
                <?php endif; ?>

                <?php if (\App\Core\Auth::isAdmin()): ?>
                <li class="nav-item-dropdown <?php echo (isset($current_page) && strpos($current_page, 'configuracoes') !== false) ? 'active' : ''; ?>">
                    <a href="javascript:void(0);" class="dropdown-toggle" onclick="toggleSubmenu('config-submenu')">⚙️ Configurações</a>
                    <ul id="config-submenu" class="submenu" style="<?php echo (isset($current_page) && strpos($current_page, 'configuracoes') !== false) ? 'display: block;' : 'display: none;'; ?>">
                        <li class="<?php echo (isset($current_page) && $current_page === 'configuracoes_produtos') ? 'active' : ''; ?>">
                            <a href="<?php echo BASE_URL; ?>configuracoes/produtos-servicos">📦 Produtos e Serviços</a>
                        </li>
                        <li class="<?php echo (isset($current_page) && $current_page === 'usuarios') ? 'active' : ''; ?>">
                            <a href="<?php echo BASE_URL; ?>usuarios">👤 Usuários</a>
                        </li>
                    </ul>
                </li>
                <?php endif; ?>
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

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
    function toggleSubmenu(id) {
        const submenu = document.getElementById(id);
        if (submenu.style.display === 'none' || submenu.style.display === '') {
            submenu.style.display = 'block';
        } else {
            submenu.style.display = 'none';
        }
    }
    </script>
</body>
</html>
