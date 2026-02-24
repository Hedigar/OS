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
            <div class="mb-5">
                <h1 class="fw-bold">👋 Olá, <?php echo htmlspecialchars($user['nome'] ?? 'Usuário'); ?>!</h1>
                <p class="text-secondary fs-5">
                    <?php 
                    if (Auth::isAdmin()) echo "Visão Geral do Sistema";
                    elseif (Auth::isTecnico()) echo "Painel de Manutenção e Ordens";
                    else echo "Painel de Atendimento e Recepção";
                    ?>
                </p>
            </div>

            <!-- AÇÕES RÁPIDAS -->
            <div class="mb-5">
                <h2 class="mb-4 fw-bold"><i class="fas fa-bolt text-warning me-2"></i> Ações Rápidas</h2>
                <div class="quick-actions">
                    <a href="<?php echo BASE_URL; ?>ordens/form" class="btn btn-primary">
                        <i class="fas fa-plus-circle"></i> Nova OS
                    </a>
                    <a href="<?php echo BASE_URL; ?>clientes/criar" class="btn btn-secondary">
                        <i class="fas fa-user-plus"></i> Novo Cliente
                    </a>
                    <?php if (Auth::isAdmin()): ?>
                        <a href="<?php echo BASE_URL; ?>usuarios/criar" class="btn btn-secondary">
                            <i class="fas fa-user-shield"></i> Novo Usuário
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <div class="dashboard-cards">
                <div class="card stat-card">
                    <div>
                        <div class="stat-icon"><i class="fas fa-folder-open text-info"></i></div>
                        <h2>OS em Aberto</h2>
                    </div>
                    <div>
                        <p class="card-value info-text"><?php echo str_pad($stats['total_abertas'], 2, '0', STR_PAD_LEFT); ?></p>
                    </div>
                </div>

                <div class="card stat-card">
                    <div>
                        <div class="stat-icon"><i class="fas fa-exclamation-triangle text-danger"></i></div>
                        <h2>OS Atrasadas</h2>
                    </div>
                    <div>
                        <p class="card-value danger-text"><?php echo str_pad($stats['total_atrasadas'], 2, '0', STR_PAD_LEFT); ?></p>
                    </div>
                </div>

                <?php if (Auth::isAdmin()): ?>
                    <div class="card stat-card">
                        <div>
                            <div class="stat-icon"><i class="fas fa-hand-holding-usd text-success"></i></div>
                            <h2>Faturamento (Finalizadas)</h2>
                        </div>
                        <div>
                            <p class="card-value success-text">R$ <?php echo number_format($stats['valor_finalizadas'], 2, ',', '.'); ?></p>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="card stat-card">
                        <div>
                            <div class="stat-icon"><i class="fas fa-check-circle text-success"></i></div>
                            <h2>OS Finalizadas</h2>
                        </div>
                        <div>
                            <p class="card-value success-text"><?php echo str_pad($stats['total_finalizadas'], 2, '0', STR_PAD_LEFT); ?></p>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (Auth::isAdmin()): ?>
                    <div class="card stat-card">
                        <div>
                            <div class="stat-icon"><i class="fas fa-piggy-bank text-warning"></i></div>
                            <h2>Lucro do Mês</h2>
                        </div>
                        <div>
                            <p class="card-value warning-text">R$ <?php echo number_format($stats['lucro_mes'], 2, ',', '.'); ?></p>
                        </div>
                    </div>
                <?php endif; ?>

                <?php 
                    $posVendaCount = 0;
                    if (!empty($alertas)) {
                        foreach ($alertas as $a) {
                            if (($a['tipo'] ?? '') === 'pos_venda') $posVendaCount++;
                        }
                    }
                ?>
                <div class="card stat-card">
                    <div>
                        <div class="stat-icon"><i class="fas fa-handshake text-primary"></i></div>
                        <h2>Pós-Venda</h2>
                    </div>
                    <div>
                        <p class="card-value warning-text"><?php echo str_pad($posVendaCount, 2, '0', STR_PAD_LEFT); ?></p>
                    </div>
                    <a href="<?php echo BASE_URL; ?>pos-venda" class="btn btn-outline-primary btn-sm mt-2">Abrir Painel</a>
                </div>

                <div class="card stat-card">
                    <div>
                        <div class="stat-icon"><i class="fas fa-filter text-primary"></i></div>
                        <h2>Filtros Rápidos</h2>
                    </div>
                    <div class="d-flex flex-wrap gap-2 mt-2">
                        <a href="<?php echo BASE_URL; ?>ordens?status_pagamento=pendente" class="btn btn-outline-primary btn-sm">Pagamento Pendente</a>
                        <a href="<?php echo BASE_URL; ?>ordens?status_entrega=nao_entregue" class="btn btn-outline-warning btn-sm">Entrega Pendente</a>
                        <a href="<?php echo BASE_URL; ?>ordens?sem_atualizacao_dias=2" class="btn btn-outline-danger btn-sm">Sem atualização 2+ dias</a>
                    </div>
                </div>
            </div>

            <!-- ÁREA DE FLUXO DE ATIVIDADES -->
            <div class="card border-0 shadow-sm p-4" style="border-radius: 20px; background: var(--bg-secondary);">
    <h3 class="mb-4 fw-bold"><i class="fas fa-history text-info me-2"></i> Fluxo de Atividades Recentes</h3>
    <div class="activity-feed" style="max-height: 420px; overflow-y: auto;">
        <?php if (empty($atividades)): ?>
            <p class="text-muted text-center py-4">Nenhuma atividade registrada recentemente.</p>
        <?php else: ?>
            <div class="list-group list-group-flush bg-transparent">
                <?php foreach ($atividades as $log): ?>
                    <?php 
                        // Proteção contra valores nulos para evitar erro de Deprecated
                        $referencia = $log['referencia'] ?? '';
                        $acao = $log['acao'] ?? '';
                        $osId = null;

                        // Tentar extrair o ID da OS da referência
                        if (preg_match('/#(\d+)/', $referencia, $matches)) {
                            $osId = $matches[1];
                        }
                        
                        $link = "#";
                        if ($osId && (strpos($referencia, 'Ordem') !== false || strpos($referencia, 'OS') !== false)) {
                            $link = (defined('BASE_URL') ? BASE_URL : '') . "ordens/view?id=" . $osId;
                        } elseif (preg_match('/Cliente #(\d+)/', $referencia, $matches)) {
                            $link = (defined('BASE_URL') ? BASE_URL : '') . "clientes/view?id=" . $matches[1];
                        }
                    ?>
                    <a href="<?php echo $link; ?>" class="list-group-item list-group-item-action bg-transparent border-0 px-0 py-3 d-flex align-items-start gap-3">
                        <div class="activity-icon bg-tertiary rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; flex-shrink: 0;">
                            <?php 
                                if (strpos($acao, 'Criou') !== false) echo '<i class="fas fa-plus text-success"></i>';
                                elseif (strpos($acao, 'Excluiu') !== false) echo '<i class="fas fa-trash text-danger"></i>';
                                elseif (strpos($acao, 'Atualizou') !== false) echo '<i class="fas fa-edit text-warning"></i>';
                                elseif (strpos($acao, 'Status') !== false) echo '<i class="fas fa-sync text-info"></i>';
                                else echo '<i class="fas fa-info-circle text-secondary"></i>';
                            ?>
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <h6 class="mb-0 fw-bold text-primary" style="font-size: 0.95rem;">
                                    <?php echo htmlspecialchars($acao); ?>
                                </h6>
                                <small class="text-muted" style="font-size: 0.75rem;">
                                    <?php echo isset($log['created_at']) ? date('d/m H:i', strtotime($log['created_at'])) : '--/--'; ?>
                                </small>
                            </div>
                            <p class="mb-1 text-secondary" style="font-size: 0.85rem;">
                                <?php echo htmlspecialchars($referencia); ?>
                            </p>
                            <small class="text-muted" style="font-size: 0.75rem;">
                                <i class="fas fa-user me-1"></i> <?php echo htmlspecialchars($log['usuario_nome'] ?? 'Sistema'); ?>
                            </small>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-chevron-right text-muted opacity-50" style="font-size: 0.8rem;"></i>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
        </div>

        <!-- COLUNA LATERAL (TASKS & ALERTS) -->
        <aside class="tasks-sidebar-content">
            <div class="tasks-section">
                <h3><i class="fas fa-bell text-warning"></i> Alertas</h3>
                <div id="alerts-container">
                    <?php if (empty($alertas)): ?>
                        <div class="text-muted fs-sm p-3 text-center border rounded-3 border-dashed">
                            Nenhum alerta crítico no momento.
                        </div>
                    <?php else: ?>
                        <?php foreach ($alertas as $alerta): ?>
                            <div class="alert-item mb-2 p-2 rounded-3 border">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>🔔 Alerta:</strong>
                                        <?php echo htmlspecialchars($alerta['mensagem'] ?? ''); ?>
                                    </div>
                                    <div class="ms-2">
                                        <?php if (!empty($alerta['os_id'])): ?>
                                            <a href="<?php echo BASE_URL; ?>ordens/view?id=<?php echo (int)$alerta['os_id']; ?>" class="btn btn-sm btn-outline-primary">
                                                Ver OS
                                            </a>
                                        <?php endif; ?>
                                        <?php 
                                            $isPosVenda = ($alerta['tipo'] ?? '') === 'pos_venda';
                                            $telefone = preg_replace('/\D+/', '', $alerta['cliente_telefone'] ?? '');
                                            if ($isPosVenda && !empty($telefone)) {
                                                $nomeCli = $alerta['cliente_nome'] ?? '';
                                                $msg = "Olá {$nomeCli}, tudo bem? Sobre a OS #{$alerta['os_id']}, gostaríamos de saber como está o equipamento e sua experiência. Seu feedback é importante.";
                                                $wa = "https://wa.me/55{$telefone}?text=" . urlencode($msg);
                                                echo '<a href="' . $wa . '" target="_blank" class="btn btn-sm btn-success ms-1">WhatsApp</a>';
                                            }
                                        ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <div class="tasks-section">
                <h3><i class="fas fa-calendar-check text-primary"></i> Tasks do Dia</h3>
                <div class="d-flex gap-2 mb-3">
                    <input type="text" id="new-task-input" class="form-control" placeholder="Nova tarefa..." style="border-radius: 10px;">
                    <button id="add-task-btn" class="btn btn-primary btn-sm" style="border-radius: 10px; padding: 0 15px;">Add</button>
                </div>
                <div id="task-list">
                    <div class="text-muted fs-sm p-3 text-center border rounded-3 border-dashed">
                        Sua lista de tarefas está vazia.
                    </div>
                </div>
            </div>
        </aside>
    </div>
</div>

<script>
    window.dashboardAlerts = <?php echo json_encode($alertas ?? []); ?>;
</script>
<script src="<?php echo BASE_URL; ?>assets/js/dashboard.js"></script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
