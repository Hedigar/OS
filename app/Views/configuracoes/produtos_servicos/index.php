<?php require_once __DIR__ . '/../../layout/main.php'; ?>

<style>
    .action-buttons .btn {
        width: auto;
        height: auto;
        padding: 0.5rem 1rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        transition: all 0.2s;
        font-size: 0.85rem;
        gap: 0.5rem;
    }
    .action-buttons .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(238, 18, 18, 0.94);
    }
    .table-card {
        border: none;
        border-radius: 15px;
        overflow: hidden;
    }
    .config-card {
        border: none;
        border-radius: 15px;
        background: var(--dark-secondary);
        border: 1px solid var(--dark-tertiary);
    }
    /* Reforçar tabela totalmente escura apenas dentro de .dark-table */
    .dark-table {
        background-color: ##1A1F2E !important;
        border: 1px solid #0e0e10 !important;
        border-radius: 15px;
    }
    .dark-table .card-body {
        background: transparent !important;
        padding: 0.5rem !important;
    }
    .dark-table .table {
        background: transparent !important;
        margin-bottom: 0 !important;
        color: #ffffff !important;
    }
    .dark-table .table thead th,
    .dark-table .table tbody td {
        color: #ffffff !important;
        background: transparent !important;
        border-color: rgba(255,255,255,0.04) !important;
    }
    .dark-table .table-hover tbody tr:hover {
        background-color: #c90505ff !important;
    }
    .dark-table .table tbody tr {
        background-color: #000000ff !important;
    }
    .dark-table .table .text-muted {
        color: rgba(255,255,255,0.75) !important;
    }
    /* garantir botões/ícones legíveis dentro da tabela escura */
    .dark-table .btn,
    .dark-table .action-buttons .btn {
        color: #ffffff !important;
        border-color: rgba(233, 58, 58, 0.06) !important;
        background: transparent !important;
    }
    .dark-table .badge-type {
        padding: 6px 12px;
        border-radius: 20px;
        font-weight: 500;
        font-size: 0.75rem;
        text-transform: uppercase;
        background: rgba(255,255,255,0.06) !important;
        color: #ffffff !important;
    }
</style>

