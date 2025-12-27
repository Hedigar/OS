<?php
// Inclui o layout principal
require_once __DIR__ . '/../layout/main.php';
?>

<div class="container mt-5">
    <h2><?= $title ?></h2>
    <a href="<?= BASE_URL ?>ordens/form" class="btn btn-primary mb-3">Nova Ordem de Serviço</a>

    <?php if (empty($ordens)): ?>
        <div class="alert alert-info">Nenhuma Ordem de Serviço cadastrada.</div>
    <?php else: ?>
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
    <?php endif; ?>
</div>

<?php
// Inclui o rodapé (se houver)
require_once __DIR__ . '/../layout/footer.php';
?>
