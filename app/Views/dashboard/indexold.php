<?php
$current_page = 'dashboard';
require_once __DIR__ . '/../layout/main.php';
?>

<div class="container">
    <!-- CABEÇALHO -->
    <div style="margin-bottom: 2rem;">
        <h1>👋 Bem-vindo, <?php echo htmlspecialchars($user['nome'] ?? 'Usuário'); ?>!</h1>
        <p style="color: var(--text-secondary); font-size: 1rem;">
            Este é o painel de controle do sistema de gerenciamento de Ordens de Serviço.
        </p>
    </div>

    <!-- CARDS DE ESTATÍSTICAS -->
    <div class="dashboard-cards">
        <!-- Card: Ordens Abertas -->
        <div class="card">
            <h2>📋 Ordens Abertas</h2>
            <p class="card-value primary-red-text">42</p>
            <p style="font-size: 0.85rem; color: var(--text-muted); margin-top: 0.5rem;">
                Aguardando conclusão
            </p>
            <a href="<?php echo BASE_URL; ?>ordens" style="display: inline-block; margin-top: 1rem; color: var(--primary-red); text-decoration: none; font-weight: 500; font-size: 0.9rem;">
                Ver todas →
            </a>
        </div>

        <!-- Card: Clientes Ativos -->
        <div class="card">
            <h2>👥 Clientes Ativos</h2>
            <p class="card-value success-text">150</p>
            <p style="font-size: 0.85rem; color: var(--text-muted); margin-top: 0.5rem;">
                Cadastrados no sistema
            </p>
            <a href="<?php echo BASE_URL; ?>clientes" style="display: inline-block; margin-top: 1rem; color: var(--success); text-decoration: none; font-weight: 500; font-size: 0.9rem;">
                Gerenciar clientes →
            </a>
        </div>

        <!-- Card: Faturamento -->
        <div class="card">
            <h2>💰 Faturamento (Mês)</h2>
            <p class="card-value info-text">R$ 15.000,00</p>
            <p style="font-size: 0.85rem; color: var(--text-muted); margin-top: 0.5rem;">
                Receita do mês atual
            </p>
            <a href="<?php echo BASE_URL; ?>ordens" style="display: inline-block; margin-top: 1rem; color: var(--info); text-decoration: none; font-weight: 500; font-size: 0.9rem;">
                Ver detalhes →
            </a>
        </div>
    </div>

    <!-- SEÇÃO DE AÇÕES RÁPIDAS -->
    <div style="margin-top: 3rem;">
        <h2 style="margin-bottom: 1.5rem; color: var(--text-primary);">⚡ Ações Rápidas</h2>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
            <a href="<?php echo BASE_URL; ?>clientes/criar" class="btn btn-primary" style="text-align: center; padding: 1rem;">
                ➕ Novo Cliente
            </a>
            <a href="<?php echo BASE_URL; ?>ordens/form" class="btn btn-primary" style="text-align: center; padding: 1rem;">
                📝 Nova Ordem de Serviço
            </a>
            <a href="<?php echo BASE_URL; ?>usuarios/criar" class="btn btn-secondary" style="text-align: center; padding: 1rem;">
                👤 Novo Usuário
            </a>
            <a href="<?php echo BASE_URL; ?>clientes" class="btn btn-secondary" style="text-align: center; padding: 1rem;">
                👥 Gerenciar Clientes
            </a>
        </div>
    </div>

    <!-- INFORMAÇÃO ADICIONAL -->
    <div class="card" style="margin-top: 3rem; background: linear-gradient(135deg, rgba(231, 76, 60, 0.1) 0%, rgba(231, 76, 60, 0.05) 100%); border-left: 4px solid var(--primary-red);">
        <h3 style="color: var(--primary-red); margin-bottom: 0.5rem;">💡 Dica</h3>
        <p style="margin: 0; color: var(--text-secondary);">
            Use o menu lateral para navegar entre as funcionalidades do sistema. Clique em qualquer item para acessar a seção desejada.
        </p>
    </div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
