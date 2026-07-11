<?php
$current_page = 'relatorios';
require_once __DIR__ . '/../layout/main.php';
?>

<div class="container">
    <h1 class="mb-4">Visão de Competência (Produção)</h1>

    <div class="card mb-4">
        <div class="card-body">
            <form method="get" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Data Início</label>
                    <input type="date" class="form-control" name="data_inicio" value="<?= htmlspecialchars($filtros['data_inicio']) ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Data Fim</label>
                    <input type="date" class="form-control" name="data_fim" value="<?= htmlspecialchars($filtros['data_fim']) ?>">
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary">Filtrar</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 text-center">
                    <h5 class="card-title text-info">Total OS</h5>
                    <h3 class="text-info">R$ <?= number_format($dados['total_os'], 2, ',', '.') ?></h3>
                </div>
                <div class="col-md-4 text-center">
                    <h5 class="card-title text-warning">Total Atendimentos Externos</h5>
                    <h3 class="text-warning">R$ <?= number_format($dados['total_atendimentos'], 2, ',', '.') ?></h3>
                </div>
                <div class="col-md-4 text-center">
                    <h5 class="card-title text-success">Total Produzido</h5>
                    <h3 class="text-success">R$ <?= number_format($dados['total'], 2, ',', '.') ?></h3>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <h5 class="card-title mb-3">Ordens Finalizadas e Atendimentos Concluídos</h5>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Tipo</th>
                            <th>ID</th>
                            <th>Data Finalização</th>
                            <th>Cliente</th>
                            <th>Valor Faturado</th>
                            <th>Custos de Peças</th>
                            <th>Custos de Taxas</th>
                            <th>Lucro/Prejuízo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($dados['itens'] as $item): ?>
                        <tr>
                            <td><?= $item['tipo'] === 'os' ? 'OS' : 'Atendimento' ?></td>
                            <td>
                                <?php if ($item['tipo'] === 'os'): ?>
                                    <a href="<?= BASE_URL ?>ordens/view?id=<?= $item['id'] ?>" class="text-primary fw-bold">
                                        #<?= $item['id'] ?>
                                    </a>
                                <?php else: ?>
                                    <a href="<?= BASE_URL ?>atendimentos-externos/view?id=<?= $item['id'] ?>" class="text-warning fw-bold">
                                        #<?= $item['id'] ?>
                                    </a>
                                <?php endif; ?>
                            </td>
                            <td><?= date('d/m/Y', strtotime($item['data_finalizacao'])) ?></td>
                            <td><?= htmlspecialchars($item['cliente']) ?></td>
                            <td class="text-success">R$ <?= number_format($item['valor_faturado'], 2, ',', '.') ?></td>
                            <td class="text-danger">R$ <?= number_format($item['custos_pecas'], 2, ',', '.') ?></td>
                            <td class="text-danger">R$ <?= number_format($item['custos_taxas'], 2, ',', '.') ?></td>
                            <td class="<?= $item['lucro_prejuizo'] >= 0 ? 'text-success' : 'text-danger' ?>">
                                R$ <?= number_format($item['lucro_prejuizo'], 2, ',', '.') ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
