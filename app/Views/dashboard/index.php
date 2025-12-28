<?php
$current_page = 'dashboard';
require_once __DIR__ . '/../layout/main.php';

use App\Core\Auth;

$nivel = $user['nivel_acesso'] ?? 'usuario';
?>

<div class="container">
    <div class="dashboard-grid">
        <!-- COLUNA PRINCIPAL -->
        <div class="main-content-area">
            <!-- CABEÇALHO DINÂMICO -->
            <div style="margin-bottom: 2rem;">
                <h1>👋 Olá, <?php echo htmlspecialchars($user['nome'] ?? 'Usuário'); ?>!</h1>
                <p style="color: var(--text-secondary); font-size: 1rem;">
                    <?php 
                    if (Auth::isAdmin()) echo "Visão Geral do Sistema (Administrador)";
                    elseif (Auth::isTecnico()) echo "Painel de Manutenção e Ordens (Técnico)";
                    else echo "Painel de Atendimento e Recepção";
                    ?>
                </p>
            </div>

            <!-- CARDS DE ESTATÍSTICAS POR PERFIL -->
            <div class="dashboard-cards">
                <?php if (Auth::isAdmin()): ?>
                    <!-- ADMIN CARDS -->
                    <div class="card stat-card">
                        <div class="stat-icon">💰</div>
                        <h2>Faturamento Mensal</h2>
                        <p class="card-value success-text">R$ 18.450,00</p>
                        <p style="font-size: 0.85rem; color: var(--text-muted);">+12% em relação ao mês anterior</p>
                    </div>
                    <div class="card stat-card">
                        <div class="stat-icon">⚠️</div>
                        <h2>OS Atrasadas</h2>
                        <p class="card-value danger-text">05</p>
                        <p style="font-size: 0.85rem; color: var(--text-muted);">Requer atenção imediata</p>
                    </div>
                    <div class="card stat-card">
                        <div class="stat-icon">📈</div>
                        <h2>Lucro Estimado</h2>
                        <p class="card-value info-text">R$ 7.200,00</p>
                        <p style="font-size: 0.85rem; color: var(--text-muted);">Baseado em OS finalizadas</p>
                    </div>

                <?php elseif (Auth::isTecnico()): ?>
                    <!-- TÉCNICO CARDS -->
                    <div class="card stat-card">
                        <div class="stat-icon">🛠️</div>
                        <h2>OS em Aberto</h2>
                        <p class="card-value primary-red-text">12</p>
                        <p style="font-size: 0.85rem; color: var(--text-muted);">Na sua fila de trabalho</p>
                    </div>
                    <div class="card stat-card">
                        <div class="stat-icon">📦</div>
                        <h2>Aguardando Peças</h2>
                        <p class="card-value warning-text">04</p>
                        <p style="font-size: 0.85rem; color: var(--text-muted);">Pendentes de fornecedor</p>
                    </div>
                    <div class="card stat-card">
                        <div class="stat-icon">⏳</div>
                        <h2>Aguardando Cliente</h2>
                        <p class="card-value info-text">03</p>
                        <p style="font-size: 0.85rem; color: var(--text-muted);">Orçamentos enviados</p>
                    </div>

                <?php else: ?>
                    <!-- RECEPÇÃO CARDS -->
                    <div class="card stat-card">
                        <div class="stat-icon">✅</div>
                        <h2>Máquinas Finalizadas</h2>
                        <p class="card-value success-text">08</p>
                        <p style="font-size: 0.85rem; color: var(--text-muted);">Prontas para entrega hoje</p>
                    </div>
                    <div class="card stat-card">
                        <div class="stat-icon">📞</div>
                        <h2>Pós-Venda Pendente</h2>
                        <p class="card-value warning-text">15</p>
                        <p style="font-size: 0.85rem; color: var(--text-muted);">Clientes a serem contatados</p>
                    </div>
                    <div class="card stat-card">
                        <div class="stat-icon">🆕</div>
                        <h2>Novos Clientes</h2>
                        <p class="card-value info-text">24</p>
                        <p style="font-size: 0.85rem; color: var(--text-muted);">Cadastrados este mês</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- ÁREA DE GRÁFICOS / RESUMO (MOCKUP) -->
            <div class="card" style="min-height: 300px; display: flex; align-items: center; justify-content: center; flex-direction: column; background: var(--dark-secondary);">
                <div style="width: 100%; height: 200px; display: flex; align-items: flex-end; gap: 10px; padding: 20px;">
                    <!-- Simulação de gráfico de barras -->
                    <div style="flex: 1; background: var(--primary-red); height: 40%; border-radius: 4px 4px 0 0;"></div>
                    <div style="flex: 1; background: var(--primary-red); height: 70%; border-radius: 4px 4px 0 0;"></div>
                    <div style="flex: 1; background: var(--primary-red); height: 55%; border-radius: 4px 4px 0 0;"></div>
                    <div style="flex: 1; background: var(--primary-red); height: 90%; border-radius: 4px 4px 0 0;"></div>
                    <div style="flex: 1; background: var(--primary-red); height: 65%; border-radius: 4px 4px 0 0;"></div>
                    <div style="flex: 1; background: var(--primary-red); height: 80%; border-radius: 4px 4px 0 0;"></div>
                    <div style="flex: 1; background: var(--primary-red); height: 45%; border-radius: 4px 4px 0 0;"></div>
                </div>
                <p style="color: var(--text-muted);">Fluxo de Atividades - Últimos 7 dias</p>
            </div>

            <!-- AÇÕES RÁPIDAS -->
            <div style="margin-top: 2rem;">
                <h2 style="margin-bottom: 1rem;">⚡ Ações Rápidas</h2>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem;">
                    <a href="<?php echo BASE_URL; ?>ordens/form" class="btn btn-primary">📝 Nova OS</a>
                    <a href="<?php echo BASE_URL; ?>clientes/criar" class="btn btn-secondary">👥 Novo Cliente</a>
                    <?php if (Auth::isAdmin()): ?>
                        <a href="<?php echo BASE_URL; ?>usuarios/criar" class="btn btn-secondary">👤 Novo Usuário</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- COLUNA LATERAL (TASKS & ALERTS) -->
        <aside class="tasks-sidebar-content">
            <div class="tasks-section">
                <h3>🔔 Alertas</h3>
                <div id="alerts-container">
                    <!-- Alertas serão inseridos via JS -->
                </div>
            </div>

            <div class="tasks-section">
                <h3>📅 Tasks do Dia</h3>
                <div style="display: flex; gap: 5px; margin-bottom: 1rem;">
                    <input type="text" id="new-task-input" placeholder="Nova tarefa..." style="flex: 1; padding: 0.5rem;">
                    <button id="add-task-btn" class="btn btn-primary btn-sm">Add</button>
                </div>
                <div id="task-list">
                    <!-- Tasks serão inseridas via JS -->
                </div>
            </div>
        </aside>
    </div>
</div>

<script src="<?php echo BASE_URL; ?>assets/js/dashboard.js"></script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
