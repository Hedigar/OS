<?php
$current_page = 'relatorios';
require_once __DIR__ . '/../layout/main.php';
?>

<div class="container">
    <h1 class="mb-4">Visão de Caixa (DRE)</h1>

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
                <div class="col-md-4 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary">Filtrar</button>
                    <button type="button" id="btnAuditar" class="btn btn-warning">
                        <i class="fas fa-check-double"></i> Auditar
                    </button>
                    <button type="button" id="btnLimparFluxo" class="btn btn-danger" title="Remove registros órfãos do fluxo de caixa (itens/pagamentos deletados)">
                        <i class="fas fa-broom"></i> Limpar Fluxo
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="alert alert-<?= $auditoria['status'] == 'OK' ? 'success' : 'danger' ?>" role="alert">
        <h4 class="alert-heading"><?= $auditoria['status'] ?></h4>
        <p>Saldo Calculado: <strong>R$ <?= number_format($auditoria['saldo_calculado'], 2, ',', '.') ?></strong></p>
        <?php if (!empty($auditoria['divergencias'])): ?>
            <hr>
            <ul class="mb-0">
                <?php foreach ($auditoria['divergencias'] as $div): ?>
                    <li><?= htmlspecialchars($div['descricao']) ?> (<?= $div['quantidade'] ?>)</li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>

    <!-- DRE Cascading Format -->
    <div class="card mb-4">
        <div class="card-body">
            <!-- Entrada Bruta -->
            <div class="mb-3">
                <div class="d-flex justify-content-between align-items-center p-3 bg-success bg-opacity-10 rounded cursor-pointer" onclick="toggleSection('entradas-section')">
                    <div>
                        <h5 class="mb-0 text-success"><i class="fas fa-arrow-down mr-2"></i> Entrada Bruta</h5>
                        <small class="text-muted">Valor total processado antes de deduções</small>
                    </div>
                    <h3 class="mb-0 text-success">R$ <?= number_format($dados['entrada_bruta'], 2, ',', '.') ?></h3>
                </div>
                <div id="entradas-section" class="mt-2 p-3 border rounded">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Data</th>
                                    <th>Origem</th>
                                    <th>Cliente</th>
                                    <th>Descrição</th>
                                    <th>Valor Bruto</th>
                                    <th>Taxa Cartão</th>
                                    <th>Valor Líquido</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($dados['entradas'] as $entrada): ?>
                                <tr>
                                    <td><?= date('d/m/Y H:i', strtotime($entrada['data_transacao'])) ?></td>
                                    <td>
                                        <?php if ($entrada['tipo_origem'] == 'os'): ?>
                                            <a href="<?= BASE_URL ?>ordens/view?id=<?= $entrada['origem_id'] ?>" class="text-primary fw-bold">OS #<?= $entrada['origem_id'] ?></a>
                                        <?php else: ?>
                                            <?= strtoupper($entrada['tipo_origem']) ?> #<?= $entrada['origem_id'] ?>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars($entrada['cliente']) ?></td>
                                    <td><?= htmlspecialchars($entrada['descricao']) ?></td>
                                    <td class="text-success">R$ <?= number_format($entrada['valor_bruto'], 2, ',', '.') ?></td>
                                    <td class="text-danger">R$ <?= number_format($entrada['taxa_cartao'], 2, ',', '.') ?></td>
                                    <td class="text-info">R$ <?= number_format($entrada['valor_liquido'], 2, ',', '.') ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- (-) Deduções de Venda -->
            <div class="mb-3">
                <div class="d-flex justify-content-between align-items-center p-3 bg-danger bg-opacity-10 rounded cursor-pointer" onclick="toggleSection('deducoes-section')">
                    <div>
                        <h5 class="mb-0 text-danger"><i class="fas fa-minus mr-2"></i> Deduções de Venda</h5>
                        <small class="text-muted">Taxas de cartão/PIX</small>
                        <?php if ($dados['entrada_bruta'] > 0): ?>
                            <br>
                            <small class="text-muted">
                                Percentual: <?= number_format(($dados['deducoes_venda'] / $dados['entrada_bruta']) * 100, 2, ',', '.') ?>%
                            </small>
                        <?php endif; ?>
                    </div>
                    <h3 class="mb-0 text-danger">R$ <?= number_format($dados['deducoes_venda'], 2, ',', '.') ?></h3>
                </div>
                <div id="deducoes-section" class="mt-2 p-3 border rounded" style="display: none;">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Data</th>
                                    <th>Origem</th>
                                    <th>Valor Taxa</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($dados['entradas'] as $entrada): ?>
                                    <?php if ($entrada['taxa_cartao'] > 0): ?>
                                    <tr>
                                        <td><?= date('d/m/Y H:i', strtotime($entrada['data_transacao'])) ?></td>
                                        <td>
                                            <?php if ($entrada['tipo_origem'] == 'os'): ?>
                                                <a href="<?= BASE_URL ?>ordens/view?id=<?= $entrada['origem_id'] ?>" class="text-primary fw-bold">OS #<?= $entrada['origem_id'] ?></a>
                                            <?php else: ?>
                                                <?= strtoupper($entrada['tipo_origem']) ?> #<?= $entrada['origem_id'] ?>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-danger">R$ <?= number_format($entrada['taxa_cartao'], 2, ',', '.') ?></td>
                                    </tr>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- (=) Receita Líquida -->
            <div class="mb-3">
                <div class="d-flex justify-content-between align-items-center p-3 bg-info bg-opacity-10 rounded">
                    <div>
                        <h5 class="mb-0 text-info"><i class="fas fa-equals mr-2"></i> Receita Líquida</h5>
                        <small class="text-muted">Entrada Bruta - Deduções de Venda</small>
                    </div>
                    <h3 class="mb-0 text-info">R$ <?= number_format($dados['receita_liquida'], 2, ',', '.') ?></h3>
                </div>
            </div>

            <!-- (-) Custos Diretos -->
            <div class="mb-3">
                <div class="d-flex justify-content-between align-items-center p-3 bg-warning bg-opacity-10 rounded cursor-pointer" onclick="toggleSection('custos-section')">
                    <div>
                        <h5 class="mb-0 text-warning"><i class="fas fa-minus mr-2"></i> Custos Diretos</h5>
                        <small class="text-muted">Custos de peças, NF e outras saídas operacionais</small>
                    </div>
                    <h3 class="mb-0 text-warning">R$ <?= number_format($dados['custos_diretos'], 2, ',', '.') ?></h3>
                </div>
                <div id="custos-section" class="mt-2 p-3 border rounded" style="display: none;">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Data</th>
                                    <th>Origem / Objeto</th>
                                    <th>Categoria</th>
                                    <th>Item / OS</th>
                                    <th>Valor</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($dados['saidas'] as $saida): ?>
                                <tr>
                                    <td><?= date('d/m/Y', strtotime($saida['data_transacao'])) ?></td>
                                    <td>
                                        <?php if ($saida['tipo_origem'] == 'item_os'): ?>
                                            <a href="<?= BASE_URL ?>ordens/view?id=<?= $saida['origem_id_relacionada'] ?? $saida['origem_id'] ?>" class="text-primary fw-bold">
                                                <i class="fas fa-file-invoice"></i> Item OS #<?= $saida['origem_id'] ?>
                                            </a>
                                        <?php elseif ($saida['tipo_origem'] == 'item_atendimento'): ?>
                                            <a href="<?= BASE_URL ?>atendimentos-externos/view?id=<?= $saida['origem_id_relacionada'] ?? $saida['origem_id'] ?>" class="text-info fw-bold">
                                                <i class="fas fa-external-link-alt"></i> Item Atend #<?= $saida['origem_id'] ?>
                                            </a>
                                        <?php elseif ($saida['tipo_origem'] == 'despesa'): ?>
                                            <span class="text-muted"><i class="fas fa-money-bill-wave"></i> DESPESA #<?= $saida['origem_id'] ?></span>
                                        <?php else: ?>
                                            <?= strtoupper($saida['tipo_origem']) ?> #<?= $saida['origem_id'] ?>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars($saida['categoria']) ?></td>
                                    <td><?= htmlspecialchars($saida['descricao']) ?></td>
                                    <td class="text-warning">R$ <?= number_format($saida['valor'], 2, ',', '.') ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- (=) Resultado Final (Fluxo de Caixa) -->
            <div class="mb-3">
                <div class="d-flex justify-content-between align-items-center p-3 bg-primary bg-opacity-10 rounded">
                    <div>
                        <h5 class="mb-0 text-primary"><i class="fas fa-equals mr-2"></i> Resultado Final (Fluxo de Caixa)</h5>
                        <small class="text-muted">Receita Líquida - Custos Diretos (deve bater com o extrato bancário)</small>
                    </div>
                    <h3 class="mb-0 text-primary">R$ <?= number_format($dados['resultado_final'], 2, ',', '.') ?></h3>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript for toggling sections -->
    <script>
        function toggleSection(sectionId) {
            const section = document.getElementById(sectionId);
            if (section.style.display === 'none') {
                section.style.display = 'block';
            } else {
                section.style.display = 'none';
            }
        }

        document.getElementById('btnLimparFluxo')?.addEventListener('click', async function () {
            if (!confirm('⚠️ ATENÇÃO: Esta ação irá remover registros órfãos do fluxo de caixa (itens e pagamentos deletados via softdelete).\n\nDeseja continuar?')) {
                return;
            }

            const btn = this;
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processando...';

            try {
                const formData = new FormData();
                const response = await fetch('<?= BASE_URL ?>relatorios/limpar-fluxo-caixa', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    let msg = `✅ Limpeza concluída! ${result.total_removidos} registro(s) removido(s).`;
                    if (result.total_removidos > 0 && result.detalhes) {
                        msg += `\n\n📊 Detalhes:\n• Pagamentos: ${result.detalhes.pagamentos}\n• Itens OS: ${result.detalhes.itens_os}\n• Itens Atendimento: ${result.detalhes.itens_atendimento}`;
                    }
                    alert(msg);
                    window.location.reload();
                } else {
                    alert('❌ Erro: ' + (result.error || 'Erro desconhecido'));
                }
            } catch (err) {
                alert('❌ Erro de conexão: ' + err.message);
            } finally {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-broom"></i> Limpar Fluxo';
            }
        });
    </script>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
