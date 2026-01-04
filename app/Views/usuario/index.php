<?php
$current_page = 'usuarios';
require_once __DIR__ . '/../layout/main.php';
?>

<div class="container">
    <!-- CABEÇALHO COM TÍTULO E BOTÃO -->
    <div class="d-flex justify-between align-center mb-4 flex-wrap">
        <h1>👤 <?php echo htmlspecialchars($title ?? 'Usuários'); ?></h1>
        <a href="<?php echo BASE_URL; ?>usuarios/form" class="btn btn-primary">
            ➕ Novo Usuário
        </a>
    </div>

    <!-- LISTAGEM DE USUÁRIOS -->
    <?php if (empty($usuarios)): ?>
        <div class="card">
            <div class="alert alert-info m-0">
                <span>ℹ️ Nenhum usuário encontrado.</span>
            </div>
        </div>
    <?php else: ?>
        <!-- TABELA RESPONSIVA -->
        <div class="overflow-x-auto">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>👤 Nome</th>
                        <th>📧 E-mail</th>
                        <th>🔑 Nível de Acesso</th>
                        <th>⚙️ Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($usuarios as $usuario): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($usuario['id']); ?></td>
                            <td>
                                <strong><?php echo htmlspecialchars($usuario['nome']); ?></strong>
                            </td>
                            <td>
                                <span class="text-muted fs-sm">
                                    <?php echo htmlspecialchars($usuario['email']); ?>
                                </span>
                            </td>
                            <td>
                                <?php 
                                $nivel = $usuario['nivel_acesso'] ?? 'usuario';
                                if ($nivel === 'admin') {
                                    echo '<span class="badge" style="background-color: var(--primary-red);">Administrador</span>';
                                } elseif ($nivel === 'tecnico') {
                                    echo '<span class="badge" style="background-color: var(--info);">Técnico</span>';
                                } else {
                                    echo '<span class="badge" style="background-color: var(--bg-tertiary);">Padrão</span>';
                                }
                                ?>
                            </td>
                            <td>
                                <div class="d-flex gap-1 flex-wrap">
                                    <a href="<?php echo BASE_URL; ?>usuarios/form?id=<?php echo htmlspecialchars($usuario['id']); ?>" class="btn btn-info btn-sm" title="Editar Usuário">
                                        ✏️ Editar
                                    </a>
                                    
                                    <form action="<?php echo BASE_URL; ?>usuarios/resetar-senha" method="POST" class="d-inline">
                                        <input type="hidden" name="id" value="<?php echo htmlspecialchars($usuario['id']); ?>">
                                        <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('🔄 Resetar senha para 12345678?');" title="Resetar Senha">
                                            🔄 Resetar
                                        </button>
                                    </form>

                                    <form action="<?php echo BASE_URL; ?>usuarios/deletar" method="POST" class="d-inline" onsubmit="return confirm('⚠️ Tem certeza que deseja deletar este usuário? Esta ação não pode ser desfeita.');">
                                        <input type="hidden" name="id" value="<?php echo htmlspecialchars($usuario['id']); ?>">
                                        <button type="submit" class="btn btn-danger btn-sm" title="Deletar Usuário">
                                            🗑️ Deletar
                                        </button>
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
