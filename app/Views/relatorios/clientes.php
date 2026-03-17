<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h3 mb-0" style="color: var(--text-primary, #fff);"><i class="fas fa-users me-2"></i>Relatório de Clientes</h2>
        <a href="<?php echo BASE_URL; ?>relatorios" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Voltar
        </a>
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
        <div class="col-lg-6 mb-4">
            <div class="card shadow h-100" style="background-color: var(--bg-secondary); border-color: var(--border-color);">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between" style="background-color: rgba(0,0,0,0.1); border-bottom: 1px solid var(--border-color);">
                    <h6 class="m-0 font-weight-bold text-primary">Clientes Novos (<?php echo count($novosClientes); ?>)</h6>
                </div>
                <div class="card-body">
                    <?php if (empty($novosClientes)): ?>
                        <p class="text-center text-muted my-4">Nenhum cliente novo neste período.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-sm table-hover" style="color: var(--text-primary);">
                                <thead>
                                    <tr>
                                        <th>Nome</th>
                                        <th class="text-end">Data Cadastro</th>
                                        <th class="text-center">Ação</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($novosClientes as $cliente): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($cliente['nome_completo']); ?></td>
                                            <td class="text-end"><?php echo date('d/m/Y', strtotime($cliente['created_at'])); ?></td>
                                            <td class="text-center">
                                                <a href="<?php echo BASE_URL; ?>clientes/view?id=<?php echo $cliente['id']; ?>" class="btn btn-sm btn-outline-primary" target="_blank">
                                                    <i class="fas fa-eye"></i>
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
        </div>

        <div class="col-lg-6 mb-4">
            <div class="card shadow h-100" style="background-color: var(--bg-secondary); border-color: var(--border-color);">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between" style="background-color: rgba(0,0,0,0.1); border-bottom: 1px solid var(--border-color);">
                    <h6 class="m-0 font-weight-bold text-success">Clientes que Voltaram (<?php echo count($clientesQueVoltaram); ?>)</h6>
                </div>
                <div class="card-body">
                    <?php if (empty($clientesQueVoltaram)): ?>
                        <p class="text-center text-muted my-4">Nenhum cliente recorrente neste período.</p>
                    <?php else: ?>
                        <div class="accordion" id="accordionClientesVoltaram">
                            <?php foreach ($clientesQueVoltaram as $index => $cliente): ?>
                                <div class="accordion-item" style="background-color: transparent; border-color: var(--border-color);">
                                    <h2 class="accordion-header" id="heading<?php echo $index; ?>">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?php echo $index; ?>" aria-expanded="false" aria-controls="collapse<?php echo $index; ?>" style="background-color: var(--bg-tertiary); color: var(--text-primary);">
                                            <div class="d-flex justify-content-between w-100 me-3">
                                                <span><?php echo htmlspecialchars($cliente['nome_completo']); ?></span>
                                                <span class="badge bg-success rounded-pill"><?php echo count($cliente['ordens']); ?> OS</span>
                                            </div>
                                        </button>
                                    </h2>
                                    <div id="collapse<?php echo $index; ?>" class="accordion-collapse collapse" aria-labelledby="heading<?php echo $index; ?>" data-bs-parent="#accordionClientesVoltaram">
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