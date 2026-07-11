<?php
$current_page = 'relatorios';
require_once __DIR__ . '/../layout/main.php';
?>

<div class="container">
    <h1 class="mb-4">Visão Analítica de OS</h1>

    <div class="card mb-4">
        <div class="card-body">
            <form method="get" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">OS ID Início</label>
                    <input type="number" class="form-control" name="os_id_inicio" value="<?= htmlspecialchars($filtros['os_id_inicio']) ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">OS ID Fim</label>
                    <input type="number" class="form-control" name="os_id_fim" value="<?= htmlspecialchars($filtros['os_id_fim']) ?>">
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary">Analisar</button>
                </div>
            </form>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title text-success">Faturamento Total</h5>
                    <h3>R$ <?= number_format($dados['total_faturamento'], 2, ',', '.') ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title text-danger">Custos Totais</h5>
                    <h3>R$ <?= number_format($dados['total_custos'], 2, ',', '.') ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title text-info">Lucro Total</h5>
                    <h3>R$ <?= number_format($dados['total_lucro'], 2, ',', '.') ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title text-warning">Média de Lucro</h5>
                    <h3>R$ <?= number_format($dados['media_lucro'], 2, ',', '.') ?></h3>
                </div>
            </div>
        </div>
    </div>

    <?php if (!empty($dados['ordens_prejuizo'])): ?>
    <div class="alert alert-warning mb-4" role="alert">
        <h4 class="alert-heading">Ordens com Prejuízo!</h4>
        <p>As seguintes ordens tiveram custos maiores que o faturamento:</p>
        <ul class="mb-0">
            <?php foreach ($dados['ordens_prejuizo'] as $osId): ?>
                <li><a href="<?= BASE_URL ?>ordens/view?id=<?= $osId ?>" class="alert-link">OS #<?= $osId ?></a></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <h5 class="card-title mb-3">Análise Detalhada</h5>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>OS #</th>
                            <th>Cliente</th>
                            <th>Faturamento</th>
                            <th>Custos de Peças</th>
                            <th>Custos de Taxas</th>
                            <th>Custos Total</th>
                            <th>Lucro</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($dados['itens'] as $item): ?>
                        <tr class="<?= $item['lucro'] < 0 ? 'table-warning' : '' ?>">
                            <td>
                                <a href="<?= BASE_URL ?>ordens/view?id=<?= $item['os_id'] ?>" class="text-primary fw-bold">
                                    #<?= $item['os_id'] ?>
                                </a>
                            </td>
                            <td><?= htmlspecialchars($item['cliente']) ?></td>
                            <td class="text-success">R$ <?= number_format($item['faturamento'], 2, ',', '.') ?></td>
                            <td class="text-danger">R$ <?= number_format($item['custos_pecas'], 2, ',', '.') ?></td>
                            <td class="text-danger">R$ <?= number_format($item['custos_taxas'], 2, ',', '.') ?></td>
                            <td class="text-danger">R$ <?= number_format($item['custos_total'], 2, ',', '.') ?></td>
                            <td class="<?= $item['lucro'] >= 0 ? 'text-success' : 'text-danger' ?>">
                                R$ <?= number_format($item['lucro'], 2, ',', '.') ?>
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
