<?php
$current_page = 'ordens';
require_once __DIR__ . '/../layout/main.php';
?>

<div class="container">
    <div class="d-flex justify-between align-center mb-4 flex-wrap gap-2">
        <h1><?= $title ?></h1>
        <div class="d-flex gap-2 flex-wrap">
            <form action="<?= BASE_URL ?>ordens" method="GET" class="d-flex gap-2">
                <input type="text" name="search" class="form-control" placeholder="ID ou Nome do Cliente" value="<?= htmlspecialchars($search ?? '') ?>">
                <button type="submit" class="btn btn-secondary">Pesquisar</button>
                <?php if (!empty($search)): ?>
                    <a href="<?= BASE_URL ?>ordens" class="btn btn-outline-secondary">Limpar</a>
                <?php endif; ?>
            </form>
            <a href="<?= BASE_URL ?>ordens/form" class="btn btn-primary">
                ➕ Nova Ordem de Serviço
            </a>
        </div>
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
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
