<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title ?? APP_NAME); ?></title>
    <!-- Bootstrap CSS (carregado primeiro para permitir sobrescrever com style.css) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>css/style.css">
    <script>
        // Aplicar tema salvo antes de renderizar
        (function() {
            const savedTheme = localStorage.getItem('theme') || 'dark';
            document.documentElement.setAttribute('data-theme', savedTheme);
        })();
    </script>
</head>
<body>
    <!-- HEADER -->
    <header class="header">
        <div class="container">
            <div class="d-flex align-items-center gap-2">
                <i class="fas fa-tools text-white fs-4"></i>
                <span class="fw-bold"><?php echo htmlspecialchars(APP_NAME); ?></span>
            </div>
            <div class="d-flex align-items-center gap-3">
                <span class="text-white d-none d-md-inline opacity-75" style="font-size: 0.8rem; font-weight: 300; letter-spacing: 0.3px;">olá, <?php echo isset($user['nome']) ? strtolower(explode(' ', $user['nome'])[0]) : 'usuário'; ?></span>
                <a href="<?php echo BASE_URL; ?>logout" class="btn btn-light btn-sm fw-bold px-3" style="border-radius: 8px; color: var(--primary-red);">
                    <i class="fas fa-sign-out-alt"></i> Sair
                </a>
            </div>
        </div>
    </header>

    <!-- WRAPPER PRINCIPAL -->
    <div class="main-wrapper">
        <!-- SIDEBAR / NAVEGAÇÃO -->
        <nav class="sidebar">
            <ul>
                <li class="<?php echo (isset($current_page) && $current_page === 'dashboard') ? 'active' : ''; ?>">
                    <a href="<?php echo BASE_URL; ?>dashboard"><i class="fas fa-chart-pie me-2"></i> Dashboard</a>
                </li>
                <li class="<?php echo (isset($current_page) && $current_page === 'clientes') ? 'active' : ''; ?>">
                    <a href="<?php echo BASE_URL; ?>clientes"><i class="fas fa-users me-2"></i> Clientes</a>
                </li>
                <li class="<?php echo (isset($current_page) && $current_page === 'ordens') ? 'active' : ''; ?>">
                    <a href="<?php echo BASE_URL; ?>ordens"><i class="fas fa-file-invoice me-2"></i> Ordens de Serviço</a>
                </li>
                <li class="<?php echo (isset($current_page) && $current_page === 'atendimentos_externos') ? 'active' : ''; ?>">
                    <a href="<?php echo BASE_URL; ?>atendimentos-externos"><i class="fas fa-truck me-2"></i> Atendimento Externo</a>
                </li>
                


                <?php if (\App\Core\Auth::isTecnico()): ?>
                
                <?php endif; ?>

                <?php if (\App\Core\Auth::isAdmin()): ?>
                <li class="<?php echo (isset($current_page) && $current_page === 'relatorios') ? 'active' : ''; ?>">
                    <a href="<?php echo BASE_URL; ?>relatorios"><i class="fas fa-chart-bar me-2"></i> Relatórios</a>
                </li>
                <li class="<?php echo (isset($current_page) && $current_page === 'despesas') ? 'active' : ''; ?>">
                    <a href="<?php echo BASE_URL; ?>despesas"><i class="fas fa-wallet me-2"></i> Despesas</a>
                </li>
                <li class="nav-item-dropdown <?php echo (isset($current_page) && strpos($current_page, 'configuracoes') !== false) ? 'active' : ''; ?>">
                    <a href="javascript:void(0);" class="dropdown-toggle" onclick="toggleSubmenu('config-submenu')"><i class="fas fa-cog me-2"></i> Configurações</a>
                    <ul id="config-submenu" class="submenu" style="<?php echo (isset($current_page) && strpos($current_page, 'configuracoes') !== false) ? 'display: block;' : 'display: none;'; ?>">
                        <li class="<?php echo (isset($current_page) && $current_page === 'configuracoes_produtos') ? 'active' : ''; ?>">
                            <a href="<?php echo BASE_URL; ?>configuracoes/produtos-servicos"><i class="fas fa-box me-2"></i> Produtos e Serviços</a>
                        </li>
                        <li class="<?php echo (isset($current_page) && $current_page === 'configuracoes_os') ? 'active' : ''; ?>">
                            <a href="<?php echo BASE_URL; ?>configuracoes/os"><i class="fas fa-file-invoice me-2"></i> Configurações de OS</a>
                        </li>
                        <li class="<?php echo (isset($current_page) && $current_page === 'usuarios') ? 'active' : ''; ?>">
                            <a href="<?php echo BASE_URL; ?>usuarios"><i class="fas fa-user-shield me-2"></i> Usuários</a>
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

    <!-- Botão de Troca de Tema -->
    <button class="theme-switch" onclick="toggleTheme()" title="Trocar Tema">
        <i class="fas fa-moon" id="theme-icon"></i>
    </button>

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

    function toggleTheme() {
        const html = document.documentElement;
        const currentTheme = html.getAttribute('data-theme');
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
        
        html.setAttribute('data-theme', newTheme);
        localStorage.setItem('theme', newTheme);
        updateThemeIcon(newTheme);
    }

    function updateThemeIcon(theme) {
        const icon = document.getElementById('theme-icon');
        if (icon) {
            icon.className = theme === 'dark' ? 'fas fa-moon' : 'fas fa-sun';
        }
    }

    // Inicializar ícone no carregamento
    document.addEventListener('DOMContentLoaded', () => {
        updateThemeIcon(document.documentElement.getAttribute('data-theme'));
    });
    </script>
</body>
</html>
