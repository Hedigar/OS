<?php
// Inclui o layout principal
require_once __DIR__ . '/../layout/main.php';
?>

<style>
    /* Tabela escura local (fundo muito escuro, textos brancos) - seletores reforçados */
    .dark-table { background-color: #050506 !important; border: 1px solid #0e0e10 !important; border-radius: 12px; padding: 0.25rem; }
    .dark-table .card-body { background: transparent !important; }
    .dark-table .table { background: transparent !important; color: #ffffff !important; margin-bottom: 0 !important; }
    .dark-table .table thead,
    .dark-table .table thead th,
    .dark-table .table tbody,
    .dark-table .table tbody tr,
    .dark-table .table tbody td,
    .dark-table .table tfoot,
    .dark-table .table tfoot td {
        background: transparent !important;
        color: #ffffff !important;
        border-color: rgba(255,255,255,0.04) !important;
    }
    /* garantir que stripes não apareçam como branco */
    .dark-table .table.table-striped tbody tr:nth-of-type(odd) { background-color: transparent !important; }
    .dark-table .table.table-striped tbody tr:nth-of-type(even) { background-color: transparent !important; }
    /* hover escuro */
    .dark-table .table-hover tbody tr:hover { background-color: #0f0f10 !important; }
    /* botões mantêm cor, ícones/labels brancas se necessário */
    .dark-table .btn { color: inherit; }
    .dark-table .table .text-muted { color: rgba(255,255,255,0.75) !important; }
</style>
<?php
?>

<div class="container mt-5">
    <h2><?= $title ?></h2>
    <a href="<?= BASE_URL ?>ordens/form" class="btn btn-outline-secondary rounded-pill px-4 me-2" style="margin-bottom: 1.25rem;">Nova Ordem de Serviço</a>

    <?php if (empty($ordens)): ?>
        <div class="alert alert-info">Nenhuma Ordem de Serviço cadastrada.</div>
    <?php else: ?>
        <div class="card dark-table" style="background-color: var(--dark-secondary); border: 1px solid var(--dark-tertiary); 
        ">
            <div class="card-body p-0">
                <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome Cliente</th>
                    <th>Equipamento</th>
                    <th>Defeito</th>
                    <th>Status</th>
                    <th>Valor</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($ordens as $ordem): ?>
                    <tr>
                        <td><?= $ordem['id'] ?></td>
                        <td><?= htmlspecialchars($ordem['cliente_nome'] ?? $ordem['cliente_id']) ?></td>
                        <td><?= htmlspecialchars($ordem['equipamento'] ?? '') ?></td>
                        <td><?= htmlspecialchars($ordem['defeito'] ?? '') ?></td>
                        <td><?= htmlspecialchars($ordem['status'] ?? '') ?></td>
                        <td>R$ <?= number_format($ordem['valor'] ?? 0, 2, ',', '.') ?></td>
                        <td>
                            <a href="<?= BASE_URL ?>ordens/view?id=<?= $ordem['id'] ?>" class="btn btn-sm btn-warning">Visualizar</a>
                            <form action="<?= BASE_URL ?>ordens/deletar" method="POST" style="display:inline-block;">
                                <input type="hidden" name="id" value="<?= $ordem['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza que deseja deletar esta Ordem de Serviço?');">Deletar</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php
// Inclui o rodapé (se houver)
require_once __DIR__ . '/../layout/footer.php';
?>
