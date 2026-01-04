<?php
$current_page = 'ordens';
require_once __DIR__ . '/../layout/main.php';
?>

<div class="container">
    <div class="d-flex justify-between align-center mb-4">
        <h1><?= $title ?></h1>
        <a href="<?= BASE_URL ?>ordens/form" class="btn btn-primary">
            ➕ Nova Ordem de Serviço
        </a>
    </div>

    <?php if (empty($ordens)): ?>
        <div class="card">
            <div class="alert alert-info m-0">Nenhuma Ordem de Serviço cadastrada.</div>
        </div>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table class="table">
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
                            <td>
                                <span class="badge" style="background-color: <?= $ordem['status_cor'] ?? 'var(--bg-tertiary)' ?>;">
                                    <?= htmlspecialchars($ordem['status'] ?? '') ?>
                                </span>
                            </td>
                            <td>R$ <?= number_format($ordem['valor'] ?? 0, 2, ',', '.') ?></td>
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
