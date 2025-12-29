<?php
// Inclui o layout principal
require_once __DIR__ . '/../layout/main.php';
?>

<style>
    /* Tabela escura local (fundo muito escuro, textos brancos) */
    .dark-table { background-color: #050506 !important; border: 1px solid #0e0e10 !important; border-radius: 12px; }
    .dark-table .card-body { background: transparent !important; padding: 0.5rem !important; }
    .dark-table .table { background: transparent !important; margin-bottom: 0 !important; color: #ffffff !important; }
    .dark-table thead th, .dark-table tbody td { color: #ffffff !important; background: transparent !important; border-color: rgba(255,255,255,0.04) !important; }
    .dark-table .table-hover tbody tr:hover { background-color: #0f0f10 !important; }
    .dark-table .table .text-muted { color: rgba(255,255,255,0.75) !important; }
</style>
<?php
?>

<div class="container mt-5">

<div class="d-flex justify-content-between align-items-start mb-3 flex-wrap">
    <div class="me-3">
        <h2 class="fw-bold mb-0 text-white">
            <i class="fas fa-users text-primary me-2"></i><?= $title ?>
        </h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>dashboard">Home</a></li>
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>configuracoes">Configurações</a></li>
                <li class="breadcrumb-item active"><?= $title ?></li>
            </ol>
        </nav>
    </div>
    <div class="ms-auto" style="display:flex; align-items:flex-start; margin-top: 0.25rem;">
        <a href="<?= BASE_URL ?>usuarios/form" class="btn btn-secondary">Novo Usuário</a>
    </div>
</div>

    <?php if (empty($usuarios)): ?>
        <div class="alert alert-info">Nenhum usuário cadastrado.</div>
    <?php else: ?>
        <div class="card shadow-sm dark-table" style="background-color: var(--dark-secondary); border: 1px solid var(--dark-tertiary);">
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead style="background-color: var(--dark-tertiary);">
                        <tr>
                            <th class="text-white">ID</th>
                            <th class="text-white">Nome</th>
                            <th class="text-white">Email</th>
                            <th class="text-white">Nível de Acesso</th>
                            <th class="text-white">Ações</th>
                        </tr>
                    </thead>
            <tbody class="text-white">
                    <?php foreach ($usuarios as $usuario): ?>
                        <tr>
                            <td class="text-white"><?= $usuario['id'] ?></td>
                            <td class="text-white"><?= htmlspecialchars($usuario['nome']) ?></td>
                            <td class="text-white"><?= htmlspecialchars($usuario['email']) ?></td>
                            <td class="text-white">
                                <?php 
                                $nivel = $usuario['nivel_acesso'] ?? 'usuario';
                                if ($nivel === 'admin') echo 'Administrador';
                                elseif ($nivel === 'tecnico') echo 'Técnico';
                                else echo 'Padrão';
                                ?>
                            </td>
                            <td>
                                <a href="<?= BASE_URL ?>usuarios/form?id=<?= $usuario['id'] ?>" class="btn btn-sm btn-warning">Editar</a>
                                
                                <form action="<?= BASE_URL ?>usuarios/resetar-senha" method="POST" style="display:inline-block;">
                                    <input type="hidden" name="id" value="<?= $usuario['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-info" onclick="return confirm('Resetar senha para 12345678?');">Resetar Senha</button>
                                </form>

                                <form action="<?= BASE_URL ?>usuarios/deletar" method="POST" style="display:inline-block;">
                                    <input type="hidden" name="id" value="<?= $usuario['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza que deseja deletar este usuário?');">Deletar</button>
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
