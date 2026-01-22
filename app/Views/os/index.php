<?php
$current_page = 'ordens';
require_once __DIR__ . '/../layout/main.php';
?>

<div class="container">
    <div class="d-flex justify-between align-center mb-4 flex-wrap gap-2">
        <h1><?= $title ?></h1>
        <div class="d-flex gap-2 flex-wrap">
            <form action="<?= BASE_URL ?>ordens" method="GET" class="d-flex gap-2 flex-wrap align-items-end">
                <div>
                    <label class="form-label mb-1">Busca</label>
                    <input type="text" name="search" class="form-control" placeholder="ID ou Nome do Cliente" value="<?= htmlspecialchars($search ?? '') ?>">
                </div>
                <div>
                    <label class="form-label mb-1">Status OS</label>
                    <select name="status_id" class="form-select">
                        <option value="">Todos</option>
                        <?php foreach (($statuses ?? []) as $st): ?>
                            <option value="<?= (int)$st['id'] ?>" <?= (!empty($filters['status_id']) && (int)$filters['status_id'] === (int)$st['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($st['nome']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="form-label mb-1">Pagamento</label>
                    <select name="status_pagamento" class="form-select">
                        <?php $sp = $filters['status_pagamento'] ?? ''; ?>
                        <option value="">Todos</option>
                        <option value="pendente" <?= $sp === 'pendente' ? 'selected' : '' ?>>Pendente</option>
                        <option value="parcial" <?= $sp === 'parcial' ? 'selected' : '' ?>>Parcial</option>
                        <option value="pago" <?= $sp === 'pago' ? 'selected' : '' ?>>Pago</option>
                    </select>
                </div>
                <div>
                    <label class="form-label mb-1">Entrega</label>
                    <select name="status_entrega" class="form-select">
                        <?php $se = $filters['status_entrega'] ?? ''; ?>
                        <option value="">Todos</option>
                        <option value="nao_entregue" <?= $se === 'nao_entregue' ? 'selected' : '' ?>>Não Entregue</option>
                        <option value="entregue" <?= $se === 'entregue' ? 'selected' : '' ?>>Entregue</option>
                    </select>
                </div>
                <div>
                    <label class="form-label mb-1">Sem Atualização (dias)</label>
                    <input type="number" name="sem_atualizacao_dias" class="form-control" min="0" value="<?= htmlspecialchars((string)($filters['sem_atualizacao_dias'] ?? '')) ?>" placeholder="ex: 2">
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-secondary">Filtrar</button>
                    <a href="<?= BASE_URL ?>ordens" class="btn btn-outline-secondary">Limpar</a>
                </div>
            </form>
            <a href="<?= BASE_URL ?>ordens/form" class="btn btn-primary">
                ➕ Nova Ordem de Serviço
            </a>
        </div>
    </div>
    
    <div class="mb-3 d-flex gap-2 flex-wrap">
        <a class="btn btn-outline-primary btn-sm" href="<?= BASE_URL ?>ordens?status_pagamento=pendente">Pagamento Pendente</a>
        <a class="btn btn-outline-warning btn-sm" href="<?= BASE_URL ?>ordens?status_entrega=nao_entregue">Entrega Pendente</a>
        <a class="btn btn-outline-danger btn-sm" href="<?= BASE_URL ?>ordens?sem_atualizacao_dias=2">Sem atualização há 2+ dias</a>
    </div>

    <?php if (empty($ordens)): ?>
        <div class="card">
            <div class="alert alert-info m-0">Nenhuma Ordem de Serviço encontrada.</div>
        </div>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Cliente</th>
                        <th>Modelo</th>
                        <th>Entrada</th>
                        <th>Status</th>
                        <th>Pagamento</th>
                        <th>Entrega</th>
                        <th>Valor</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($ordens as $ordem): ?>
                        <tr>
                            <td><?= $ordem['id'] ?></td>
                            <td><?= htmlspecialchars($ordem['cliente_nome'] ?? $ordem['cliente_id']) ?></td>
                            <td><?= htmlspecialchars($ordem['equipamento_modelo'] ?? 'N/A') ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($ordem['created_at'])) ?></td>
                            <td>
                                <span class="badge" style="background-color: <?= $ordem['status_cor'] ?? 'var(--bg-tertiary)' ?>;">
                                    <?= htmlspecialchars($ordem['status_nome'] ?? '') ?>
                                </span>
                            </td>
                            <td>
                                <?php 
                                    $status_pag = $ordem['status_pagamento'] ?? 'pendente';
                                    $pag_cor = ($status_pag === 'pago') ? '#2ecc71' : (($status_pag === 'parcial') ? '#f1c40f' : '#e74c3c');
                                    $pag_label = ($status_pag === 'pago') ? 'Pago' : (($status_pag === 'parcial') ? 'Parcial' : 'Pendente');
                                ?>
                                <span class="badge" style="background-color: <?= $pag_cor ?>;">
                                    <?= $pag_label ?>
                                </span>
                            </td>
                            <td>
                                <?php 
                                    $status_ent = $ordem['status_entrega'] ?? 'nao_entregue';
                                    $ent_cor = ($status_ent === 'entregue') ? '#2ecc71' : '#e74c3c';
                                    $ent_label = ($status_ent === 'entregue') ? 'Entregue' : 'Não Entregue';
                                ?>
                                <span class="badge" style="background-color: <?= $ent_cor ?>;">
                                    <?= $ent_label ?>
                                </span>
                            </td>
                            <td>R$ <?= number_format($ordem['valor_total_os'] ?? 0, 2, ',', '.') ?></td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="<?= BASE_URL ?>ordens/view?id=<?= $ordem['id'] ?>" class="btn btn-sm btn-info">Visualizar</a>
                                    <form action="<?= BASE_URL ?>ordens/deletar" method="POST" class="d-inline">
                                        <input type="hidden" name="id" value="<?= $ordem['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza que deseja deletar esta Ordem de Serviço?');">Deletar</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php if (($totalPaginas ?? 0) > 1): ?>
            <div class="mt-4">
                <div class="pagination">
                    <?php 
                        $p_atual = $paginaAtual ?? 1;
                        $raio = 2; 
                        $query_search = !empty($search) ? '&search=' . urlencode($search) : '';
                        $query_filters = '';
                        if (!empty($filters['status_id'])) $query_filters .= '&status_id=' . (int)$filters['status_id'];
                        if (!empty($filters['status_pagamento'])) $query_filters .= '&status_pagamento=' . urlencode($filters['status_pagamento']);
                        if (!empty($filters['status_entrega'])) $query_filters .= '&status_entrega=' . urlencode($filters['status_entrega']);
                        if (!empty($filters['sem_atualizacao_dias'])) $query_filters .= '&sem_atualizacao_dias=' . (int)$filters['sem_atualizacao_dias'];
                        $qs = $query_search . $query_filters;
                    ?>

                    <?php if ($p_atual > 1): ?>
                        <a href="<?= BASE_URL . 'ordens?pagina=' . ($p_atual - 1) . $qs ?>">«</a>
                    <?php endif; ?>

                    <?php if ($p_atual > ($raio + 1)): ?>
                        <a href="<?= BASE_URL . 'ordens?pagina=1' . $qs ?>">1</a>
                        <?php if ($p_atual > ($raio + 2)): ?>
                            <span class="gap" style="padding: 8px 14px;">...</span>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php 
                    $inicio = max(1, $p_atual - $raio);
                    $fim = min($totalPaginas, $p_atual + $raio);

                    for ($i = $inicio; $i <= $fim; $i++): 
                    ?>
                        <a href="<?= BASE_URL . 'ordens?pagina=' . $i . $qs ?>" class="<?= ($i == $p_atual) ? 'active' : '' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>

                    <?php if ($p_atual < ($totalPaginas - $raio)): ?>
                        <?php if ($p_atual < ($totalPaginas - $raio - 1)): ?>
                            <span class="gap" style="padding: 8px 14px;">...</span>
                        <?php endif; ?>
                        <a href="<?= BASE_URL . 'ordens?pagina=' . $totalPaginas . $qs ?>"><?= $totalPaginas ?></a>
                    <?php endif; ?>

                    <?php if ($p_atual < $totalPaginas): ?>
                        <a href="<?= BASE_URL . 'ordens?pagina=' . ($p_atual + 1) . $qs ?>">»</a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

    <?php endif; ?>
</div>

<style>
.pagination { display: flex; justify-content: center; gap: 5px; flex-wrap: wrap; margin-bottom: 20px; }
.pagination a { padding: 8px 14px; border: 1px solid #ddd; border-radius: 4px; text-decoration: none; color: #007bff; }
.pagination a.active { background-color: #007bff; color: white; border-color: #007bff; font-weight: bold; }
.pagination a:hover:not
(.active) { background-color: #f8f9fa; }

</style>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
