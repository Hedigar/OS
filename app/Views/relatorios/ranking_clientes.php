<?php
$current_page = 'relatorios';
require_once __DIR__ . '/../layout/main.php';
?>

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-crown text-warning me-2"></i>Ranking de Clientes (Melhores Clientes)</h1>
        <a href="<?= BASE_URL ?>relatorios" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Voltar
        </a>
    </div>

    <!-- Filtros -->
    <div class="card mb-4">
        <div class="card-body">
            <form id="filterForm" method="get" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">Data Início</label>
                    <input type="date" id="data_inicio" class="form-control" name="data_inicio" value="<?= htmlspecialchars($filtros['data_inicio']) ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Data Fim</label>
                    <input type="date" id="data_fim" class="form-control" name="data_fim" value="<?= htmlspecialchars($filtros['data_fim']) ?>">
                </div>
                <div class="col-md-6 d-flex flex-wrap gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter"></i> Filtrar
                    </button>
                    <button type="button" class="btn btn-outline-secondary" onclick="setPeriod('this_month')">Este Mês</button>
                    <button type="button" class="btn btn-outline-secondary" onclick="setPeriod('last_month')">Mês Passado</button>
                    <button type="button" class="btn btn-outline-secondary" onclick="setPeriod('this_year')">Este Ano</button>
                    <button type="button" class="btn btn-outline-secondary" onclick="setPeriod('last_year')">Ano Passado</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Top 3 Destaques -->
    <?php if (count($dados) > 0): ?>
        <div class="row mb-4 justify-content-center">
            <!-- 2º Lugar -->
            <?php if (isset($dados[1])): ?>
                <div class="col-md-3 mt-4">
                    <div class="card border-0 shadow text-center position-relative" style="background: linear-gradient(135deg, #e2e8f0 0%, #cbd5e1 100%);">
                        <div class="card-body py-4">
                            <div class="position-absolute top-0 start-50 translate-middle-x mt-n3" style="width: 40px; height: 40px; background-color: #94a3b8; border-radius: 50%; color: white; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 1.2rem; border: 3px solid #f1f5f9;">
                                2
                            </div>
                            <h5 class="card-title text-dark font-weight-bold mt-2 text-truncate" title="<?= htmlspecialchars($dados[1]['nome_completo']) ?>">
                                <?= htmlspecialchars($dados[1]['nome_completo']) ?>
                            </h5>
                            <p class="card-text text-muted mb-2 small"><?= htmlspecialchars($dados[1]['telefone_principal'] ?? 'Sem telefone') ?></p>
                            <h4 class="text-secondary font-weight-bold mb-0">R$ <?= number_format($dados[1]['total_gasto'], 2, ',', '.') ?></h4>
                            <span class="badge bg-secondary mt-2"><?= $dados[1]['qtd_os'] + $dados[1]['qtd_ae'] ?> Atendimentos</span>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- 1º Lugar -->
            <?php if (isset($dados[0])): ?>
                <div class="col-md-4">
                    <div class="card border-0 shadow text-center position-relative" style="background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); transform: scale(1.05); z-index: 2; border: 2px solid #fbbf24 !important;">
                        <div class="card-body py-5">
                            <div class="position-absolute top-0 start-50 translate-middle-x mt-n3" style="width: 50px; height: 50px; background-color: #f59e0b; border-radius: 50%; color: white; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 1.5rem; border: 4px solid #fef3c7; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                                <i class="fas fa-crown text-warning"></i>
                            </div>
                            <h4 class="card-title text-dark font-weight-bold mt-2 text-truncate" title="<?= htmlspecialchars($dados[0]['nome_completo']) ?>">
                                <?= htmlspecialchars($dados[0]['nome_completo']) ?>
                            </h4>
                            <p class="card-text text-muted mb-2"><?= htmlspecialchars($dados[0]['telefone_principal'] ?? 'Sem telefone') ?></p>
                            <h3 class="text-warning-dark font-weight-bold mb-0" style="color: #b45309;">R$ <?= number_format($dados[0]['total_gasto'], 2, ',', '.') ?></h3>
                            <span class="badge bg-warning mt-2 text-dark"><?= $dados[0]['qtd_os'] + $dados[0]['qtd_ae'] ?> Atendimentos</span>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- 3º Lugar -->
            <?php if (isset($dados[2])): ?>
                <div class="col-md-3 mt-4">
                    <div class="card border-0 shadow text-center position-relative" style="background: linear-gradient(135deg, #ffedd5 0%, #fed7aa 100%);">
                        <div class="card-body py-4">
                            <div class="position-absolute top-0 start-50 translate-middle-x mt-n3" style="width: 40px; height: 40px; background-color: #d97706; border-radius: 50%; color: white; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 1.2rem; border: 3px solid #fff7ed;">
                                3
                            </div>
                            <h5 class="card-title text-dark font-weight-bold mt-2 text-truncate" title="<?= htmlspecialchars($dados[2]['nome_completo']) ?>">
                                <?= htmlspecialchars($dados[2]['nome_completo']) ?>
                            </h5>
                            <p class="card-text text-muted mb-2 small"><?= htmlspecialchars($dados[2]['telefone_principal'] ?? 'Sem telefone') ?></p>
                            <h4 class="text-danger-dark font-weight-bold mb-0" style="color: #c2410c;">R$ <?= number_format($dados[2]['total_gasto'], 2, ',', '.') ?></h4>
                            <span class="badge bg-danger mt-2"><?= $dados[2]['qtd_os'] + $dados[2]['qtd_ae'] ?> Atendimentos</span>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <!-- Tabela do Ranking Completo -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-list me-2"></i>Lista de Classificação</h6>
            <span class="badge bg-info text-white"><?= count($dados) ?> Clientes Encontrados</span>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th class="text-center" style="width: 80px;">Posição</th>
                            <th>Cliente</th>
                            <th>Documento</th>
                            <th>Telefone</th>
                            <th class="text-center">Qtd OS</th>
                            <th class="text-end">Total OS</th>
                            <th class="text-center">Qtd Atend.</th>
                            <th class="text-end">Total Atend.</th>
                            <th class="text-end fw-bold text-success">Total Geral</th>
                            <th class="text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($dados)): ?>
                            <tr>
                                <td colspan="10" class="text-center text-muted py-4">Nenhum gasto registrado para clientes no período selecionado.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($dados as $index => $row): ?>
                                <?php 
                                    $pos = $index + 1;
                                    $badgeClass = '';
                                    if ($pos === 1) $badgeClass = 'bg-warning text-dark';
                                    elseif ($pos === 2) $badgeClass = 'bg-secondary';
                                    elseif ($pos === 3) $badgeClass = 'bg-danger';
                                    else $badgeClass = 'bg-light text-dark border';
                                ?>
                                <tr>
                                    <td class="text-center">
                                        <span class="badge <?= $badgeClass ?> rounded-circle p-2 fs-6" style="min-width: 35px;">
                                            <?= $pos ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="fw-bold"><?= htmlspecialchars($row['nome_completo']) ?></div>
                                    </td>
                                    <td><?= htmlspecialchars($row['documento'] ?? '-') ?></td>
                                    <td><?= htmlspecialchars($row['telefone_principal'] ?? '-') ?></td>
                                    <td class="text-center"><?= $row['qtd_os'] ?></td>
                                    <td class="text-end">R$ <?= number_format($row['total_os'], 2, ',', '.') ?></td>
                                    <td class="text-center"><?= $row['qtd_ae'] ?></td>
                                    <td class="text-end">R$ <?= number_format($row['total_ae'], 2, ',', '.') ?></td>
                                    <td class="text-end fw-bold text-success">R$ <?= number_format($row['total_gasto'], 2, ',', '.') ?></td>
                                    <td class="text-center">
                                        <a href="<?= BASE_URL ?>clientes/view?id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-primary" target="_blank" title="Visualizar Cliente">
                                            <i class="fas fa-eye"></i> Ver Perfil
                                        </a>
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

<script>
function setPeriod(period) {
    const today = new Date();
    let start, end;

    switch (period) {
        case 'this_month':
            start = new Date(today.getFullYear(), today.getMonth(), 1);
            end = new Date(today.getFullYear(), today.getMonth() + 1, 0);
            break;
        case 'last_month':
            start = new Date(today.getFullYear(), today.getMonth() - 1, 1);
            end = new Date(today.getFullYear(), today.getMonth(), 0);
            break;
        case 'this_year':
            start = new Date(today.getFullYear(), 0, 1);
            end = new Date(today.getFullYear(), 11, 31);
            break;
        case 'last_year':
            start = new Date(today.getFullYear() - 1, 0, 1);
            end = new Date(today.getFullYear() - 1, 11, 31);
            break;
    }

    if (start && end) {
        document.getElementById('data_inicio').value = formatDate(start);
        document.getElementById('data_fim').value = formatDate(end);
        document.getElementById('filterForm').submit();
    }
}

function formatDate(date) {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
}
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
