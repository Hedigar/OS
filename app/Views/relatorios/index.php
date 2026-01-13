<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h3 mb-0 text-gray-800"><i class="fas fa-chart-bar me-2"></i>Relatórios e Resumos</h2>
    </div>

    <!-- Filtros -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">Data Início</label>
                    <input type="date" name="data_inicio" class="form-control" value="<?php echo $filtros['data_inicio']; ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Data Fim</label>
                    <input type="date" name="data_fim" class="form-control" value="<?php echo $filtros['data_fim']; ?>">
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
        <!-- Resumo Financeiro -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Resumo Financeiro (OS Finalizadas)</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-3 mb-3">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Bruto</div>
                            <div class="h5 mb-0 font-weight-bold">R$ <?php echo number_format($financeiro['total_bruto'] ?? 0, 2, ',', '.'); ?></div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Serviços</div>
                            <div class="h5 mb-0 font-weight-bold">R$ <?php echo number_format($financeiro['total_servicos'] ?? 0, 2, ',', '.'); ?></div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Produtos</div>
                            <div class="h5 mb-0 font-weight-bold">R$ <?php echo number_format($financeiro['total_produtos'] ?? 0, 2, ',', '.'); ?></div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Custo Peças</div>
                            <div class="h5 mb-0 font-weight-bold">R$ <?php echo number_format($financeiro['total_custo'] ?? 0, 2, ',', '.'); ?></div>
                        </div>
                    </div>
                    <hr>
                    <div class="text-center">
                        <?php 
                            $lucro = ($financeiro['total_bruto'] ?? 0) - ($financeiro['total_custo'] ?? 0);
                            $corLucro = $lucro >= 0 ? 'text-success' : 'text-danger';
                        ?>
                        <div class="text-sm font-weight-bold text-uppercase mb-1">Lucro Estimado</div>
                        <div class="h3 mb-0 font-weight-bold <?php echo $corLucro; ?>">R$ <?php echo number_format($lucro, 2, ',', '.'); ?></div>
                    </div>
                </div>
            </div>

            <!-- Atendimento Externo -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Atendimentos Externos</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 text-center">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Visitas</div>
                            <div class="h5 mb-0 font-weight-bold"><?php echo $atendimentos['total'] ?? 0; ?></div>
                        </div>
                        <div class="col-md-4 text-center">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Valor Total</div>
                            <div class="h5 mb-0 font-weight-bold">R$ <?php echo number_format($atendimentos['valor_total'] ?? 0, 2, ',', '.'); ?></div>
                        </div>
                        <div class="col-md-4 text-center">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Deslocamentos</div>
                            <div class="h5 mb-0 font-weight-bold">R$ <?php echo number_format($atendimentos['valor_deslocamento'] ?? 0, 2, ',', '.'); ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- OS por Status -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">OS por Status (No Período)</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Status</th>
                                    <th class="text-center">Qtd</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($statusResumo as $status): ?>
                                <tr>
                                    <td>
                                        <span class="badge" style="background-color: <?php echo $status['cor']; ?>;">
                                            <?php echo $status['nome']; ?>
                                        </span>
                                    </td>
                                    <td class="text-center font-weight-bold"><?php echo $status['total']; ?></td>
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
