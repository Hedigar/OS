<?php
function formatCurrency($value) {
    return number_format((float)$value, 2, ',', '.');
}
?>
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="mb-0"><?php echo htmlspecialchars($title ?? 'Caixa'); ?></h1>
        <a href="<?php echo BASE_URL; ?>caixa/revisar" class="btn btn-outline-primary">Revisar Caixa</a>
    </div>
    <form class="row g-2 mb-3" method="get" action="<?php echo BASE_URL; ?>caixa">
        <div class="col-md-3">
            <input type="date" name="data_inicio" class="form-control" value="<?php echo htmlspecialchars($filtros['data_inicio'] ?? date('Y-m-d')); ?>">
        </div>
        <div class="col-md-3">
            <input type="date" name="data_fim" class="form-control" value="<?php echo htmlspecialchars($filtros['data_fim'] ?? date('Y-m-d')); ?>">
        </div>
        <div class="col-md-2">
            <button class="btn btn-secondary w-100" type="submit">Filtrar</button>
        </div>
    </form>
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card p-3">
                <div class="text-muted">Entradas (líquido)</div>
                <div class="h4">R$ <?php echo formatCurrency($totais['entradas'] ?? 0); ?></div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-3">
                <div class="text-muted">Taxas</div>
                <div class="h4">R$ <?php echo formatCurrency($totais['taxas'] ?? 0); ?></div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-3">
                <div class="text-muted">Saídas</div>
                <div class="h4">R$ <?php echo formatCurrency($totais['saidas'] ?? 0); ?></div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-3">
                <div class="text-muted">Saldo</div>
                <div class="h4">R$ <?php echo formatCurrency($totais['saldo'] ?? 0); ?></div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <h2 class="mb-2">Entradas</h2>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Data</th>
                            <th>Origem</th>
                            <th>Forma</th>
                            <th>Bandeira</th>
                            <th class="text-end">Bruto</th>
                            <th class="text-end">Taxa</th>
                            <th class="text-end">Líquido</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($entradas)): ?>
                            <?php foreach ($entradas as $e): ?>
                                <?php
                                    $origem = htmlspecialchars($e['tipo_origem'] ?? '');
                                    $origemId = (int)($e['origem_id'] ?? 0);
                                    $link = '#';
                                    if ($origem === 'os') {
                                        $link = BASE_URL . 'ordens/view?id=' . $origemId;
                                    } elseif ($origem === 'atendimento') {
                                        $link = BASE_URL . 'atendimentos-externos/view?id=' . $origemId;
                                    }
                                ?>
                                <tr>
                                    <td><?php echo htmlspecialchars(substr((string)($e['created_at'] ?? ''), 0, 19)); ?></td>
                                    <td><a href="<?php echo $link; ?>"><?php echo strtoupper($origem); ?> #<?php echo $origemId; ?></a></td>
                                    <td><?php echo htmlspecialchars($e['forma'] ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($e['bandeira'] ?? ''); ?></td>
                                    <td class="text-end">R$ <?php echo formatCurrency($e['valor_bruto'] ?? 0); ?></td>
                                    <td class="text-end">R$ <?php echo formatCurrency($e['valor_taxa'] ?? 0); ?></td>
                                    <td class="text-end fw-bold">R$ <?php echo formatCurrency($e['valor_liquido'] ?? 0); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="7">Sem entradas no período</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-md-6">
            <h2 class="mb-2">Saídas</h2>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Data</th>
                            <th>Categoria</th>
                            <th>Descrição</th>
                            <th>Método</th>
                            <th class="text-end">Valor</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($saidas)): ?>
                            <?php foreach ($saidas as $s): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($s['data_despesa'] ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($s['categoria_nome'] ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($s['descricao'] ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($s['metodo_pagamento'] ?? ''); ?></td>
                                    <td class="text-end fw-bold">R$ <?php echo formatCurrency($s['valor'] ?? 0); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="5">Sem saídas no período</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
