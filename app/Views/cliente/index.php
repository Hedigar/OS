<?php
$current_page = 'clientes';
require_once __DIR__ . '/../layout/main.php';
?>

<div class="container">
    <!-- CABEÇALHO COM TÍTULO E BOTÃO -->
    <div class="d-flex justify-between align-center mb-4 flex-wrap">
        <h1><?php echo htmlspecialchars($title ?? 'Clientes'); ?></h1>
        <a href="<?php echo BASE_URL; ?>clientes/criar" class="btn btn-primary">
            ➕ Novo Cliente
        </a>
    </div>

    <!-- CARD DE BUSCA -->
    <div class="card mb-4">
        <form action="<?php echo BASE_URL; ?>clientes" method="GET">
            <div class="grid-search">
                <div class="form-group mb-0">
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
            <div class="alert alert-info m-0">
                <span>ℹ️ Nenhum cliente encontrado. <a href="<?php echo BASE_URL; ?>clientes/criar" class="text-red">Criar novo cliente</a></span>
            </div>
        </div>
    <?php else: ?>
        <!-- TABELA RESPONSIVA -->
        <div class="overflow-x-auto">
            <table class="table">
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
                                <span class="text-muted fs-sm">
                                    <?php echo htmlspecialchars($cliente['email'] ?? 'N/A'); ?>
                                </span>
                            </td>
                            <td>
                                <div class="d-flex gap-1 flex-wrap">
                                    <a href="<?php echo BASE_URL; ?>clientes/editar?id=<?php echo htmlspecialchars($cliente['id']); ?>" class="btn btn-info btn-sm" title="Editar Cliente">
                                        ✏️ Editar
                                    </a>
                                    <a href="<?php echo BASE_URL; ?>clientes/view?id=<?php echo htmlspecialchars($cliente['id']); ?>" class="btn btn-success btn-sm" title="Ver Ordens de Serviço">
                                        📋 Ordens
                                    </a>
                                    <form action="<?php echo BASE_URL; ?>clientes/deletar" method="POST" class="d-inline" onsubmit="return confirm('⚠️ Tem certeza que deseja deletar este cliente? Esta ação não pode ser desfeita.');">
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
            <div class="mt-4">
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
            </div>
        <?php endif; ?>

    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
