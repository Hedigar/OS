<?php
// Inclui o layout principal
require_once __DIR__ . '/../layout/main.php';

$is_edit = isset($usuario) && $usuario !== null;
$action_url = BASE_URL . ($is_edit ? 'usuarios/atualizar' : 'usuarios/salvar');
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card" style="background-color: var(--dark-secondary); border: 1px solid var(--dark-tertiary);">
                <div class="card-header" style="background-color: var(--dark-tertiary); border-bottom: 1px solid var(--dark-tertiary);">
                    <h4 class="text-white mb-0"><?= $title ?></h4>
                </div>
                <div class="card-body text-white">
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

        <?php if ($is_edit): ?>
        <div class="alert alert-warning">
            Para alterar a senha deste usuário, utilize o botão <strong>Resetar Senha</strong> na listagem de usuários. 
            Isso definirá a senha como <code>12345678</code> e forçará o usuário a trocá-la no próximo acesso.
        </div>
        <?php else: ?>
        <div class="alert alert-info">A senha padrão para novos usuários é: <strong>12345678</strong>. O usuário deverá alterá-la no primeiro acesso.</div>
        <?php endif; ?>

        <div class="mb-3">
            <label for="nivel_acesso" class="form-label">Nível de Acesso</label>
            <select class="form-control" id="nivel_acesso" name="nivel_acesso" required>
                <option value="admin" <?= $is_edit && $usuario['nivel_acesso'] == 'admin' ? 'selected' : '' ?>>Administrador</option>
                <option value="tecnico" <?= $is_edit && $usuario['nivel_acesso'] == 'tecnico' ? 'selected' : '' ?>>Técnico</option>
                <option value="usuario" <?= $is_edit && $usuario['nivel_acesso'] == 'usuario' ? 'selected' : '' ?>>Padrão (Usuário)</option>
            </select>
        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success btn-lg"><?= $is_edit ? 'Atualizar' : 'Salvar' ?></button>
                            <a href="<?= BASE_URL ?>usuarios" class="btn btn-secondary">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Inclui o rodapé (se houver)
require_once __DIR__ . '/../layout/footer.php';
?>
