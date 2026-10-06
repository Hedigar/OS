<?php
$current_page = 'dashboard';
require_once __DIR__ . '/../layout/main.php';

use App\Core\Auth;

$nivel = $user['nivel_acesso'] ?? 'usuario';
$isAdmin = Auth::isAdmin();
$stats = $stats ?? [];
$alertas = $alertas ?? [];
$osParaCompra = $osParaCompra ?? [];

function formatCurrency($v){ return 'R$ '.number_format((float)$v,2,',','.'); }
?>

<div class="container-fluid px-4">
    <?php if ($isAdmin && !empty($osParaCompra)): ?>
    <div id="modalCompraPeca" class="modal show" style="display:block; position:fixed; z-index:2000; left:0; top:0; width:100%; height:100%; background:rgba(0,0,0,0.7); align-items:center; justify-content:center;">
        <div class="modal-content card" style="background: var(--bg-secondary, #fff); color: var(--text-primary, #000); margin: 3% auto; padding: 20px; width: 95%; max-width: 1100px; border-radius: 8px; border: 1px solid var(--border-color, #ccc); max-height:85vh; overflow:auto;">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="mb-0">🛒 O que tem que comprar • OS em Comprar Peça / POA</h3>
                <button type="button" class="btn btn-sm btn-secondary" onclick="document.getElementById('modalCompraPeca').style.display='none'">Fechar</button>
            </div>
            <p class="text-muted small mb-3">Lista atualizada a cada acesso. Atualize a observação para registrar a checagem.</p>
            <div class="table-responsive">
                <table class="table table-sm align-middle">
                    <thead>
                        <tr>
                            <th style="width:70px;">OS</th>
                            <th>Cliente</th>
                            <th>Peças a comprar</th>
                            <th>Status</th>
                            <th>Pagamento</th>
                            <th style="width:150px;">Entrada no Status</th>
                            <th style="width:180px;">Observar / Atualizar</th>
                            <th style="width:120px;">Ação</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($osParaCompra as $os): 
                        $dataEntrada = date('d/m/Y H:i', strtotime($os['data_entrada_status']));
                        $statusNome = htmlspecialchars($os['status_nome'] ?? '');
                        $statusId = (int)($os['status_id'] ?? 0);
                        $pecasLinksRaw = $os['pecas_links'] ?? '';
                        $linksHtml = '';
                        if (!empty($pecasLinksRaw)) {
                            $items = explode(';;', $pecasLinksRaw);
                            $parts = [];
                            foreach ($items as $it) {
                                [$desc,$link] = array_pad(explode('||', $it,2),2,'');
                                $descE = htmlspecialchars($desc);
                                if (!empty($link)) {
                                    $parts[] = '<a href="'.htmlspecialchars($link).'" target="_blank" title="Fornecedor">'.$descE.' 🔗</a>';
                                } else {
                                    $parts[] = $descE;
                                }
                            }
                            $linksHtml = implode('<br>', $parts);
                        }
                    ?>
                        <tr>
                            <td><a href="<?php echo BASE_URL; ?>ordens/view?id=<?php echo $os['id']; ?>">#<?php echo $os['id']; ?></a></td>
                            <td><?php echo htmlspecialchars($os['cliente_nome'] ?? ''); ?></td>
                            <td><?php echo $linksHtml ?: htmlspecialchars($os['pecas'] ?? '—'); ?></td>
                            <td><?php echo $statusNome; ?></td>
                            <td><?php echo htmlspecialchars($os['status_pagamento'] ?? 'pendente'); ?></td>
                            <td><?php echo $dataEntrada; ?></td>
                            <td>
                                <form method="post" action="<?php echo BASE_URL; ?>ordens/atualizar-obs-compra" class="d-flex gap-1">
                                    <input type="hidden" name="id" value="<?php echo $os['id']; ?>">
                                    <input type="text" name="observacao" class="form-control form-control-sm" placeholder="Atualizar / Verificar" style="width:140px;">
                                    <button class="btn btn-sm btn-outline-primary">Salvar</button>
                                </form>
                            </td>
                            <td>
                                <?php if ($statusId === 11): ?>
                                <form method="post" action="<?php echo BASE_URL; ?>ordens/marcar-peca-comprada" style="display:inline;">
                                    <input type="hidden" name="id" value="<?php echo $os['id']; ?>">
                                    <button class="btn btn-sm btn-success">Peça comprada</button>
                                </form>
                                <?php else: ?>
                                <span class="text-muted">—</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php endif; ?>
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
                            <h3 class="fw-bold mb-0 text-white">R$ <?php echo number_format($stats['faturamento_producao'], 2, ',', '.'); ?></h3>
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
                            <h3 class="fw-bold mb-0 text-white">R$ <?php echo number_format($stats['faturamento_caixa'], 2, ',', '.'); ?></h3>
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
            
            <?php if (Auth::isTecnico() && !$isAdmin): ?>
            <!-- PAINEL EXCLUSIVO PARA TÉCNICOS -->
            
            <!-- SEÇÃO 1: MÉTRICAS DE MANUTENÇÃO (CARDS) -->
            <div class="mb-4">
                <h5 class="fw-bold mb-3"><i class="fas fa-wrench text-primary me-2"></i> Minhas Metas & Pendências</h5>
                <div class="row g-3">
                    <!-- OS Sem Laudo -->
                    <div class="col-md-3">
                        <a href="<?php echo BASE_URL; ?>ordens?sem_laudo=1" class="text-decoration-none">
                            <div class="card h-100 border-0 shadow-sm stat-card-hover <?php echo $tecnicoStats['total_sem_laudo'] > 0 ? 'bg-danger bg-opacity-10 border border-danger' : ''; ?>" style="border-radius: 12px;">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <div class="bg-danger bg-opacity-10 p-2 rounded-circle">
                                            <i class="fas fa-file-signature text-danger"></i>
                                        </div>
                                        <span class="text-danger small fw-bold">Pendente</span>
                                    </div>
                                    <h2 class="fw-bold mb-0 text-danger"><?php echo $tecnicoStats['total_sem_laudo']; ?></h2>
                                    <small class="text-muted">OS Sem Laudo Técnico</small>
                                </div>
                            </div>
                        </a>
                    </div>
                    <!-- OS Sem Atualização (2+ dias) -->
                    <div class="col-md-3">
                        <a href="<?php echo BASE_URL; ?>ordens?sem_atualizacao_dias=2" class="text-decoration-none">
                            <div class="card h-100 border-0 shadow-sm stat-card-hover <?php echo $tecnicoStats['total_sem_atualizacao'] > 0 ? 'bg-warning bg-opacity-10 border border-warning' : ''; ?>" style="border-radius: 12px;">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <div class="bg-warning bg-opacity-10 p-2 rounded-circle">
                                            <i class="fas fa-clock text-warning"></i>
                                        </div>
                                        <span class="text-warning small fw-bold">Atrasadas</span>
                                    </div>
                                    <h2 class="fw-bold mb-0 text-warning"><?php echo $tecnicoStats['total_sem_atualizacao']; ?></h2>
                                    <small class="text-muted">Sem Atualizar (2+ dias)</small>
                                </div>
                            </div>
                        </a>
                    </div>
                    <!-- OS Sem Itens (Prod/Serv) -->
                    <div class="col-md-3">
                        <a href="<?php echo BASE_URL; ?>ordens?sem_itens=1" class="text-decoration-none">
                            <div class="card h-100 border-0 shadow-sm stat-card-hover <?php echo $tecnicoStats['total_sem_itens'] > 0 ? 'bg-info bg-opacity-10 border border-info' : ''; ?>" style="border-radius: 12px;">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <div class="bg-info bg-opacity-10 p-2 rounded-circle">
                                            <i class="fas fa-box-open text-info"></i>
                                        </div>
                                        <span class="text-info small fw-bold">Incompleto</span>
                                    </div>
                                    <h2 class="fw-bold mb-0 text-info"><?php echo $tecnicoStats['total_sem_itens']; ?></h2>
                                    <small class="text-muted">Sem Produto ou Serviço</small>
                                </div>
                            </div>
                        </a>
                    </div>
                    <!-- OS Abertas Gerais -->
                    <div class="col-md-3">
                        <a href="<?php echo BASE_URL; ?>ordens" class="text-decoration-none">
                            <div class="card h-100 border-0 shadow-sm stat-card-hover" style="border-radius: 12px;">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <div class="bg-primary bg-opacity-10 p-2 rounded-circle">
                                            <i class="fas fa-folder-open text-primary"></i>
                                        </div>
                                        <span class="text-muted small fw-bold">Total</span>
                                    </div>
                                    <h2 class="fw-bold mb-0"><?php echo $stats['total_abertas']; ?></h2>
                                    <small class="text-muted">OS Ativas em Aberto</small>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>

            <!-- SEÇÃO 2: CRM & PÓS-VENDA DA SEMANA (CRONOGRAMA DIA-A-DIA) -->
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3"><i class="fas fa-calendar-alt text-success me-2"></i> CRM & Pós-Venda (Contatos Semanais)</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle text-center mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-start">Canal de Contato</th>
                                    <?php foreach ($crmSemanaStats['dias'] as $dia): ?>
                                        <th>
                                            <div><?php echo $dia['dia_nome']; ?></div>
                                            <small class="text-muted"><?php echo $dia['data_formatada']; ?></small>
                                        </th>
                                    <?php endforeach; ?>
                                    <th class="table-dark">Total Geral</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="text-start fw-bold"><i class="fas fa-bullhorn text-primary me-1"></i> Contatos Realizados (CRM)</td>
                                    <?php foreach ($crmSemanaStats['dias'] as $dia): ?>
                                        <td class="<?php echo $dia['crm'] > 0 ? 'fw-bold text-primary bg-primary bg-opacity-10' : 'text-muted'; ?>">
                                            <?php echo $dia['crm']; ?>
                                        </td>
                                    <?php endforeach; ?>
                                    <td class="table-dark fw-bold"><?php echo $crmSemanaStats['total_crm']; ?></td>
                                </tr>
                                <tr>
                                    <td class="text-start fw-bold"><i class="fas fa-handshake text-success me-1"></i> Contatos Pós-Venda (Por Venda)</td>
                                    <?php foreach ($crmSemanaStats['dias'] as $dia): ?>
                                        <td class="<?php echo $dia['pos_venda'] > 0 ? 'fw-bold text-success bg-success bg-opacity-10' : 'text-muted'; ?>">
                                            <?php echo $dia['pos_venda']; ?>
                                        </td>
                                    <?php endforeach; ?>
                                    <td class="table-dark fw-bold"><?php echo $crmSemanaStats['total_pos_venda']; ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3 bg-light p-3 rounded-3">
                        <h6 class="fw-bold mb-1"><i class="fas fa-info-circle text-secondary me-1"></i> Resumo do CRM na Semana</h6>
                        <p class="text-muted small mb-0">
                            Nesta semana, foram efetuados um total de <strong><?php echo $crmSemanaStats['total_crm'] + $crmSemanaStats['total_pos_venda']; ?> interações</strong> de contato com clientes, sendo <strong><?php echo $crmSemanaStats['total_crm']; ?></strong> interações de campanhas/atrativos (CRM) e <strong><?php echo $crmSemanaStats['total_pos_venda']; ?></strong> acompanhamentos pós-vendas das ordens entregues.
                        </p>
                    </div>
                </div>
            </div>

            <!-- SEÇÃO 3: OS PARADAS / ESQUECIDAS (AÇÃO NECESSÁRIA) -->
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold mb-0 text-danger"><i class="fas fa-exclamation-triangle me-2"></i> OS Paradas ou "Esquecidas" <small class="text-muted fw-normal fs-6">(Inativas há mais de 5 dias)</small></h5>
                        <span class="badge bg-danger"><?php echo $tecnicoStats['total_esquecidas']; ?> OS Paradas</span>
                    </div>
                    
                    <?php if (empty($tecnicoStats['esquecidas'])): ?>
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-star-and-crescent text-success fa-2x mb-2"></i>
                            <p class="mb-0">Incrível! Não existem Ordens de Serviço paradas há mais de 5 dias.</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>OS #</th>
                                        <th>Cliente</th>
                                        <th>Status Atual</th>
                                        <th>Última Atualização</th>
                                        <th class="text-end">Ação</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($tecnicoStats['esquecidas'] as $os): 
                                        $diasParados = (new \DateTimeImmutable($os['ultima_atualizacao']))->diff(new \DateTimeImmutable())->days;
                                    ?>
                                        <tr>
                                            <td><strong>#<?php echo $os['id']; ?></strong></td>
                                            <td><?php echo htmlspecialchars($os['cliente_nome'] ?? $os['cliente_name'] ?? ''); ?></td>
                                            <td>
                                                <span class="badge" style="background-color: <?php echo $os['status_cor']; ?>; color: #fff;">
                                                    <?php echo htmlspecialchars($os['status_nome']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="text-danger fw-bold"><?php echo $diasParados; ?> dias sem mexer</div>
                                                <small class="text-muted"><?php echo date('d/m/Y H:i', strtotime($os['ultima_atualizacao'])); ?></small>
                                            </td>
                                            <td class="text-end">
                                                <a href="<?php echo BASE_URL; ?>ordens/view?id=<?php echo $os['id']; ?>" class="btn btn-sm btn-danger px-3" style="border-radius: 8px;">
                                                    <i class="fas fa-wrench me-1"></i> TRATAR AGORA
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <?php else: ?>
            <!-- ORIGINAL EXECUTIVE / ADMIN DASHBOARD -->
            
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
            
            <?php endif; ?>
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

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    window.dashboardAlerts = <?php echo json_encode($alertas ?? []); ?>;
    window.trendData = <?php echo json_encode($stats['trend'] ?? []); ?>;

    document.addEventListener('DOMContentLoaded', function() {
        const canvas = document.getElementById('trendChart');
        if (!canvas) return;

        const ctx = canvas.getContext('2d');
        const css = getComputedStyle(document.documentElement);
        const tickColor = (css.getPropertyValue('--text-muted') || '').trim() || '#94a3b8';
        const gridColor = (css.getPropertyValue('--border-color') || '').trim() || '#e2e8f0';
        const tooltipBg = (css.getPropertyValue('--bg-tertiary') || '').trim() || '#1e293b';

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
                        backgroundColor: tooltipBg,
                        titleFont: { size: 14, weight: 'bold' },
                        padding: 12,
                        cornerRadius: 8
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1, color: tickColor },
                        grid: { borderDash: [5, 5], color: gridColor }
                    },
                    x: {
                        ticks: { color: tickColor },
                        grid: { display: false }
                    }
                }
            }
        });
    });
</script>
<script src="<?php echo BASE_URL; ?>assets/js/dashboard.js"></script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
