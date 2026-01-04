<?php
$current_page = 'configuracoes';
require_once __DIR__ . '/../../layout/main.php';
?>

<div class="container">
    <!-- CABEÇALHO COM TÍTULO E BOTÃO -->
    <div class="d-flex justify-between align-center mb-4 flex-wrap">
        <div>
            <h1>📦 <?php echo htmlspecialchars($title ?? 'Produtos e Serviços'); ?></h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>dashboard">Home</a></li>
                    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>configuracoes">Configurações</a></li>
                    <li class="breadcrumb-item active">Produtos e Serviços</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= BASE_URL ?>configuracoes" class="btn btn-secondary">
                ⬅️ Voltar
            </a>
            <a href="<?= BASE_URL ?>configuracoes/produtos-servicos/form" class="btn btn-primary">
                ➕ Novo Item
            </a>
        </div>
    </div>

    <!-- ALERTAS -->
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success mb-4">
            <span>✅ <?= $_SESSION['success']; unset($_SESSION['success']); ?></span>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger mb-4">
            <span>❌ <?= $_SESSION['error']; unset($_SESSION['error']); ?></span>
        </div>
    <?php endif; ?>

    <div class="grid-search mb-4" style="grid-template-columns: 1fr 1fr; align-items: stretch;">
        <!-- CARD DE MARGEM DE LUCRO -->
        <div class="card m-0">
            <form action="<?= BASE_URL ?>configuracoes/salvar-porcentagem" method="POST">
                <label class="form-label text-muted small fw-bold">📈 MARGEM DE LUCRO PADRÃO (%)</label>
                <div class="d-flex gap-2">
                    <input type="number" step="0.01" name="porcentagem_venda" class="form-control" value="<?= $porcentagem ?>" required>
                    <button type="submit" class="btn btn-success" title="Salvar nova margem">
                        💾 Salvar
                    </button>
                </div>
                <p class="text-muted fs-sm mt-2 mb-0">Usada para sugerir o preço de venda automaticamente.</p>
            </form>
        </div>

        <!-- CARD DE ATUALIZAÇÃO EM MASSA -->
        <div class="card m-0 d-flex flex-column justify-center">
            <p class="small text-muted mb-2">Aplicar margem de <strong><?= $porcentagem ?>%</strong> em todos os produtos?</p>
            <form action="<?= BASE_URL ?>configuracoes/atualizar-precos-massa" method="POST" onsubmit="return confirm('⚠️ Atenção: Isso irá recalcular o preço de venda de TODOS os produtos ativos. Deseja continuar?')">
                <button type="submit" class="btn btn-secondary w-100">
                    🪄 Recalcular Todos os Preços
                </button>
            </form>
        </div>
    </div>

    <!-- LISTAGEM DE ITENS -->
    <?php if (empty($itens)): ?>
        <div class="card">
            <div class="alert alert-info m-0">
                <span>ℹ️ Nenhum item encontrado.</span>
            </div>
        </div>
    <?php else: ?>
        <!-- TABELA RESPONSIVA -->
        <div class="overflow-x-auto">
            <table class="table">
                <thead>
                    <tr>
                        <th>📦 Item</th>
                        <th>🏷️ Tipo</th>
                        <th>💰 Custo</th>
                        <th>📈 Venda</th>
                        <th>⚙️ Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($itens as $item): ?>
                        <tr>
                            <td>
                                <strong><?= htmlspecialchars($item['nome']) ?></strong>
                                <?php if($item['mao_de_obra'] > 0): ?>
                                    <br><small class="text-muted">Mão de Obra: R$ <?= number_format($item['mao_de_obra'], 2, ',', '.') ?></small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge" style="background-color: <?= $item['tipo'] == 'produto' ? 'var(--info)' : 'var(--warning)' ?>;">
                                    <?= $item['tipo'] == 'produto' ? '🛠️ Produto' : '🔧 Serviço' ?>
                                </span>
                            </td>
                            <td>
                                <span class="text-muted">R$ <?= number_format($item['custo'], 2, ',', '.') ?></span>
                            </td>
                            <td>
                                <strong class="text-success">R$ <?= number_format($item['valor_venda'], 2, ',', '.') ?></strong>
                            </td>
                            <td>
                                <div class="d-flex gap-1 flex-wrap">
                                    <a href="<?= BASE_URL ?>configuracoes/produtos-servicos/form?id=<?= $item['id'] ?>" class="btn btn-info btn-sm" title="Editar">
                                        ✏️ Editar
                                    </a>
                                    <form action="<?= BASE_URL ?>configuracoes/produtos-servicos/deletar" method="POST" class="d-inline" onsubmit="return confirm('⚠️ Tem certeza que deseja deletar este item?');">
                                        <input type="hidden" name="id" value="<?= $item['id'] ?>">
                                        <button type="submit" class="btn btn-danger btn-sm" title="Deletar">
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

<?php require_once __DIR__ . '/../../layout/footer.php'; ?>
