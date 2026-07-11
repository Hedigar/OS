<?php
$current_page = 'relatorios';
require_once __DIR__ . '/../layout/main.php';
?>

<div class="container">
    <h1 class="mb-4">Visão de Entradas Órfãs</h1>

    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title text-warning">Total de Entradas Órfãs</h5>
            <h3>R$ <?= number_format($dados['total'], 2, ',', '.') ?></h3>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <h5 class="card-title mb-3">Entradas Não Faturadas</h5>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Data Pagamento</th>
                            <th>Origem</th>
                            <th>Cliente</th>
                            <th>Descrição</th>
                            <th>Valor Bruto</th>
                            <th>Valor Líquido</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($dados['itens'] as $item): ?>
                        <tr>
                            <td><?= date('d/m/Y H:i', strtotime($item['data_pagamento'])) ?></td>
                            <td>
                                <?php if ($item['tipo_origem'] == 'os'): ?>
                                    <a href="<?= BASE_URL ?>ordens/view?id=<?= $item['origem_id'] ?>" class="text-primary fw-bold">
                                        OS #<?= $item['origem_id'] ?>
                                    </a>
                                <?php else: ?>
                                    <?= strtoupper($item['tipo_origem']) ?> #<?= $item['origem_id'] ?>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($item['cliente']) ?></td>
                            <td><?= htmlspecialchars($item['descricao']) ?></td>
                            <td class="text-success">R$ <?= number_format($item['valor_bruto'], 2, ',', '.') ?></td>
                            <td class="text-info">R$ <?= number_format($item['valor_liquido'], 2, ',', '.') ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
