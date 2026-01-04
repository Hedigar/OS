<?php
$is_edit = isset($usuario) && $usuario !== null;
$action_url = BASE_URL . ($is_edit ? 'usuarios/atualizar' : 'usuarios/salvar');
$title_text = $is_edit ? 'Editar Usuário: ' . htmlspecialchars($usuario['nome']) : 'Novo Usuário';
$current_page = 'usuarios';
require_once __DIR__ . '/../layout/main.php';
?>

<div class="container">
    <div class="d-flex justify-between align-center mb-4">
        <h1><?php echo htmlspecialchars($title_text); ?></h1>
        <a href="<?php echo BASE_URL; ?>usuarios" class="btn btn-secondary btn-sm">← Voltar</a>
    </div>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger">
            <span>⚠️</span>
            <span><?php echo htmlspecialchars($error); ?></span>
        </div>
    <?php endif; ?>

    <div class="card">
        <form action="<?= $action_url ?>" method="POST">
            <?php if ($is_edit): ?>
                <input type="hidden" name="id" value="<?= $usuario['id'] ?>">
            <?php endif; ?>

            <!-- SEÇÃO: DADOS DO USUÁRIO -->
            <div class="mb-4">
                <h3 class="card-title">👤 Dados do Usuário</h3>

                <div class="form-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label for="nome">Nome Completo *</label>
                        <input
                            type="text"
                            id="nome"
                            name="nome"
                            class="form-control"
                            placeholder="Digite o nome completo"
                            value="<?= $is_edit ? htmlspecialchars($usuario['nome']) : '' ?>"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label for="email">E-mail *</label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            class="form-control"
                            placeholder="exemplo@email.com"
                            value="<?= $is_edit ? htmlspecialchars($usuario['email']) : '' ?>"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label for="nivel_acesso">Nível de Acesso *</label>
                        <select id="nivel_acesso" name="nivel_acesso" class="form-select" required>
                            <option value="admin" <?= $is_edit && $usuario['nivel_acesso'] == 'admin' ? 'selected' : '' ?>>Administrador</option>
                            <option value="tecnico" <?= $is_edit && $usuario['nivel_acesso'] == 'tecnico' ? 'selected' : '' ?>>Técnico</option>
                            <option value="usuario" <?= $is_edit && $usuario['nivel_acesso'] == 'usuario' ? 'selected' : '' ?>>Padrão (Usuário)</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- SEÇÃO: SEGURANÇA -->
            <div class="mb-4">
                <h3 class="card-title">🔒 Segurança</h3>
                
                <?php if ($is_edit): ?>
                    <div class="alert alert-info m-0">
                        <span>ℹ️ Para alterar a senha deste usuário, utilize o botão <strong>Resetar Senha</strong> na listagem de usuários.</span>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info m-0">
                        <span>ℹ️ A senha padrão para novos usuários é: <strong>12345678</strong>. O usuário deverá alterá-la no primeiro acesso.</span>
                    </div>
                <?php endif; ?>
            </div>

            <!-- AÇÕES -->
            <div class="d-flex gap-2 mt-4 pt-4 border-top">
                <button type="submit" class="btn btn-primary">
                    <?= $is_edit ? '✓ Atualizar Usuário' : '✓ Cadastrar Usuário' ?>
                </button>
                <a href="<?= BASE_URL ?>usuarios" class="btn btn-secondary">
                    ✕ Cancelar
                </a>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
