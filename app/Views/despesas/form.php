<div class="container">
    <h1><?php echo htmlspecialchars($title ?? 'Despesa'); ?></h1>
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    <?php
        $isEdit = !empty($despesa) && isset($despesa['id']);
        $action = $isEdit ? BASE_URL.'despesas/atualizar' : BASE_URL.'despesas/salvar';
        $d = $despesa ?? [];
    ?>
    <form method="post" action="<?php echo $action; ?>">
        <?php if ($isEdit): ?>
            <input type="hidden" name="id" value="<?php echo (int)$d['id']; ?>">
        <?php endif; ?>
        <div class="row">
            <div class="col-md-4 mb-3">
                <label class="form-label">Data</label>
                <input type="date" name="data_despesa" class="form-control" value="<?php echo htmlspecialchars($d['data_despesa'] ?? date('Y-m-d')); ?>">
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Categoria</label>
                <select name="categoria_id" class="form-control mb-2">
                    <option value="">Selecione uma existente...</option>
                    <?php foreach (($categorias ?? []) as $cat): ?>
                        <option value="<?php echo (int)$cat['id']; ?>" <?php echo isset($d['categoria_id']) && (int)$d['categoria_id'] === (int)$cat['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($cat['nome']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <input type="text" name="nova_categoria" class="form-control" placeholder="Ou digite uma nova categoria aqui" value="<?php echo htmlspecialchars($nova_categoria ?? ''); ?>">
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Valor</label>
                <input type="text" name="valor" class="form-control" value="<?php echo htmlspecialchars(isset($d['valor']) ? number_format((float)$d['valor'], 2, ',', '.') : '0,00'); ?>">
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label">Descrição</label>
            <input type="text" name="descricao" class="form-control" value="<?php echo htmlspecialchars($d['descricao'] ?? ''); ?>">
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Método de Pagamento</label>
                <?php
                    $metodos = ['dinheiro'=>'Dinheiro','cartao'=>'Cartão','pix'=>'Pix','transferencia'=>'Transferência','boleto'=>'Boleto','outro'=>'Outro'];
                    $metSel = $d['metodo_pagamento'] ?? 'outro';
                ?>
                <select name="metodo_pagamento" class="form-control">
                    <?php foreach ($metodos as $k => $v): ?>
                        <option value="<?php echo $k; ?>" <?php echo $metSel === $k ? 'selected' : ''; ?>><?php echo $v; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Status do Pagamento</label>
                <?php
                    $statuses = ['pendente' => 'Pendente','pago' => 'Pago','parcial' => 'Parcial'];
                    $stSel = $d['status_pagamento'] ?? 'pendente';
                ?>
                <select name="status_pagamento" class="form-control">
                    <?php foreach ($statuses as $k => $v): ?>
                        <option value="<?php echo $k; ?>" <?php echo $stSel === $k ? 'selected' : ''; ?>><?php echo $v; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label">Observações</label>
            <textarea name="observacoes" class="form-control" rows="4"><?php echo htmlspecialchars($d['observacoes'] ?? ''); ?></textarea>
        </div>
        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary"><?php echo $isEdit ? 'Atualizar' : 'Salvar'; ?></button>
            <a href="<?php echo BASE_URL; ?>despesas" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</div>