<div class="container-fluid px-4 mt-4">
    <!-- Cabeçalho -->
    <div class="row align-items-center mb-4">
        <div class="col-md-6">
            <h2 class="fw-bold mb-0 text-white">
                <i class="fas fa-boxes text-primary me-2"></i>Produtos e Serviços
            </h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>dashboard">Home</a></li>
                    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>configuracoes">Configurações</a></li>
                    <li class="breadcrumb-item active">Produtos e Serviços</li>
                </ol>
            </nav>
        </div>
        <div class="col-md-6 text-md-end mt-3 mt-md-0">
            <a href="<?= BASE_URL ?>configuracoes" class="btn btn-outline-secondary rounded-pill px-4 me-2">
                <i class="fas fa-chevron-left me-1"></i> Voltar
            </a>
            <a href="<?= BASE_URL ?>configuracoes/produtos-servicos/form" class="btn btn-outline-secondary rounded-pill px-4 me-2">
                <i class="fas fa-plus-circle me-1"></i> Novo Item
            </a>
        </div>
    </div>

    <!-- Alertas -->
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
            <i class="fas fa-check-circle me-2"></i> <?= $_SESSION['success']; unset($_SESSION['success']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i> <?= $_SESSION['error']; unset($_SESSION['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <!-- Coluna de Configurações -->
        <div class="col-lg-4 mb-4">
            <div class="card config-card shadow-sm h-100">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4"><i class="fas fa-cog text-secondary me-2"></i>Ajustes Globais</h5>
                    
                    <form action="<?= BASE_URL ?>configuracoes/salvar-porcentagem" method="POST" class="mb-4">
                        <label class="form-label text-muted small fw-bold">MARGEM DE LUCRO PADRÃO (%)</label>
                        <div class="input-group mb-3">
                            <input type="number" step="0.01" name="porcentagem_venda" class="form-control form-control-lg border-end-0" value="<?= $porcentagem ?>" required>
                            <span class="input-group-text bg-white border-start-0 text-muted fw-bold">%</span>
                            <button type="submit" class="btn btn-success px-4" title="Salvar nova margem">
                                <i class="fas fa-save"></i>
                            </button>
                        </div>
                        <p class="text-muted small">Esta margem é usada para sugerir o preço de venda automaticamente.</p>
                    </form>

                    <hr class="my-4 opacity-10">

                    <div class="bg-light p-3 rounded-3 border">
                        <h6 class="fw-bold mb-2"><i class="fas fa-sync-alt text-primary me-2"></i>Atualização em Massa</h6>
                        <p class="small text-muted mb-3">Deseja aplicar a margem de <strong><?= $porcentagem ?>%</strong> em todos os produtos já cadastrados?</p>
                        <form action="<?= BASE_URL ?>configuracoes/atualizar-precos-massa" method="POST" onsubmit="return confirm('Atenção: Isso irá recalcular o preço de venda de TODOS os produtos ativos com base no custo e na margem atual. Deseja continuar?')">
                            <button type="submit" class="btn btn-outline-primary btn-sm w-100 rounded-pill">
                                <i class="fas fa-magic me-1"></i> Recalcular Todos os Preços
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Coluna da Tabela -->
        <div class="col-lg-8 mb-4">
            <div class="card table-card shadow-sm dark-table">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                           <thead>
                                <tr>
                                    <th class="ps-4 py-3 border-0 text-white">ITEM</th>
                                    <th class="py-3 border-0 text-white">TIPO</th>
                                    <th class="py-3 border-0 text-white">CUSTO</th>
                                    <th class="py-3 border-0 text-white">VENDA</th>
                                    <th class="py-3 border-0 text-center text-white">AÇÕES</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($itens)): ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-5">
                                            <img src="https://cdn-icons-png.flaticon.com/512/4076/4076432.png" width="80" class="mb-3 opacity-25">
                                            <p class="text-muted">Nenhum item encontrado.</p>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($itens as $item): ?>
                                        <tr>
                                            <td class="ps-4">
                                                <div class="fw-bold text-white"><?= $item['nome'] ?></div>
                                                <?php if($item['mao_de_obra'] > 0): ?>
                                                    <small class="text-muted">Mão de Obra: R$ <?= number_format($item['mao_de_obra'], 2, ',', '.') ?></small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="badge-type <?= $item['tipo'] == 'produto' ? 'bg-info text-white' : 'bg-warning text-dark' ?>">
                                                    <i class="fas <?= $item['tipo'] == 'produto' ? 'fa-microchip' : 'fa-tools' ?> me-1"></i>
                                                    <?= $item['tipo'] ?>
                                                </span>
                                            </td>
                                            <td class="text-muted">R$ <?= number_format($item['custo'], 2, ',', '.') ?></td>
                                            <td>
                                                <span class="fw-bold text-success">R$ <?= number_format($item['valor_venda'], 2, ',', '.') ?></span>
                                            </td>
                                            <td class="text-center pe-4">
                                                <div class="action-buttons">
                                                    <a href="<?= BASE_URL ?>configuracoes/produtos-servicos/form?id=<?= $item['id'] ?>" class="btn btn-info btn-sm" title="Editar">
                                                        <i class="fas fa-pencil-alt"></i> Editar
                                                    </a>
                                                    <button type="button" class="btn btn-danger btn-sm" onclick="confirmarExclusao(<?= $item['id'] ?>, '<?= $item['nome'] ?>')" title="Excluir">
                                                        <i class="fas fa-trash-alt"></i> Excluir
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Exclusão (Bootstrap Nativo) -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; background-color: var(--dark-secondary) !important; border: 1px solid var(--dark-tertiary) !important; color: white !important;">
            <div class="modal-header border-0 pt-4 px-4">
                <h5 class="modal-title fw-bold" id="deleteModalLabel" style="color: white !important;"><i class="fas fa-trash-alt text-danger me-2"></i>Confirmar Exclusão</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body px-4 pb-4">
                <p class="text-secondary mb-0">Você tem certeza que deseja remover este item?</p>
                <div class="p-3 rounded-3 mt-3 border-start border-danger border-4" style="background-color: var(--dark-tertiary);">
                    <strong id="itemName" class="text-white"></strong>
                </div>
                <p class="text-danger small mt-3 mb-0"><i class="fas fa-info-circle me-1"></i> Esta ação não poderá ser revertida.</p>
            </div>
            <div class="modal-footer border-0 px-4 pb-4">
                <form action="<?= BASE_URL ?>configuracoes/produtos-servicos/deletar" method="POST" class="w-100 d-flex gap-2">
                    <input type="hidden" name="id" id="itemId">
                    <button type="button" class="btn btn-secondary flex-grow-1 rounded-pill fw-bold" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger flex-grow-1 rounded-pill fw-bold shadow-sm">Sim, Excluir</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function confirmarExclusao(id, nome) {
    document.getElementById('itemId').value = id;
    document.getElementById('itemName').innerText = nome;
    var myModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    myModal.show();
}
</script>
