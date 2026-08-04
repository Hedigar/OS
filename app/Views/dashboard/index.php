<?php
$current_page = 'dashboard';
require_once __DIR__ . '/../layout/main.php';

use App\Core\Auth;

$nivel = $user['nivel_acesso'] ?? 'usuario';
$isAdmin = Auth::isAdmin();
?>

<div class="container-fluid px-4">
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
                <a href="<?php echo BASE_URL; ?>ordens/form" class="btn btn-primary shadow-sm border-0" style="border-radius: 10px;">
                    <i class="fas fa-plus-circle me-1"></i> Nova OS
                </a>
                <a href="<?php echo BASE_URL; ?>atendimentos-externos/form" class="btn btn-info text-white shadow-sm border-0" style="border-radius: 10px;">
                    <i class="fas fa-truck me-1"></i> Novo Externo
                </a>
                <a href="<?php echo BASE_URL; ?>clientes/criar" class="btn btn-secondary shadow-sm border-0" style="border-radius: 10px;">
                    <i class="fas fa-user-plus me-1"></i> Cliente
                </a>
            </div>
        </div>
    </div>

    <?php if ($isAdmin): ?>
    <!-- SEÇÃO FINANCEIRA EXECUTIVA (PRODUÇÃO VS CAIXA) -->
    <div class="row g-3 mb-4">
        <!-- PRODUÇÃO (DRE) -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm overflow-hidden" style="border-radius: 15px; background: linear-gradient(135deg, #2c3e50, #000000); color: white;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold mb-0 text-white-50"><i class="fas fa-industry me-2"></i> Relatório de Produção (DRE)</h5>
                        <span class="badge bg-primary bg-opacity-25 text-white">Mês Atual</span>
                    </div>
                    <div class="row align-items-end">
                        <div class="col-6">
                            <small class="d-block text-white-50">Faturamento Produzido</small>
                            <h3 class="fw-bold mb-0">R$ <?php echo number_format($stats['faturamento_producao'], 2, ',', '.'); ?></h3>
                        </div>
                        <div class="col-6 text-end">
                            <small class="d-block text-white-50">Lucro Previsto</small>
                            <h3 class="fw-bold mb-0 text-success">R$ <?php echo number_format($stats['lucro_mes'], 2, ',', '.'); ?></h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- CAIXA (REAL) -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm overflow-hidden" style="border-radius: 15px; background: linear-gradient(135deg, #1e3c72, #2a5298); color: white;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold mb-0 text-white-50"><i class="fas fa-wallet me-2"></i> Visão de Caixa (Real)</h5>
                        <span class="badge bg-success bg-opacity-25 text-white">Mês Atual</span>
                    </div>
                    <div class="row align-items-end">
                        <div class="col-6">
                            <small class="d-block text-white-50">Entrada Real (Dinheiro)</small>
                            <h3 class="fw-bold mb-0">R$ <?php echo number_format($stats['faturamento_caixa'], 2, ',', '.'); ?></h3>
                        </div>
                        <div class="col-6 text-end">
                            <small class="d-block text-white-50">Saldo Final (Líquido)</small>
                            <h3 class="fw-bold mb-0 text-info">R$ <?php echo number_format($stats['lucro_caixa'], 2, ',', '.'); ?></h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <div class="row g-4">
        <!-- COLUNA PRINCIPAL -->
        <div class="col-xl-9 col-lg-8">
            
            <!-- SEÇÃO 1: ORDENS DE SERVIÇO -->
            <div class="mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0"><i class="fas fa-tools text-primary me-2"></i> Ordens de Serviço</h5>
                    <a href="<?php echo BASE_URL; ?>ordens" class="btn btn-sm btn-link text-decoration-none">Ver todas <i class="fas fa-chevron-right ms-1"></i></a>
                </div>
                <div class="row g-3">
                    <!-- OS Abertas -->
                    <div class="col-md-3">
                        <a href="<?php echo BASE_URL; ?>ordens?status_id=1" class="text-decoration-none">
                            <div class="card h-100 border-0 shadow-sm stat-card-hover" style="border-radius: 12px;">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <div class="bg-primary bg-opacity-10 p-2 rounded-circle">
                                            <i class="fas fa-folder-open text-primary"></i>
                                        </div>
                                        <span class="text-muted small fw-bold">Ativas</span>
                                    </div>
                                    <h2 class="fw-bold mb-0"><?php echo $stats['total_abertas']; ?></h2>
                                    <small class="text-muted">OS em Aberto</small>
                                </div>
                            </div>
                        </a>
                    </div>
                    <!-- Pagamentos Pendentes OS -->
                    <div class="col-md-3">
                        <a href="<?php echo BASE_URL; ?>ordens?status_pagamento=pendente" class="text-decoration-none">
                            <div class="card h-100 border-0 shadow-sm stat-card-hover" style="border-radius: 12px;">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <div class="bg-danger bg-opacity-10 p-2 rounded-circle">
                                            <i class="fas fa-money-bill-wave text-danger"></i>
                                        </div>
                                        <span class="text-danger small fw-bold">Pendente</span>
                                    </div>
                                    <h2 class="fw-bold mb-0 <?php echo $stats['total_pag_pendentes_os'] > 0 ? 'text-danger' : ''; ?>">
                                        <?php echo $stats['total_pag_pendentes_os']; ?>
                                    </h2>
                                    <small class="text-muted">Pag. Pendentes</small>
                                </div>
                            </div>
                        </a>
                    </div>
                    <!-- Pagamentos Parciais OS -->
                    <div class="col-md-3">
                        <a href="<?php echo BASE_URL; ?>ordens?status_pagamento=parcial" class="text-decoration-none">
                            <div class="card h-100 border-0 shadow-sm stat-card-hover" style="border-radius: 12px;">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <div class="bg-warning bg-opacity-10 p-2 rounded-circle">
                                            <i class="fas fa-adjust text-warning"></i>
                                        </div>
                                        <span class="text-warning small fw-bold">Parcial</span>
                                    </div>
                                    <h2 class="fw-bold mb-0 <?php echo $stats['total_pag_parciais_os'] > 0 ? 'text-warning' : ''; ?>">
                                        <?php echo $stats['total_pag_parciais_os']; ?>
                                    </h2>
                                    <small class="text-muted">Pag. Parciais</small>
                                </div>
                            </div>
                        </a>
                    </div>
                    <!-- Inconsistências -->
                    <div class="col-md-3">
                        <a href="<?php echo BASE_URL; ?>ordens?inconsistencia=1" class="text-decoration-none">
                            <div class="card h-100 border-0 shadow-sm stat-card-hover <?php echo $stats['total_inconsistencias'] > 0 ? 'bg-danger bg-opacity-10 border border-danger' : ''; ?>" style="border-radius: 12px;">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <div class="bg-dark bg-opacity-10 p-2 rounded-circle">
                                            <i class="fas fa-search-minus text-dark"></i>
                                        </div>
                                        <span class="text-dark small fw-bold">Auditoria</span>
                                    </div>
                                    <h2 class="fw-bold mb-0 <?php echo $stats['total_inconsistencias'] > 0 ? 'text-danger' : ''; ?>">
                                        <?php echo $stats['total_inconsistencias']; ?>
                                    </h2>
                                    <small class="<?php echo $stats['total_inconsistencias'] > 0 ? 'text-danger fw-bold' : 'text-muted'; ?>">Sem Laudo Técnico</small>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>

            <!-- SEÇÃO 2: ATENDIMENTOS EXTERNOS -->
            <div class="mb-4">
                <h5 class="fw-bold mb-3"><i class="fas fa-car-side text-info me-2"></i> Atendimentos Externos</h5>
                <div class="row g-3">
                    <div class="col-md-6">
                        <a href="<?php echo BASE_URL; ?>atendimentos-externos?status_pagamento=pendente" class="text-decoration-none">
                            <div class="card border-0 shadow-sm stat-card-hover" style="border-radius: 15px;">
                                <div class="card-body d-flex align-items-center p-4">
                                    <div class="bg-danger bg-opacity-10 p-3 rounded-circle me-4">
                                        <i class="fas fa-clock text-danger fa-lg"></i>
                                    </div>
                                    <div>
                                        <h4 class="fw-bold mb-0"><?php echo $stats['total_ext_pendentes']; ?></h4>
                                        <p class="text-muted mb-0">Pagamentos Pendentes (Externo)</p>
                                    </div>
                                    <i class="fas fa-chevron-right ms-auto text-muted opacity-25"></i>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-6">
                        <a href="<?php echo BASE_URL; ?>atendimentos-externos?status_pagamento=parcial" class="text-decoration-none">
                            <div class="card border-0 shadow-sm stat-card-hover" style="border-radius: 15px;">
                                <div class="card-body d-flex align-items-center p-4">
                                    <div class="bg-warning bg-opacity-10 p-3 rounded-circle me-4">
                                        <i class="fas fa-hourglass-half text-warning fa-lg"></i>
                                    </div>
                                    <div>
                                        <h4 class="fw-bold mb-0"><?php echo $stats['total_ext_parciais']; ?></h4>
                                        <p class="text-muted mb-0">Pagamentos Parciais (Externo)</p>
                                    </div>
                                    <i class="fas fa-chevron-right ms-auto text-muted opacity-25"></i>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>

            <!-- SEÇÃO 3: CRM E PÓS-VENDA -->
            <div class="mb-4">
                <h5 class="fw-bold mb-3"><i class="fas fa-chart-line text-success me-2"></i> CRM & Pós-Venda <small class="text-muted fw-normal fs-6">(Na Semana)</small></h5>
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm h-100" style="border-radius: 15px;">
                            <div class="card-body p-4 text-center">
                                <div class="bg-primary bg-opacity-10 p-3 rounded-circle d-inline-flex mb-3">
                                    <i class="fas fa-users text-primary fa-lg"></i>
                                </div>
                                <h3 class="fw-bold mb-1"><?php echo $stats['total_crm_contatados']; ?></h3>
                                <p class="text-muted mb-0">Contatados CRM</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm h-100" style="border-radius: 15px;">
                            <div class="card-body p-4 text-center">
                                <div class="bg-success bg-opacity-10 p-3 rounded-circle d-inline-flex mb-3">
                                    <i class="fas fa-check-circle text-success fa-lg"></i>
                                </div>
                                <h3 class="fw-bold mb-1"><?php echo $stats['total_pos_venda_realizados']; ?></h3>
                                <p class="text-muted mb-0">Pós-Venda Realizado</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <a href="<?php echo BASE_URL; ?>pos-venda" class="text-decoration-none">
                            <div class="card border-0 shadow-sm h-100 stat-card-hover" style="border-radius: 15px;">
                                <div class="card-body p-4 text-center">
                                    <div class="bg-danger bg-opacity-10 p-3 rounded-circle d-inline-flex mb-3">
                                        <i class="fas fa-exclamation-triangle text-danger fa-lg"></i>
                                    </div>
                                    <h3 class="fw-bold mb-1 <?php echo $stats['total_pos_venda_pendentes'] > 0 ? 'text-danger' : ''; ?>">
                                        <?php echo $stats['total_pos_venda_pendentes']; ?>
                                    </h3>
                                    <p class="text-muted mb-0">Pós-Venda Pendente</p>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>

            <!-- GRÁFICO DE TENDÊNCIA -->
            
        </div>

        <!-- COLUNA LATERAL -->
        <div class="col-xl-3 col-lg-4">
            <!-- ALERTAS -->
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 20px;">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4"><i class="fas fa-bell text-warning me-2"></i> Alertas Ativos</h5>
                    <div id="alerts-container">
                        <?php if (empty($alertas)): ?>
                            <div class="text-center py-5 opacity-50">
                                <i class="fas fa-check-circle text-success fa-3x mb-3"></i>
                                <p class="mb-0">Sem pendências críticas</p>
                            </div>
                        <?php else: ?>
                            <div class="d-flex flex-column gap-3">
                                <?php foreach ($alertas as $index => $alerta): if ($index >= 5) break; ?>
                                    <div class="p-3 rounded-4 border-start border-4 border-<?php echo ($alerta['prioridade'] ?? '') === 'alta' ? 'danger' : 'warning'; ?> bg-light position-relative">
                                        <div class="small fw-bold text-dark mb-1">OS #<?php echo $alerta['os_id'] ?? ''; ?></div>
                                        <div class="small text-secondary mb-2 lh-sm"><?php echo htmlspecialchars($alerta['mensagem'] ?? ''); ?></div>
                                        <a href="<?php echo BASE_URL; ?>ordens/view?id=<?php echo $alerta['os_id']; ?>" class="btn btn-sm btn-white shadow-sm border py-0 px-2 fw-bold" style="font-size: 10px;">TRATAR AGORA</a>
                                    </div>
                                <?php endforeach; ?>
                                <?php if (count($alertas) > 5): ?>
                                    <button class="btn btn-sm btn-link text-center text-decoration-none">Ver mais <?php echo count($alertas)-5; ?> alertas...</button>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- CHECKLIST -->
            <div class="card border-0 shadow-sm" style="border-radius: 20px;">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3"><i class="fas fa-calendar-check text-primary me-2"></i> Tasks do Dia</h5>
                    <div class="d-flex gap-2 mb-3">
                        <input type="text" id="new-task-input" class="form-control form-control-sm border-0 bg-light" placeholder="O que fazer hoje?" style="border-radius: 8px;">
                        <button id="add-task-btn" class="btn btn-primary btn-sm rounded-circle"><i class="fas fa-plus"></i></button>
                    </div>
                    <div id="task-list" class="d-flex flex-column gap-2">
                        <!-- Render via JS -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    :root {
        --bg-secondary: #f8f9fa;
    }
    body {
        background-color: #f4f6f9;
    }
    .stat-card-hover {
        transition: all 0.3s cubic-bezier(0.165, 0.84, 0.44, 1);
    }
    .stat-card-hover:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.08) !important;
    }
    .btn-white {
        background: white;
        color: #333;
    }
    .rounded-4 { border-radius: 12px !important; }
    canvas { max-width: 100%; }
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
                    label: 'Abertura de OS',
                    data: values,
                    borderColor: '#0d6efd',
                    backgroundColor: 'rgba(13, 110, 253, 0.1)',
                    fill: true,
                    tension: 0.4,
                    borderWidth: 3,
                    pointBackgroundColor: '#0d6efd',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 7
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1e293b',
                        titleFont: { size: 14, weight: 'bold' },
                        padding: 12,
                        cornerRadius: 8
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1, color: '#94a3b8' },
                        grid: { borderDash: [5, 5], color: '#e2e8f0' }
                    },
                    x: {
                        ticks: { color: '#94a3b8' },
                        grid: { display: false }
                    }
                }
            }
        });
    });
</script>
<script src="<?php echo BASE_URL; ?>assets/js/dashboard.js"></script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
