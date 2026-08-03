<?php
$current_page = 'dashboard';
require_once __DIR__ . '/../layout/main.php';

use App\Core\Auth;

$nivel = $user['nivel_acesso'] ?? 'usuario';
$isAdmin = Auth::isAdmin();
?>

<div class="container">
    <!-- CABEÇALHO E AÇÕES RÁPIDAS -->
    <div class="row mb-4 align-items-center">
        <div class="col-md-6">
            <h1 class="fw-bold mb-0">👋 Olá, <?php echo htmlspecialchars($user['nome'] ?? 'Usuário'); ?>!</h1>
            <p class="text-secondary fs-5 mb-0">
                <?php 
                if ($isAdmin) echo "Visão Geral Executiva";
                elseif (Auth::isTecnico()) echo "Painel de Manutenção e Ordens";
                else echo "Painel de Atendimento e Recepção";
                ?>
            </p>
        </div>
        <div class="col-md-6 text-md-end mt-3 mt-md-0">
            <div class="d-flex gap-2 justify-content-md-end flex-wrap">
                <a href="<?php echo BASE_URL; ?>ordens/form" class="btn btn-primary shadow-sm">
                    <i class="fas fa-plus-circle me-1"></i> Nova OS
                </a>
                <a href="<?php echo BASE_URL; ?>atendimentos-externos/form" class="btn btn-info text-white shadow-sm">
                    <i class="fas fa-truck me-1"></i> Novo Externo
                </a>
                <a href="<?php echo BASE_URL; ?>clientes/criar" class="btn btn-secondary shadow-sm">
                    <i class="fas fa-user-plus me-1"></i> Cliente
                </a>
            </div>
        </div>
    </div>

    <div class="dashboard-grid">
        <!-- COLUNA PRINCIPAL -->
        <div class="main-content-area">
            
            <!-- SEÇÃO 1: ORDENS DE SERVIÇO -->
            <div class="mb-4">
                <h4 class="fw-bold mb-3"><i class="fas fa-tools text-primary me-2"></i> Ordens de Serviço</h4>
                <div class="row g-3">
                    <!-- OS Abertas -->
                    <div class="col-md-3">
                        <a href="<?php echo BASE_URL; ?>ordens?status_id=1" class="text-decoration-none">
                            <div class="card h-100 border-0 shadow-sm stat-card-new">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="text-muted text-uppercase fw-semibold small">OS Abertas</h6>
                                            <h2 class="fw-bold mb-0"><?php echo $stats['total_abertas']; ?></h2>
                                        </div>
                                        <div class="bg-primary bg-opacity-10 p-2 rounded">
                                            <i class="fas fa-folder-open text-primary"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                    <!-- Pagamentos Pendentes OS -->
                    <div class="col-md-3">
                        <a href="<?php echo BASE_URL; ?>ordens?status_pagamento=pendente" class="text-decoration-none">
                            <div class="card h-100 border-0 shadow-sm stat-card-new">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="text-muted text-uppercase fw-semibold small">Pag. Pendentes</h6>
                                            <h2 class="fw-bold mb-0 <?php echo $stats['total_pag_pendentes_os'] > 0 ? 'text-danger' : ''; ?>">
                                                <?php echo $stats['total_pag_pendentes_os']; ?>
                                            </h2>
                                        </div>
                                        <div class="bg-danger bg-opacity-10 p-2 rounded">
                                            <i class="fas fa-money-bill-wave text-danger"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                    <!-- Pagamentos Parciais OS -->
                    <div class="col-md-3">
                        <a href="<?php echo BASE_URL; ?>ordens?status_pagamento=parcial" class="text-decoration-none">
                            <div class="card h-100 border-0 shadow-sm stat-card-new">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="text-muted text-uppercase fw-semibold small">Pag. Parciais</h6>
                                            <h2 class="fw-bold mb-0 <?php echo $stats['total_pag_parciais_os'] > 0 ? 'text-warning' : ''; ?>">
                                                <?php echo $stats['total_pag_parciais_os']; ?>
                                            </h2>
                                        </div>
                                        <div class="bg-warning bg-opacity-10 p-2 rounded">
                                            <i class="fas fa-adjust text-warning"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                    <!-- Inconsistências -->
                    <div class="col-md-3">
                        <a href="<?php echo BASE_URL; ?>ordens?inconsistencia=1" class="text-decoration-none">
                            <div class="card h-100 border-0 shadow-sm stat-card-new <?php echo $stats['total_inconsistencias'] > 0 ? 'border-start border-danger border-4' : ''; ?>">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="text-muted text-uppercase fw-semibold small">Inconsistências</h6>
                                            <h2 class="fw-bold mb-0 <?php echo $stats['total_inconsistencias'] > 0 ? 'text-danger' : ''; ?>">
                                                <?php echo $stats['total_inconsistencias']; ?>
                                            </h2>
                                        </div>
                                        <div class="bg-dark bg-opacity-10 p-2 rounded">
                                            <i class="fas fa-search-minus text-dark"></i>
                                        </div>
                                    </div>
                                    <?php if ($stats['total_inconsistencias'] > 0): ?>
                                        <span class="badge bg-danger mt-2">Sem Laudo</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>

            <!-- SEÇÃO 2: ATENDIMENTOS EXTERNOS -->
            <div class="mb-4">
                <h4 class="fw-bold mb-3"><i class="fas fa-car-side text-info me-2"></i> Atendimentos Externos</h4>
                <div class="row g-3">
                    <div class="col-md-6">
                        <a href="<?php echo BASE_URL; ?>atendimentos-externos?status_pagamento=pendente" class="text-decoration-none">
                            <div class="card h-100 border-0 shadow-sm stat-card-new">
                                <div class="card-body d-flex align-items-center">
                                    <div class="bg-danger bg-opacity-10 p-3 rounded me-3">
                                        <i class="fas fa-clock text-danger fa-lg"></i>
                                    </div>
                                    <div>
                                        <h6 class="text-muted text-uppercase fw-semibold small mb-1">Externos com Pagamento Pendente</h6>
                                        <h3 class="fw-bold mb-0"><?php echo $stats['total_ext_pendentes']; ?></h3>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-6">
                        <a href="<?php echo BASE_URL; ?>atendimentos-externos?status_pagamento=parcial" class="text-decoration-none">
                            <div class="card h-100 border-0 shadow-sm stat-card-new">
                                <div class="card-body d-flex align-items-center">
                                    <div class="bg-warning bg-opacity-10 p-3 rounded me-3">
                                        <i class="fas fa-hourglass-half text-warning fa-lg"></i>
                                    </div>
                                    <div>
                                        <h6 class="text-muted text-uppercase fw-semibold small mb-1">Externos com Pagamento Parcial</h6>
                                        <h3 class="fw-bold mb-0"><?php echo $stats['total_ext_parciais']; ?></h3>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>

            <!-- SEÇÃO 3: CRM E PÓS-VENDA (SEMANAL) -->
            <div class="mb-5">
                <h4 class="fw-bold mb-3"><i class="fas fa-chart-line text-success me-2"></i> CRM & Pós-Venda (Na Semana)</h4>
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="card h-100 border-0 shadow-sm stat-card-new">
                            <div class="card-body">
                                <h6 class="text-muted text-uppercase fw-semibold small">Contatados via CRM</h6>
                                <div class="d-flex align-items-center mt-2">
                                    <h2 class="fw-bold mb-0 me-2"><?php echo $stats['total_crm_contatados']; ?></h2>
                                    <span class="text-success small"><i class="fas fa-user-check me-1"></i> clientes</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card h-100 border-0 shadow-sm stat-card-new">
                            <div class="card-body">
                                <h6 class="text-muted text-uppercase fw-semibold small">Pós-Venda Realizados</h6>
                                <div class="d-flex align-items-center mt-2">
                                    <h2 class="fw-bold mb-0 me-2 text-success"><?php echo $stats['total_pos_venda_realizados']; ?></h2>
                                    <i class="fas fa-check-double text-success"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <a href="<?php echo BASE_URL; ?>pos-venda" class="text-decoration-none">
                            <div class="card h-100 border-0 shadow-sm stat-card-new">
                                <div class="card-body">
                                    <h6 class="text-muted text-uppercase fw-semibold small">Pós-Venda Pendentes</h6>
                                    <div class="d-flex align-items-center mt-2">
                                        <h2 class="fw-bold mb-0 me-2 <?php echo $stats['total_pos_venda_pendentes'] > 0 ? 'text-danger' : ''; ?>">
                                            <?php echo $stats['total_pos_venda_pendentes']; ?>
                                        </h2>
                                        <i class="fas fa-exclamation-circle <?php echo $stats['total_pos_venda_pendentes'] > 0 ? 'text-danger' : 'text-muted'; ?>"></i>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>

            <!-- GRÁFICO DE TENDÊNCIA -->
            <div class="card border-0 shadow-sm p-4 mb-4" style="border-radius: 20px; background: white;">
                <h5 class="fw-bold mb-4"><i class="fas fa-chart-area text-primary me-2"></i> Volume de Abertura (Últimos 7 dias)</h5>
                <canvas id="trendChart" height="100"></canvas>
            </div>

            <!-- ÁREA DE FLUXO DE ATIVIDADES -->
            <div class="card border-0 shadow-sm p-4" style="border-radius: 20px; background: var(--bg-secondary);">
                <h5 class="mb-4 fw-bold"><i class="fas fa-history text-info me-2"></i> Fluxo de Atividades Recentes</h5>
                <div class="activity-feed" style="max-height: 400px; overflow-y: auto;">
                    <?php if (empty($atividades)): ?>
                        <p class="text-muted text-center py-4">Nenhuma atividade registrada recentemente.</p>
                    <?php else: ?>
                        <div class="list-group list-group-flush bg-transparent">
                            <?php foreach ($atividades as $log): ?>
                                <?php 
                                    $referencia = $log['referencia'] ?? '';
                                    $acao = $log['acao'] ?? '';
                                    $osId = null;
                                    if (preg_match('/#(\d+)/', $referencia, $matches)) { $osId = $matches[1]; }
                                    $link = "#";
                                    if ($osId && (strpos($referencia, 'Ordem') !== false || strpos($referencia, 'OS') !== false)) {
                                        $link = BASE_URL . "ordens/view?id=" . $osId;
                                    } elseif (preg_match('/Cliente #(\d+)/', $referencia, $matches)) {
                                        $link = BASE_URL . "clientes/view?id=" . $matches[1];
                                    }
                                ?>
                                <a href="<?php echo $link; ?>" class="list-group-item list-group-item-action bg-transparent border-0 px-0 py-3 d-flex align-items-start gap-3">
                                    <div class="activity-icon bg-white shadow-sm rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; flex-shrink: 0;">
                                        <?php 
                                            if (strpos($acao, 'Criou') !== false) echo '<i class="fas fa-plus text-success small"></i>';
                                            elseif (strpos($acao, 'Excluiu') !== false) echo '<i class="fas fa-trash text-danger small"></i>';
                                            elseif (strpos($acao, 'Atualizou') !== false) echo '<i class="fas fa-edit text-warning small"></i>';
                                            elseif (strpos($acao, 'Status') !== false) echo '<i class="fas fa-sync text-info small"></i>';
                                            else echo '<i class="fas fa-info-circle text-secondary small"></i>';
                                        ?>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <h6 class="mb-0 fw-bold text-dark" style="font-size: 0.9rem;">
                                                <?php echo htmlspecialchars($acao); ?>
                                            </h6>
                                            <small class="text-muted" style="font-size: 0.7rem;">
                                                <?php echo isset($log['created_at']) ? date('d/m H:i', strtotime($log['created_at'])) : '--/--'; ?>
                                            </small>
                                        </div>
                                        <p class="mb-0 text-secondary" style="font-size: 0.8rem;">
                                            <?php echo htmlspecialchars($referencia); ?>
                                        </p>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- COLUNA LATERAL -->
        <aside class="tasks-sidebar-content">
            <div class="tasks-section shadow-sm mb-4">
                <h5 class="fw-bold mb-3"><i class="fas fa-bell text-warning me-2"></i> Alertas Críticos</h5>
                <div id="alerts-container">
                    <?php if (empty($alertas)): ?>
                        <div class="text-muted fs-sm p-3 text-center border rounded-3 border-dashed">
                            Tudo sob controle!
                        </div>
                    <?php else: ?>
                        <?php foreach ($alertas as $alerta): ?>
                            <div class="alert-item mb-2 p-2 rounded-3 border bg-white shadow-sm">
                                <div class="d-flex flex-column">
                                    <div class="small mb-2">
                                        <i class="fas fa-circle text-<?php echo ($alerta['prioridade'] ?? '') === 'alta' ? 'danger' : 'warning'; ?> me-1" style="font-size: 8px;"></i>
                                        <?php echo htmlspecialchars($alerta['mensagem'] ?? ''); ?>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <?php if (!empty($alerta['os_id'])): ?>
                                            <a href="<?php echo BASE_URL; ?>ordens/view?id=<?php echo (int)$alerta['os_id']; ?>" class="btn btn-xs btn-outline-primary py-0 px-2 small" style="font-size: 11px;">
                                                Ver OS
                                            </a>
                                        <?php endif; ?>
                                        <?php 
                                            $isPosVenda = ($alerta['tipo'] ?? '') === 'pos_venda';
                                            $telefone = preg_replace('/\D+/', '', $alerta['cliente_telefone'] ?? '');
                                            if ($isPosVenda && !empty($telefone)) {
                                                echo '<a href="#" class="btn btn-xs btn-success py-0 px-2 small" style="font-size: 11px;">WhatsApp</a>';
                                            }
                                        ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <div class="tasks-section shadow-sm">
                <h5 class="fw-bold mb-3"><i class="fas fa-calendar-check text-primary me-2"></i> Checklist do Dia</h5>
                <div class="d-flex gap-2 mb-3">
                    <input type="text" id="new-task-input" class="form-control form-control-sm" placeholder="Nova tarefa...">
                    <button id="add-task-btn" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i></button>
                </div>
                <div id="task-list">
                    <!-- Tasks renderizadas via JS -->
                </div>
            </div>
        </aside>
    </div>
</div>

<style>
    .stat-card-new {
        transition: transform 0.2s, box-shadow 0.2s;
        border-radius: 15px;
    }
    .stat-card-new:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
    }
    .btn-xs {
        padding: 1px 5px;
        font-size: 10px;
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    window.dashboardAlerts = <?php echo json_encode($alertas ?? []); ?>;
    window.trendData = <?php echo json_encode($stats['trend'] ?? []); ?>;

    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('trendChart').getContext('2d');
        const labels = window.trendData.map(d => d.date);
        const values = window.trendData.map(d => d.total);

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Novas OS',
                    data: values,
                    borderColor: '#0d6efd',
                    backgroundColor: 'rgba(13, 110, 253, 0.1)',
                    fill: true,
                    tension: 0.4,
                    borderWidth: 3,
                    pointBackgroundColor: '#0d6efd',
                    pointRadius: 4
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1 }
                    }
                }
            }
        });
    });
</script>
<script src="<?php echo BASE_URL; ?>assets/js/dashboard.js"></script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
