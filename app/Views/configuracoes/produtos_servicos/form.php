<?php
$is_edit = isset($item);
$action_url = BASE_URL . 'configuracoes/produtos-servicos/' . ($is_edit ? 'atualizar' : 'salvar');
$title_text = $is_edit ? 'Editar Item: ' . htmlspecialchars($item['nome']) : 'Novo Item';
$current_page = 'configuracoes';
require_once __DIR__ . '/../../layout/main.php';
?>

<div class="container">
    <div class="d-flex justify-between align-center mb-4">
        <h1><?php echo htmlspecialchars($title_text); ?></h1>
        <a href="<?= BASE_URL ?>configuracoes/produtos-servicos" class="btn btn-secondary btn-sm">← Voltar</a>
    </div>

    <div class="card">
        <form action="<?= $action_url ?>" method="POST" id="formItem">
            <?php if ($is_edit): ?>
                <input type="hidden" name="id" value="<?= $item['id'] ?>">
            <?php endif; ?>

            <!-- SEÇÃO: DADOS DO ITEM -->
            <div class="mb-4">
                <h3 class="card-title">📦 Dados do Item</h3>

                <div class="form-grid" style="display: grid; grid-template-columns: 2fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label for="nome">Nome do Item *</label>
                        <input
                            type="text"
                            name="nome"
                            id="nome"
                            class="form-control"
                            value="<?= $item['nome'] ?? '' ?>"
                            required
                            placeholder="Ex: SSD 240GB, FORMATAÇÃO"
                        >
                        <small class="text-muted fs-sm">O nome será salvo em letras maiúsculas para padronização.</small>
                    </div>

                    <div class="form-group">
                        <label for="tipo">Tipo *</label>
                        <select name="tipo" id="tipo" class="form-select" required>
                            <option value="produto" <?= (isset($item) && $item['tipo'] == 'produto') ? 'selected' : '' ?>>🛠️ Produto (Peça)</option>
                            <option value="servico" <?= (isset($item) && $item['tipo'] == 'servico') ? 'selected' : '' ?>>🔧 Serviço (Mão de Obra)</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- SEÇÃO: VALORES -->
            <div class="mb-4">
                <h3 class="card-title">💰 Valores</h3>

                <div class="form-grid" style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label for="custo">Custo (R$)</label>
                        <input
                            type="number"
                            step="0.01"
                            name="custo"
                            id="custo"
                            class="form-control"
                            value="<?= $item['custo'] ?? '' ?>"
                            placeholder="0,00"
                        >
                    </div>

                    <div class="form-group">
                        <label for="valor_venda">Valor de Venda (R$)</label>
                        <input
                            type="number"
                            step="0.01"
                            name="valor_venda"
                            id="valor_venda"
                            class="form-control"
                            value="<?= $item['valor_venda'] ?? '' ?>"
                            placeholder="0,00"
                        >
                        <small class="text-info fs-sm" id="calcHint" style="display:none;">✨ Calculado com <?= $porcentagem ?>% de margem.</small>
                    </div>

                    <div class="form-group">
                        <label for="mao_de_obra">Mão de Obra (R$)</label>
                        <input
                            type="number"
                            step="0.01"
                            name="mao_de_obra"
                            id="mao_de_obra"
                            class="form-control"
                            value="<?= $item['mao_de_obra'] ?? '' ?>"
                            placeholder="0,00"
                        >
                    </div>
                </div>
            </div>

            <!-- AÇÕES -->
            <div class="d-flex gap-2 mt-4 pt-4 border-top">
                <button type="submit" class="btn btn-primary">
                    ✓ Salvar Item
                </button>
                <a href="<?= BASE_URL ?>configuracoes/produtos-servicos" class="btn btn-secondary">
                    ✕ Cancelar
                </a>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const custoInput = document.getElementById('custo');
    const vendaInput = document.getElementById('valor_venda');
    const calcHint = document.getElementById('calcHint');
    const porcentagem = <?= $porcentagem ?>;

    function calcularVenda() {
        const custo = parseFloat(custoInput.value) || 0;
        if (custo > 0 && porcentagem > 0) {
            const venda = custo * (1 + (porcentagem / 100));
            vendaInput.value = venda.toFixed(2);
            calcHint.style.display = 'block';
        } else {
            calcHint.style.display = 'none';
        }
    }

    custoInput.addEventListener('input', calcularVenda);
});
</script>

<?php require_once __DIR__ . '/../../layout/footer.php'; ?>
