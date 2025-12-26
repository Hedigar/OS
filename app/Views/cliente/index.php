<?php
$current_page = 'clientes';
require_once __DIR__ . '/../layout/main.php';
?>

<div class="container">
    <!-- CABEÇALHO COM TÍTULO E BOTÃO -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
        <h1><?php echo htmlspecialchars($title ?? 'Clientes'); ?></h1>
        <a href="<?php echo BASE_URL; ?>clientes/criar" class="btn btn-primary">
            ➕ Novo Cliente
        </a>
    </div>

    <!-- CARD DE BUSCA -->
    <div class="card" style="margin-bottom: 2rem;">
        <form action="<?php echo BASE_URL; ?>clientes" method="GET">
            <div style="display: grid; grid-template-columns: 1fr auto auto; gap: 1rem; align-items: end;">
                <div class="form-group" style="margin-bottom: 0;">
                    <label for="busca">🔍 Buscar Cliente</label>
                    <input
                        type="text"
                        id="busca"
                        name="busca"
                        class="form-control"
                        placeholder="Digite o nome ou CPF/CNPJ..."
                        value="<?php echo htmlspecialchars($busca ?? ''); ?>"
                    >
                </div>
                <button type="submit" class="btn btn-primary">Buscar</button>
                <?php if (!empty($busca)): ?>
                    <a href="<?php echo BASE_URL; ?>clientes" class="btn btn-secondary">Limpar</a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- LISTAGEM DE CLIENTES -->
    <?php if (empty($clientes)): ?>
        <div class="card">
            <div class="alert alert-info" style="margin: 0;">
                <span>ℹ️</span>
                <span>Nenhum cliente encontrado. <a href="<?php echo BASE_URL; ?>clientes/criar" style="color: var(--info); text-decoration: underline;">Criar novo cliente</a></span>
            </div>
        </div>
    <?php else: ?>
        <!-- TABELA RESPONSIVA -->
        <div style="overflow-x: auto;">
            <table>
                <thead>
                    <tr>
                        <th>👤 Nome</th>
                        <th>📄 Documento</th>
                        <th>📞 Telefone</th>
                        <th>📧 E-mail</th>
                        <th>⚙️ Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($clientes as $cliente): ?>
                        <tr>
                            <td>
                                <strong><?php echo htmlspecialchars($cliente['nome_completo']); ?></strong>
                            </td>
                            <td>
                                <?php echo htmlspecialchars($cliente['documento'] ?? 'N/A'); ?>
                            </td>
                            <td>
                                <?php echo htmlspecialchars($cliente['telefone_principal'] ?? 'N/A'); ?>
                            </td>
                            <td>
                                <span style="color: var(--text-muted); font-size: 0.85rem;">
                                    <?php echo htmlspecialchars($cliente['email'] ?? 'N/A'); ?>
                                </span>
                            </td>
                            <td>
                                <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                                    <a href="<?php echo BASE_URL; ?>clientes/editar?id=<?php echo htmlspecialchars($cliente['id']); ?>" class="btn btn-info btn-sm" title="Editar Cliente">
                                        ✏️ Editar
                                    </a>
                                    <a href="<?php echo BASE_URL; ?>ordens?cliente_id=<?php echo htmlspecialchars($cliente['id']); ?>" class="btn btn-success btn-sm" title="Ver Ordens de Serviço">
                                        📋 Ordens
                                    </a>
                                    <form action="<?php echo BASE_URL; ?>clientes/deletar" method="POST" style="display: inline;" onsubmit="return confirm('⚠️ Tem certeza que deseja deletar este cliente? Esta ação não pode ser desfeita.');>
                                        <input type="hidden" name="id" value="<?php echo htmlspecialchars($cliente['id']); ?>">
                                        <button type="submit" class="btn btn-danger btn-sm" title="Deletar Cliente">
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

        <!-- PAGINAÇÃO -->
        <?php if (($totalPaginas ?? 0) > 1): ?>
            <nav style="margin-top: 2rem;">
                <div class="pagination">
                    <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                        <?php
                            $url = BASE_URL . 'clientes?pagina=' . $i;
                            if (!empty($busca)) {
                                $url .= '&busca=' . urlencode($busca);
                            }
                            $is_active = $i == ($paginaAtual ?? 1);
                        ?>
                        <a href="<?php echo htmlspecialchars($url); ?>" class="<?php echo $is_active ? 'active' : ''; ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>
                </div>
            </nav>
        <?php endif; ?>

    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
