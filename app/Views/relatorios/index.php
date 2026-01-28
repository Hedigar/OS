<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h3 mb-0" style="color: var(--text-primary, #fff);"><i class="fas fa-chart-bar me-2"></i>Relatórios e Resumos</h2>
    </div>

    <div class="card shadow mb-4" style="background-color: var(--bg-secondary); border-color: var(--border-color);">
        <div class="card-body">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label" style="color: var(--text-primary);">Data Início</label>
                    <input type="date" name="data_inicio" class="form-control" value="<?php echo $filtros['data_inicio']; ?>" style="background-color: var(--bg-tertiary); color: var(--text-primary); border: 1px solid var(--border-color);">
                </div>
                <div class="col-md-4">
                    <label class="form-label" style="color: var(--text-primary);">Data Fim</label>
                    <input type="date" name="data_fim" class="form-control" value="<?php echo $filtros['data_fim']; ?>" style="background-color: var(--bg-tertiary); color: var(--text-primary); border: 1px solid var(--border-color);">
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-filter me-2"></i>Filtrar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="row">
        <div class="col-12 mb-4">
            <div class="card shadow" style="background-color: var(--bg-secondary); border-color: var(--border-color); border-left: 5px solid #1cc88a;">
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
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Custo Total de Peças</div>
                            <div class="h4 mb-0 font-weight-bold text-danger">
                                R$ <?php echo number_format($lucroReal['custo_pecas'] ?? 0, 2, ',', '.'); ?>
                            </div>
                            <small class="text-muted">Produtos usados em OS/Atendimentos</small>
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
        </div>
    </div>

    <div class="row">
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4" style="background-color: var(--bg-secondary); border-color: var(--border-color);">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between" style="background-color: rgba(0,0,0,0.1); border-bottom: 1px solid var(--border-color);">
                    <h6 class="m-0 font-weight-bold text-primary">Resumo Financeiro (OS Finalizadas)</h6>
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
                        <div class="text-sm font-weight-bold text-uppercase mb-1" style="color: var(--text-primary);">Lucro Estimado</div>
                        <div class="h3 mb-0 font-weight-bold <?php echo $corLucro; ?>">R$ <?php echo number_format($lucro, 2, ',', '.'); ?></div>
                    </div>
                </div>
            </div>

            <div class="card shadow mb-4" style="background-color: var(--bg-secondary); border-color: var(--border-color);">
                <div class="card-header py-3" style="background-color: rgba(0,0,0,0.1); border-bottom: 1px solid var(--border-color);">
                    <h6 class="m-0 font-weight-bold text-primary">Atendimentos Externos</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 text-center">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Visitas</div>
                            <div class="h5 mb-0 font-weight-bold" style="color: var(--text-primary, #fff);"><?php echo $atendimentos['total'] ?? 0; ?></div>
                        </div>
                        <div class="col-md-3 text-center">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Valor Total</div>
                            <div class="h5 mb-0 font-weight-bold" style="color: var(--text-primary, #fff);">R$ <?php echo number_format($atendimentos['valor_total'] ?? 0, 2, ',', '.'); ?></div>
                        </div>
                        <div class="col-md-3 text-center">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Deslocamentos</div>
                            <div class="h5 mb-0 font-weight-bold" style="color: var(--text-primary, #fff);">R$ <?php echo number_format($atendimentos['valor_deslocamento'] ?? 0, 2, ',', '.'); ?></div>
                        </div>
                        <div class="col-md-3 text-center">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Lucro Estimado</div>
                            <div class="h5 mb-0 font-weight-bold" style="color: var(--text-primary, #fff);">R$ <?php echo number_format($atendimentos['lucro_total'] ?? 0, 2, ',', '.'); ?></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow mb-4" style="background-color: var(--bg-secondary); border-color: var(--border-color);">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between" style="background-color: rgba(0,0,0,0.1); border-bottom: 1px solid var(--border-color);">
                    <h6 class="m-0 font-weight-bold text-primary">Origem de Custos (por OS)</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover" style="color: var(--text-primary);">
                            <thead>
                                <tr style="border-bottom: 1px solid var(--border-color);">
                                    <th style="color: var(--text-primary);">OS</th>
                                    <th style="color: var(--text-primary);">Cliente</th>
                                    <th class="text-end" style="color: var(--text-primary);">Custo Total</th>
                                    <th class="text-center" style="color: var(--text-primary);">Detalhes</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($custosPorOS ?? [])): ?>
                                    <tr><td colspan="4">Sem custos registrados no período.</td></tr>
                                <?php else: ?>
                                    <?php foreach ($custosPorOS as $row): ?>
                                        <tr>
                                            <td><a href="<?php echo BASE_URL; ?>ordens/view?id=<?php echo (int)$row['os_id']; ?>">#<?php echo (int)$row['os_id']; ?></a></td>
                                            <td><?php echo htmlspecialchars($row['cliente_nome'] ?? ''); ?></td>
                                            <td class="text-end"><strong>R$ <?php echo number_format((float)($row['custo_total'] ?? 0), 2, ',', '.'); ?></strong></td>
                                            <td class="text-center">
                                                <button class="btn btn-sm btn-outline-primary" type="button" onclick="toggleDetalhes('os-<?php echo (int)$row['os_id']; ?>')">Ver</button>
                                            </td>
                                        </tr>
                                        <tr id="os-<?php echo (int)$row['os_id']; ?>" style="display:none;">
                                            <td colspan="4">
                                                <div class="table-responsive">
                                                    <table class="table table-sm" style="color: var(--text-primary);">
                                                        <thead>
                                                            <tr>
                                                                <th>Item</th>
                                                                <th>Tipo</th>
                                                                <th class="text-center">Qtd</th>
                                                                <th class="text-end">Custo Unit.</th>
                                                                <th class="text-end">Subtotal</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php foreach (($row['itens'] ?? []) as $it): ?>
                                                                <tr>
                                                                    <td><?php echo htmlspecialchars($it['descricao'] ?? ''); ?></td>
                                                                    <td><span class="badge bg-secondary"><?php echo htmlspecialchars($it['tipo_item'] ?? ''); ?></span></td>
                                                                    <td class="text-center"><?php echo number_format((float)($it['quantidade'] ?? 0), 2, ',', '.'); ?></td>
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
        </div>

        <div class="col-xl-4 col-lg-5">
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
<script>
function toggleDetalhes(id){
    var el = document.getElementById(id);
    if (!el) return;
    el.style.display = (el.style.display === 'none' || el.style.display === '') ? 'table-row' : 'none';
}
</script>
