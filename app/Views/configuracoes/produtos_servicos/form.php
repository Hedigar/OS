<?php require_once __DIR__ . '/../../layout/main.php'; ?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
           <div class="card dark-form">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-white"><?= $title ?></h5>
                    <a href="<?= BASE_URL ?>configuracoes/produtos-servicos" class="btn btn-sm btn-secondary">Voltar</a>
                </div>
                <div class="card-body text-white">
                    <form action="<?= BASE_URL ?>configuracoes/produtos-servicos/<?= isset($item) ? 'atualizar' : 'salvar' ?>" method="POST" id="formItem">
                        <?php if (isset($item)): ?>
                            <input type="hidden" name="id" value="<?= $item['id'] ?>">
                        <?php endif; ?>

                        <div class="row mb-3">
                            <div class="col-md-8">
                                <label for="nome" class="form-label">Nome do Item (Padronizado)</label>
                                <input type="text" name="nome" id="nome" class="form-control" value="<?= $item['nome'] ?? '' ?>" required placeholder="Ex: SSD 240GB, FORMATAÇÃO">
                                <small class="text-muted">O nome será salvo em letras maiúsculas para padronização.</small>
                            </div>
                            <div class="col-md-4">
                                <label for="tipo" class="form-label">Tipo</label>
                                <select name="tipo" id="tipo" class="form-select" required>
                                    <option value="produto" <?= (isset($item) && $item['tipo'] == 'produto') ? 'selected' : '' ?>>Produto (Peça)</option>
                                    <option value="servico" <?= (isset($item) && $item['tipo'] == 'servico') ? 'selected' : '' ?>>Serviço (Mão de Obra)</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="custo" class="form-label">Custo (R$)</label>
                                <input type="number" step="0.01" name="custo" id="custo" class="form-control" value="<?= $item['custo'] ?? '' ?>" placeholder="0,00">
                            </div>
                            <div class="col-md-4">
                                <label for="valor_venda" class="form-label">Valor de Venda (R$)</label>
                                <input type="number" step="0.01" name="valor_venda" id="valor_venda" class="form-control" value="<?= $item['valor_venda'] ?? '' ?>" placeholder="0,00">
                                <small class="text-info" id="calcHint" style="display:none;">Calculado com <?= $porcentagem ?>% de margem.</small>
                            </div>
                            <div class="col-md-4">
                                <label for="mao_de_obra" class="form-label">Mão de Obra (R$)</label>
                                <input type="number" step="0.01" name="mao_de_obra" id="mao_de_obra" class="form-control" value="<?= $item['mao_de_obra'] ?? '' ?>" placeholder="0,00">
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">Salvar Item</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
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
    
    // Se for um novo cadastro e o custo for alterado, calcula. 
    // Se for edição, o usuário pode sobrescrever.
});
</script>
