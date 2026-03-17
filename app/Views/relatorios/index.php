<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h3 mb-0" style="color: var(--text-primary, #fff);"><i class="fas fa-chart-bar me-2"></i>Relatórios e Resumos</h2>
    </div>

    <!-- Filtros -->
    <div class="card shadow mb-4" style="background-color: var(--bg-secondary); border-color: var(--border-color);">
        <div class="card-body">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label" style="color: var(--text-primary);">Data Início</label>
                    <input type="date" name="data_inicio" class="form-control" value="<?php echo $filtros['data_inicio'] ?? ''; ?>" style="background-color: var(--bg-tertiary); color: var(--text-primary); border: 1px solid var(--border-color);">
                </div>
                <div class="col-md-4">
                    <label class="form-label" style="color: var(--text-primary);">Data Fim</label>
                    <input type="date" name="data_fim" class="form-control" value="<?php echo $filtros['data_fim'] ?? ''; ?>" style="background-color: var(--bg-tertiary); color: var(--text-primary); border: 1px solid var(--border-color);">
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-filter me-2"></i>Filtrar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Navegação por Abas -->
    <ul class="nav nav-pills mb-4" id="pills-tab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="pills-geral-tab" data-bs-toggle="pill" data-bs-target="#pills-geral" type="button" role="tab" aria-controls="pills-geral" aria-selected="true"><i class="fas fa-tachometer-alt me-2"></i>Resumo Geral</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="pills-financeiro-tab" data-bs-toggle="pill" data-bs-target="#pills-financeiro" type="button" role="tab" aria-controls="pills-financeiro" aria-selected="false"><i class="fas fa-dollar-sign me-2"></i>Financeiro Detalhado</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="pills-clientes-tab" data-bs-toggle="pill" data-bs-target="#pills-clientes" type="button" role="tab" aria-controls="pills-clientes" aria-selected="false"><i class="fas fa-users me-2"></i>Clientes e Recorrência</button>
        </li>
    </ul>

    <div class="tab-content" id="pills-tabContent">
        <!-- Aba 1: Resumo Geral -->
        <div class="tab-pane fade show active" id="pills-geral" role="tabpanel" aria-labelledby="pills-geral-tab">
            <div class="row">
                <div class="col-lg-8">
                    <!-- Resumo Financeiro (Competência) -->
                    <div class="card shadow mb-4" style="background-color: var(--bg-secondary); border-color: var(--border-color);">
                        <div class="card-header py-3" style="background-color: rgba(0,0,0,0.1); border-bottom: 1px solid var(--border-color);">
                            <h6 class="m-0 font-weight-bold text-primary">Resumo de OS Criadas no Período</h6>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-md-3 mb-3">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Bruto</div>
                                    <div class="h5 mb-0 font-weight-bold" style="color: var(--text-primary, #fff);">R$ <?php echo number_format($financeiro['total_bruto'] ?? 0, 2, ',', '.'); ?></div>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Serviços</div>
                                    <div class="h5 mb-0 font-weight-bold" style="color: var(--text-primary, #fff);">R$ <?php echo number_format($financeiro['total_servicos'] ?? 0, 2, ',', '.'); ?></div>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Produtos</div>
                                    <div class="h5 mb-0 font-weight-bold" style="color: var(--text-primary, #fff);">R$ <?php echo number_format($financeiro['total_produtos'] ?? 0, 2, ',', '.'); ?></div>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Custo Peças</div>
                                    <div class="h5 mb-0 font-weight-bold" style="color: var(--text-primary, #fff);">R$ <?php echo number_format($financeiro['total_custo'] ?? 0, 2, ',', '.'); ?></div>
                                </div>
                            </div>
                            <hr style="border-top: 1px solid var(--border-color);">
                            <div class="text-center">
                                <?php 
                                    $lucro = ($financeiro['total_bruto'] ?? 0) - ($financeiro['total_custo'] ?? 0);
                                    $corLucro = $lucro >= 0 ? 'text-success' : 'text-danger';
                                ?>
                                <div class="text-sm font-weight-bold text-uppercase mb-1" style="color: var(--text-primary);">Lucro Estimado (Competência)</div>
                                <div class="h3 mb-0 font-weight-bold <?php echo $corLucro; ?>">R$ <?php echo number_format($lucro, 2, ',', '.'); ?></div>
                            </div>
                        </div>
                    </div>

                    <!-- Atendimentos Externos -->
                    <div class="card shadow mb-4" style="background-color: var(--bg-secondary); border-color: var(--border-color);">
                        <div class="card-header py-3" style="background-color: rgba(0,0,0,0.1); border-bottom: 1px solid var(--border-color);">
                            <h6 class="m-0 font-weight-bold text-primary">Atendimentos Externos no Período</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col text-center">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Visitas</div>
                                    <div class="h5 mb-0 font-weight-bold" style="color: var(--text-primary, #fff);"><?php echo $atendimentos['total'] ?? 0; ?></div>
                                </div>
                                <div class="col text-center">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Valor Total</div>
                                    <div class="h5 mb-0 font-weight-bold" style="color: var(--text-primary, #fff);">R$ <?php echo number_format($atendimentos['valor_total'] ?? 0, 2, ',', '.'); ?></div>
                                </div>
                                <div class="col text-center">
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Deslocamento</div>
                                    <div class="h5 mb-0 font-weight-bold" style="color: var(--text-primary, #fff);">R$ <?php echo number_format($atendimentos['valor_deslocamento'] ?? 0, 2, ',', '.'); ?></div>
                                </div>
                                <div class="col text-center">
                                    <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Custo Peças</div>
                                    <div class="h5 mb-0 font-weight-bold" style="color: var(--text-primary, #fff);">R$ <?php echo number_format($atendimentos['custo_total'] ?? 0, 2, ',', '.'); ?></div>
                                </div>
                                <div class="col text-center">
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Lucro Estimado</div>
                                    <div class="h5 mb-0 font-weight-bold" style="color: var(--text-primary, #fff);">R$ <?php echo number_format($atendimentos['lucro_total'] ?? 0, 2, ',', '.'); ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <!-- OS por Status -->
                    <div class="card shadow mb-4" style="background-color: var(--bg-secondary); border-color: var(--border-color);">
                        <div class="card-header py-3" style="background-color: rgba(0,0,0,0.1); border-bottom: 1px solid var(--border-color);">
                            <h6 class="m-0 font-weight-bold text-primary">OS por Status (No Período)</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-sm table-hover" style="color: var(--text-primary);">
                                    <thead>
                                        <tr style="border-bottom: 1px solid var(--border-color);">
                                            <th style="color: var(--text-primary);">Status</th>
                                            <th class="text-center" style="color: var(--text-primary);">Qtd</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($statusResumo as $status): ?>
                                        <tr style="border-bottom: 1px solid var(--border-color);">
                                            <td>
                                                <span class="badge" style="background-color: <?php echo $status['cor']; ?>; color: #fff;">
                                                    <?php echo $status['nome']; ?>
                                                </span>
                                            </td>
                                            <td class="text-center font-weight-bold" style="color: var(--text-primary);"><?php echo $status['total']; ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Aba 2: Financeiro Detalhado -->
        <div class="tab-pane fade" id="pills-financeiro" role="tabpanel" aria-labelledby="pills-financeiro-tab">
            <div class="row">
                <div class="col-12">
                    <!-- Resultado Líquido (Caixa Real) -->
                    <div class="card shadow mb-4" style="background-color: var(--bg-secondary); border-color: var(--border-color); border-left: 5px solid #1cc88a;">
                        <div class="card-header py-3" style="background-color: rgba(0,0,0,0.1); border-bottom: 1px solid var(--border-color);">
                            <h6 class="m-0 font-weight-bold text-success"><i class="fas fa-coins me-2"></i>Resultado Líquido (Caixa Real)</h6>
                        </div>
                        <div class="card-body">
                            <div class="row text-center align-items-center">
                                <div class="col-md-4 mb-2 mb-md-0">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Entradas Líquidas (Pós-Taxas)</div>
                                    <div class="h4 mb-0 font-weight-bold" style="color: var(--text-primary, #fff);">
                                        R$ <?php echo number_format($lucroReal['receita_liquida'] ?? 0, 2, ',', '.'); ?>
                                    </div>
                                    <small class="text-muted">Total recebido em caixa/conta</small>
                                </div>
                                <div class="col-md-4 mb-2 mb-md-0 position-relative">
                                    <div class="d-none d-md-block position-absolute" style="left: 0; top: 50%; transform: translateY(-50%); font-size: 2rem; color: var(--text-muted); opacity: 0.3;">-</div>
                                    <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Custo Total (OS + Atend.)</div>
                                    <div class="h4 mb-0 font-weight-bold text-danger">
                                        R$ <?php echo number_format($lucroReal['custo_pecas'] ?? 0, 2, ',', '.'); ?>
                                    </div>
                                    <div class="mt-1">
                                        <small class="text-muted d-block" style="font-size: 0.7rem;">OS: R$ <?php echo number_format($lucroReal['custo_os'] ?? 0, 2, ',', '.'); ?></small>
                                        <small class="text-muted d-block" style="font-size: 0.7rem;">Atend.: R$ <?php echo number_format($lucroReal['custo_atendimentos'] ?? 0, 2, ',', '.'); ?></small>
                                    </div>
                                    <div class="d-none d-md-block position-absolute" style="right: 0; top: 50%; transform: translateY(-50%); font-size: 2rem; color: var(--text-muted); opacity: 0.3;">=</div>
                                </div>
                                <div class="col-md-4">
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1" style="font-size: 0.9rem;">Lucro Disponível</div>
                                    <div class="h2 mb-0 font-weight-bold text-success">
                                        R$ <?php echo number_format($lucroReal['lucro_real'] ?? 0, 2, ',', '.'); ?>
                                    </div>
                                    <small class="text-success font-weight-bold">Valor final para divisão</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Auditoria do Caixa Real -->
                    <div class="card shadow mb-4" style="background-color: var(--bg-secondary); border-color: var(--border-color);">
                        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between" style="background-color: rgba(0,0,0,0.1); border-bottom: 1px solid var(--border-color);">
                            <h6 class="m-0 font-weight-bold text-success"><i class="fas fa-search-dollar me-2"></i>Auditoria do Caixa Real (O que compõe os custos?)</h6>
                        </div>
                        <div class="card-body">
                            <ul class="nav nav-tabs mb-3" id="auditTabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="os-caixa-tab" data-bs-toggle="tab" data-bs-target="#os-caixa" type="button" role="tab">Custos OS (Caixa)</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="at-caixa-tab" data-bs-toggle="tab" data-bs-target="#at-caixa" type="button" role="tab">Custos Atendimentos (Caixa)</button>
                                </li>
                            </ul>
                            <div class="tab-content" id="auditTabsContent">
                                <div class="tab-pane fade show active" id="os-caixa" role="tabpanel">
                                    <div class="table-responsive">
                                        <table class="table table-sm table-hover" style="color: var(--text-primary);">
                                            <thead>
                                                <tr>
                                                    <th>OS</th>
                                                    <th>Cliente</th>
                                                    <th class="text-end">Custo Total</th>
                                                    <th class="text-center">Detalhes</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (empty($custosOSCaixa)): ?>
                                                    <tr><td colspan="4" class="text-center text-muted">Nenhum custo de OS neste período de caixa.</td></tr>
                                                <?php else: ?>
                                                    <?php foreach ($custosOSCaixa as $row): ?>
                                                        <tr>
                                                            <td>#<?php echo (int)$row['os_id']; ?></td>
                                                            <td><?php echo htmlspecialchars($row['cliente_nome']); ?></td>
                                                            <td class="text-end">R$ <?php echo number_format($row['custo_total'], 2, ',', '.'); ?></td>
                                                            <td class="text-center">
                                                                <button class="btn btn-sm btn-outline-info" type="button" onclick="toggleDetalhes('audit-os-<?php echo $row['os_id']; ?>')">Ver</button>
                                                            </td>
                                                        </tr>
                                                        <tr id="audit-os-<?php echo $row['os_id']; ?>" style="display:none;">
                                                            <td colspan="4">
                                                                <table class="table table-sm text-muted mb-0">
                                                                    <?php foreach ($row['itens'] as $it): ?>
                                                                        <tr>
                                                                            <td><?php echo htmlspecialchars($it['descricao']); ?></td>
                                                                            <td class="text-end"><?php echo (float)$it['quantidade']; ?>x R$ <?php echo number_format($it['valor_custo'], 2, ',', '.'); ?></td>
                                                                            <td class="text-end">R$ <?php echo number_format($it['quantidade'] * $it['valor_custo'], 2, ',', '.'); ?></td>
                                                                        </tr>
                                                                    <?php endforeach; ?>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="at-caixa" role="tabpanel">
                                    <div class="table-responsive">
                                        <table class="table table-sm table-hover" style="color: var(--text-primary);">
                                            <thead>
                                                <tr>
                                                    <th>Atend.</th>
                                                    <th>Cliente</th>
                                                    <th class="text-end">Custo Total</th>
                                                    <th class="text-center">Detalhes</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (empty($custosAtendimentoCaixa)): ?>
                                                    <tr><td colspan="4" class="text-center text-muted">Nenhum custo de atendimento neste período de caixa.</td></tr>
                                                <?php else: ?>
                                                    <?php foreach ($custosAtendimentoCaixa as $row): ?>
                                                        <tr>
                                                            <td>#<?php echo (int)$row['atendimento_id']; ?></td>
                                                            <td><?php echo htmlspecialchars($row['cliente_nome']); ?></td>
                                                            <td class="text-end">R$ <?php echo number_format($row['custo_total'], 2, ',', '.'); ?></td>
                                                            <td class="text-center">
                                                                <button class="btn btn-sm btn-outline-info" type="button" onclick="toggleDetalhes('audit-at-<?php echo $row['atendimento_id']; ?>')">Ver</button>
                                                            </td>
                                                        </tr>
                                                        <tr id="audit-at-<?php echo $row['atendimento_id']; ?>" style="display:none;">
                                                            <td colspan="4">
                                                                <table class="table table-sm text-muted mb-0">
                                                                    <?php foreach ($row['itens'] as $it): ?>
                                                                        <tr>
                                                                            <td><?php echo htmlspecialchars($it['descricao']); ?></td>
                                                                            <td class="text-end"><?php echo (float)$it['quantidade']; ?>x R$ <?php echo number_format($it['valor_custo'], 2, ',', '.'); ?></td>
                                                                            <td class="text-end">R$ <?php echo number_format($it['quantidade'] * $it['valor_custo'], 2, ',', '.'); ?></td>
                                                                        </tr>
                                                                    <?php endforeach; ?>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Origem de Custos -->
                    <div class="card shadow mb-4" style="background-color: var(--bg-secondary); border-color: var(--border-color);">
                        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between" style="background-color: rgba(0,0,0,0.1); border-bottom: 1px solid var(--border-color);">
                            <h6 class="m-0 font-weight-bold text-primary">Origem de Custos (Criados no Período)</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-sm table-hover" style="color: var(--text-primary);">
                                    <thead>
                                        <tr style="border-bottom: 1px solid var(--border-color);">
                                            <th style="color: var(--text-primary);">Origem</th>
                                            <th style="color: var(--text-primary);">Cliente</th>
                                            <th class="text-end" style="color: var(--text-primary);">Custo Total</th>
                                            <th class="text-center" style="color: var(--text-primary);">Detalhes</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                            $todosCustos = array_merge(
                                                array_map(fn($v) => array_merge($v, ['tipo' => 'OS']), $custosPorOS),
                                                array_map(fn($v) => array_merge($v, ['tipo' => 'Atend.']), $custosPorAtendimento)
                                            );
                                            usort($todosCustos, fn($a, $b) => ($b['os_id'] ?? $b['atendimento_id']) <=> ($a['os_id'] ?? $a['atendimento_id']));
                                        ?>
                                        <?php if (empty($todosCustos)): ?>
                                            <tr><td colspan="4">Sem custos registrados no período.</td></tr>
                                        <?php else: ?>
                                            <?php foreach ($todosCustos as $row): ?>
                                                <?php $id = $row['os_id'] ?? $row['atendimento_id']; ?>
                                                <?php $prefix = isset($row['os_id']) ? 'os' : 'at'; ?>
                                                <tr>
                                                    <td>
                                                        <span class="badge bg-<?php echo $prefix === 'os' ? 'primary' : 'info'; ?>">
                                                            <?php echo $row['tipo']; ?> #<?php echo (int)$id; ?>
                                                        </span>
                                                    </td>
                                                    <td><?php echo htmlspecialchars($row['cliente_nome'] ?? ''); ?></td>
                                                    <td class="text-end"><strong>R$ <?php echo number_format((float)($row['custo_total'] ?? 0), 2, ',', '.'); ?></strong></td>
                                                    <td class="text-center">
                                                        <button class="btn btn-sm btn-outline-primary" type="button" onclick="toggleDetalhes('raw-<?php echo $prefix; ?>-<?php echo (int)$id; ?>')">Ver</button>
                                                    </td>
                                                </tr>
                                                <tr id="raw-<?php echo $prefix; ?>-<?php echo (int)$id; ?>" style="display:none;">
                                                    <td colspan="4">
                                                        <div class="table-responsive">
                                                            <table class="table table-sm" style="color: var(--text-primary);">
                                                                <tbody>
                                                                    <?php foreach (($row['itens'] ?? []) as $it): ?>
                                                                        <tr>
                                                                            <td><?php echo htmlspecialchars($it['descricao'] ?? ''); ?></td>
                                                                            <td class="text-center"><?php echo number_format((float)($it['quantidade'] ?? 0), 2, ',', '.'); ?>x</td>
                                                                            <td class="text-end">R$ <?php echo number_format((float)($it['valor_custo'] ?? 0), 2, ',', '.'); ?></td>
                                                                            <td class="text-end">R$ <?php echo number_format(((float)($it['quantidade'] ?? 0) * (float)($it['valor_custo'] ?? 0)), 2, ',', '.'); ?></td>
                                                                        </tr>
                                                                    <?php endforeach; ?>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Itens Vendidos -->
                    <div class="card shadow mb-4" style="background-color: var(--bg-secondary); border-color: var(--border-color);">
                        <div class="card-header py-3" style="background-color: rgba(0,0,0,0.1); border-bottom: 1px solid var(--border-color);">
                            <h6 class="m-0 font-weight-bold text-primary">Produtos e Serviços Vendidos (Competência)</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-sm table-hover" style="color: var(--text-primary);">
                                    <thead>
                                        <tr style="border-bottom: 1px solid var(--border-color);">
                                            <th>Tipo</th>
                                            <th>Descrição</th>
                                            <th class="text-center">Quantidade</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($itensVendidos ?? [])): ?>
                                            <tr><td colspan="3">Nenhum item vendido no período.</td></tr>
                                        <?php else: ?>
                                            <?php foreach ($itensVendidos as $item): ?>
                                                <tr>
                                                    <td>
                                                        <span class="badge bg-<?php echo $item['tipo_item'] === 'produto' ? 'info' : 'secondary'; ?>">
                                                            <?php echo htmlspecialchars($item['tipo_item']); ?>
                                                        </span>
                                                    </td>
                                                    <td><?php echo htmlspecialchars($item['descricao']); ?></td>
                                                    <td class="text-center">
                                                        <?php echo number_format((float)$item['quantidade_total'], 2, ',', '.'); ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Aba 3: Clientes e Recorrência -->
        <div class="tab-pane fade" id="pills-clientes" role="tabpanel" aria-labelledby="pills-clientes-tab">
            <div class="row">
                <div class="col-lg-6 mb-4">
                    <div class="card shadow h-100" style="background-color: var(--bg-secondary); border-color: var(--border-color);">
                        <div class="card-header py-3" style="background-color: rgba(0,0,0,0.1); border-bottom: 1px solid var(--border-color);">
                            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-user-plus me-2"></i>Clientes Novos (<?php echo (int)($clientesResumo['novos'] ?? 0); ?>)</h6>
                        </div>
                        <div class="card-body">
                            <?php if (empty($novosClientes)): ?>
                                <p class="text-center text-muted my-4">Nenhum cliente novo neste período.</p>
                            <?php else: ?>
                                <div class="accordion" id="accordionNovos">
                                    <?php foreach ($novosClientes as $index => $cliente): ?>
                                        <div class="accordion-item" style="background-color: transparent; border-color: var(--border-color);">
                                            <h2 class="accordion-header" id="headingNov<?php echo $index; ?>">
                                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseNov<?php echo $index; ?>" aria-expanded="false" aria-controls="collapseNov<?php echo $index; ?>" style="background-color: var(--bg-tertiary); color: var(--text-primary);">
                                                    <div class="d-flex justify-content-between w-100 me-3">
                                                        <span><?php echo htmlspecialchars($cliente['nome_completo']); ?></span>
                                                        <span class="badge bg-primary rounded-pill"><?php echo count($cliente['ordens']); ?> OS</span>
                                                    </div>
                                                </button>
                                            </h2>
                                            <div id="collapseNov<?php echo $index; ?>" class="accordion-collapse collapse" aria-labelledby="headingNov<?php echo $index; ?>" data-bs-parent="#accordionNovos">
                                                <div class="accordion-body" style="color: var(--text-primary);">
                                                    <div class="list-group list-group-flush">
                                                        <?php if (empty($cliente['ordens'])): ?>
                                                            <p class="small text-muted mb-0">Sem OS registradas no período.</p>
                                                        <?php else: ?>
                                                            <?php foreach ($cliente['ordens'] as $os): ?>
                                                                <a href="<?php echo BASE_URL; ?>ordens/view?id=<?php echo $os['id']; ?>" class="list-group-item list-group-item-action" style="background-color: transparent; color: var(--text-primary); border-color: var(--border-color);" target="_blank">
                                                                    <div class="d-flex w-100 justify-content-between">
                                                                        <h6 class="mb-1">OS #<?php echo $os['id']; ?></h6>
                                                                        <small><?php echo date('d/m/Y', strtotime($os['data'])); ?></small>
                                                                    </div>
                                                                    <p class="mb-1 small text-muted"><?php echo htmlspecialchars($os['defeito'] ?? 'Sem descrição'); ?></p>
                                                                </a>
                                                            <?php endforeach; ?>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div class="text-end mt-2">
                                                        <a href="<?php echo BASE_URL; ?>clientes/view?id=<?php echo $cliente['id']; ?>" class="btn btn-sm btn-primary" target="_blank">Ver Cliente</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 mb-4">
                    <div class="card shadow h-100" style="background-color: var(--bg-secondary); border-color: var(--border-color);">
                        <div class="card-header py-3" style="background-color: rgba(0,0,0,0.1); border-bottom: 1px solid var(--border-color);">
                            <h6 class="m-0 font-weight-bold text-success"><i class="fas fa-sync-alt me-2"></i>Clientes que Voltaram (<?php echo (int)($clientesResumo['clientes_que_voltaram'] ?? 0); ?>)</h6>
                        </div>
                        <div class="card-body">
                            <?php if (empty($clientesQueVoltaram)): ?>
                                <p class="text-center text-muted my-4">Nenhum cliente recorrente neste período.</p>
                            <?php else: ?>
                                <div class="accordion" id="accordionRecorrencia">
                                    <?php foreach ($clientesQueVoltaram as $index => $cliente): ?>
                                        <div class="accordion-item" style="background-color: transparent; border-color: var(--border-color);">
                                            <h2 class="accordion-header" id="headingRec<?php echo $index; ?>">
                                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseRec<?php echo $index; ?>" aria-expanded="false" aria-controls="collapseRec<?php echo $index; ?>" style="background-color: var(--bg-tertiary); color: var(--text-primary);">
                                                    <div class="d-flex justify-content-between w-100 me-3">
                                                        <span><?php echo htmlspecialchars($cliente['nome_completo']); ?></span>
                                                        <span class="badge bg-success rounded-pill"><?php echo count($cliente['ordens']); ?> OS</span>
                                                    </div>
                                                </button>
                                            </h2>
                                            <div id="collapseRec<?php echo $index; ?>" class="accordion-collapse collapse" aria-labelledby="headingRec<?php echo $index; ?>" data-bs-parent="#accordionRecorrencia">
                                                <div class="accordion-body" style="color: var(--text-primary);">
                                                    <div class="list-group list-group-flush">
                                                        <?php foreach ($cliente['ordens'] as $os): ?>
                                                            <a href="<?php echo BASE_URL; ?>ordens/view?id=<?php echo $os['id']; ?>" class="list-group-item list-group-item-action" style="background-color: transparent; color: var(--text-primary); border-color: var(--border-color);" target="_blank">
                                                                <div class="d-flex w-100 justify-content-between">
                                                                    <h6 class="mb-1">OS #<?php echo $os['id']; ?></h6>
                                                                    <small><?php echo date('d/m/Y', strtotime($os['data'])); ?></small>
                                                                </div>
                                                                <p class="mb-1 small text-muted"><?php echo htmlspecialchars($os['defeito'] ?? 'Sem descrição'); ?></p>
                                                            </a>
                                                        <?php endforeach; ?>
                                                    </div>
                                                    <div class="text-end mt-2">
                                                        <a href="<?php echo BASE_URL; ?>clientes/view?id=<?php echo $cliente['id']; ?>" class="btn btn-sm btn-primary" target="_blank">Ver Cliente</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function toggleDetalhes(id){
    var el = document.getElementById(id);
    if (!el) return;
    el.style.display = (el.style.display === 'none' || el.style.display === '') ? 'table-row' : 'none';
}
</script>