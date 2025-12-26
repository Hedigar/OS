<?php
// Inclui o layout principal
require_once __DIR__ . '/../layout/main.php';

$is_edit = isset($usuario) && $usuario !== null;
$action_url = BASE_URL . ($is_edit ? 'usuarios/atualizar' : 'usuarios/salvar');
?>

<div class="container mt-5">
    <h2><?= $title ?></h2>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form action="<?= $action_url ?>" method="POST">
        <?php if ($is_edit): ?>
            <input type="hidden" name="id" value="<?= $usuario['id'] ?>">
        <?php endif; ?>

        <div class="mb-3">
            <label for="nome" class="form-label">Nome</label>
            <input type="text" class="form-control" id="nome" name="nome" value="<?= $is_edit ? htmlspecialchars($usuario['nome']) : '' ?>" required>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" value="<?= $is_edit ? htmlspecialchars($usuario['email']) : '' ?>" required>
        </div>

        <div class="mb-3">
            <label for="senha" class="form-label"><?= $is_edit ? 'Nova Senha (deixe em branco para não alterar)' : 'Senha' ?></label>
            <input type="password" class="form-control" id="senha" name="<?= $is_edit ? 'nova_senha' : 'senha' ?>" <?= $is_edit ? '' : 'required' ?>>
        </div>

        <div class="mb-3">
            <label for="nivel_acesso" class="form-label">Nível de Acesso</label>
            <select class="form-control" id="nivel_acesso" name="nivel_acesso" required>
                <option value="1" <?= $is_edit && $usuario['nivel_acesso'] == 1 ? 'selected' : '' ?>>Administrador</option>
                <option value="0" <?= $is_edit && $usuario['nivel_acesso'] == 0 ? 'selected' : '' ?>>Padrão</option>
            </select>
        </div>

        <button type="submit" class="btn btn-success"><?= $is_edit ? 'Atualizar' : 'Salvar' ?></button>
        <a href="<?= BASE_URL ?>usuarios" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

<?php
// Inclui o rodapé (se houver)
require_once __DIR__ . '/../layout/footer.php';
?>
