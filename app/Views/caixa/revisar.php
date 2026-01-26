<?php
$fmt = fn($v) => number_format((float)$v, 2, ',', '.');
?>
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="mb-0"><?php echo htmlspecialchars($title ?? 'Revisão do Caixa'); ?></h1>
        <a href="<?php echo BASE_URL; ?>caixa" class="btn btn-outline-secondary">Voltar ao Caixa</a>
    </div>
    <form class="row g-2 mb-3" method="get" action="<?php echo BASE_URL; ?>caixa/revisar">
        <div class="col-md-3">
            <input type="date" name="data_inicio" class="form-control" value="<?php echo htmlspecialchars($filtros['data_inicio'] ?? date('Y-m-01')); ?>">
        </div>
        <div class="col-md-3">
            <input type="date" name="data_fim" class="form-control" value="<?php echo htmlspecialchars($filtros['data_fim'] ?? date('Y-m-t')); ?>">
        </div>
        <div class="col-md-2">
            <button class="btn btn-secondary w-100" type="submit">Filtrar</button>
        </div>
    </form>
    <div class="row">
        <div class="col-lg-6">
            <div class="card p-3 mb-4">
                <h3 class="mb-2">OS Entregues com Pagamento Pendente</h3>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>OS</th>
                                <th>Cliente</th>
                                <th class="text-end">Total</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($osPendentes ?? [])): ?>
                                <?php foreach ($osPendentes as $os): ?>
                                    <tr>
                                        <td><a href="<?php echo BASE_URL; ?>ordens/view?id=<?php echo (int)$os['id']; ?>">#<?php echo (int)$os['id']; ?></a></td>
                                        <td><?php echo htmlspecialchars($os['cliente_nome'] ?? ''); ?></td>
                                        <td class="text-end">R$ <?php echo $fmt($os['valor_total_os'] ?? 0); ?></td>
                                        <td><?php echo htmlspecialchars($os['status_pagamento'] ?? 'pendente'); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="4">Sem pendências no período</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card p-3 mb-4">
                <h3 class="mb-2">OS Entregues e Pagas</h3>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>OS</th>
                                <th>Cliente</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($osPagasEntregues ?? [])): ?>
                                <?php foreach ($osPagasEntregues as $os): ?>
                                    <tr>
                                        <td><a href="<?php echo BASE_URL; ?>ordens/view?id=<?php echo (int)$os['id']; ?>">#<?php echo (int)$os['id']; ?></a></td>
                                        <td><?php echo htmlspecialchars($os['cliente_nome'] ?? ''); ?></td>
                                        <td class="text-end">R$ <?php echo $fmt($os['valor_total_os'] ?? 0); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="3">Sem registros no período</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="card p-3">
        <h3 class="mb-2">Transações no Período</h3>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Data</th>
                        <th>Origem</th>
                        <th>Máquina</th>
                        <th>Forma</th>
                        <th>Bandeira</th>
                        <th>Parcelas</th>
                        <th class="text-end">Bruto</th>
                        <th class="text-end">Taxa</th>
                        <th class="text-end">Líquido</th>
                        <th class="text-center">Verificação</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($transacoes ?? [])): ?>
                        <?php foreach ($transacoes as $t): ?>
                            <?php
                                $origem = htmlspecialchars($t['tipo_origem'] ?? '');
                                $origemId = (int)($t['origem_id'] ?? 0);
                                $link = '#';
                                if ($origem === 'os') {
                                    $link = BASE_URL . 'ordens/view?id=' . $origemId;
                                } elseif ($origem === 'atendimento') {
                                    $link = BASE_URL . 'atendimentos-externos/view?id=' . $origemId;
                                }
                                $ver = (int)($t['verificado'] ?? 0) === 1;
                            ?>
                            <tr data-id="<?php echo (int)($t['id'] ?? 0); ?>">
                                <td><?php echo htmlspecialchars(substr((string)($t['created_at'] ?? ''), 0, 19)); ?></td>
                                <td><a href="<?php echo $link; ?>"><?php echo strtoupper($origem); ?> #<?php echo $origemId; ?></a></td>
                                <td><?php echo htmlspecialchars($t['maquina'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($t['forma'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($t['bandeira'] ?? ''); ?></td>
                                <td><?php echo (int)($t['parcelas'] ?? 1); ?></td>
                                <td class="text-end">R$ <?php echo $fmt($t['valor_bruto'] ?? 0); ?></td>
                                <td class="text-end">R$ <?php echo $fmt($t['valor_taxa'] ?? 0); ?></td>
                                <td class="text-end fw-bold">R$ <?php echo $fmt($t['valor_liquido'] ?? 0); ?></td>
                                <td class="text-center">
                                    <?php if ($ver): ?>
                                        <span class="badge bg-success">Verificado</span>
                                    <?php else: ?>
                                        <button type="button" class="btn btn-sm btn-primary btn-verificar">Marcar verificado</button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="10">Sem transações no período</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div id="verificar-msg" class="mt-2"></div>
    </div>
</div>
<script>
document.addEventListener('click', async function(e){
    const btn = e.target.closest('.btn-verificar');
    if (!btn) return;
    const tr = btn.closest('tr');
    const id = tr?.dataset?.id;
    if (!id) return;
    btn.disabled = true;
    const msg = document.getElementById('verificar-msg');
    try {
        const resp = await fetch('<?php echo BASE_URL; ?>caixa/marcar-verificado', {
            method: 'POST',
            headers: {'X-Requested-With': 'XMLHttpRequest','Content-Type':'application/x-www-form-urlencoded'},
            body: 'id=' + encodeURIComponent(id)
        });
        const data = await resp.json();
        if (data && data.success) {
            const cell = btn.parentElement;
            cell.innerHTML = '<span class="badge bg-success">Verificado</span>';
            msg.textContent = 'Transação #' + id + ' marcada como verificada.';
            msg.className = 'alert alert-success';
        } else {
            msg.textContent = (data && data.error) ? data.error : 'Erro ao marcar verificado';
            msg.className = 'alert alert-danger';
            btn.disabled = false;
        }
    } catch (err) {
        msg.textContent = 'Erro de rede';
        msg.className = 'alert alert-danger';
        btn.disabled = false;
    }
});
</script>
