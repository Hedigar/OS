<?php
// Inclui o layout principal
require_once __DIR__ . '/../layout/main.php';
?>

<div class="container mt-5">
    <h2><?= $title ?></h2>
    <a href="<?= BASE_URL ?>usuarios/form" class="btn btn-primary mb-3">Novo Usuário</a>

    <?php if (empty($usuarios)): ?>
        <div class="alert alert-info">Nenhum usuário cadastrado.</div>
    <?php else: ?>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Email</th>
                    <th>Nível de Acesso</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($usuarios as $usuario): ?>
                    <tr>
                        <td><?= $usuario['id'] ?></td>
                        <td><?= htmlspecialchars($usuario['nome']) ?></td>
                        <td><?= htmlspecialchars($usuario['email']) ?></td>
                        <td><?= ($usuario['nivel_acesso'] ?? 0) == 1 ? 'Administrador' : 'Padrão' ?></td>
                        <td>
                            <a href="<?= BASE_URL ?>usuarios/form?id=<?= $usuario['id'] ?>" class="btn btn-sm btn-warning">Editar</a>
                            <form action="<?= BASE_URL ?>usuarios/deletar" method="POST" style="display:inline-block;">
                                <input type="hidden" name="id" value="<?= $usuario['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza que deseja deletar este usuário?');">Deletar</button>
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
