<?php
// Inclui o layout principal
require_once __DIR__ . '/../layout/main.php';
?>

<div class="container mt-5">
    <div class="d-flex justify-between align-center mb-4">
        <h2><i class="fas fa-print"></i> <?= $title ?></h2>
        <a href="<?= BASE_URL ?>configuracoes" class="btn btn-secondary">Voltar</a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="<?= BASE_URL ?>configuracoes/salvar-impressao" method="POST">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-4">
                            <label for="fonte_tamanho" class="fw-bold">Tamanho da Fonte na Impressão (px)</label>
                            <p class="text-muted small">Define o tamanho do texto nos documentos PDF da OS e Orçamento.</p>
                            <input type="number" name="fonte_tamanho" id="fonte_tamanho" class="form-control" value="<?= $fonte_tamanho ?? '12' ?>" min="8" max="24">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-4">
                            <label class="fw-bold">Visibilidade de Campos</label>
                            <p class="text-muted small">Escolha o que deve aparecer nos documentos impressos.</p>
                            <div class="form-check form-switch">
                                <input type="checkbox" name="exibir_observacoes" id="exibir_observacoes" class="form-check-input" value="1" <?= ($exibir_observacoes ?? '1') == '1' ? 'checked' : '' ?>>
                                <label class="form-check-label" for="exibir_observacoes">Exibir Defeito Relatado e Laudo Técnico</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group mb-4">
                    <label for="texto_observacoes" class="fw-bold">Texto de Observações / Termos de Garantia</label>
                    <p class="text-muted small">Este texto aparecerá no rodapé da Ordem de Serviço e do Orçamento.</p>
                    <textarea name="texto_observacoes" id="texto_observacoes" class="form-control" rows="6"><?= $texto_observacoes ?? '' ?></textarea>
                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-save"></i> Salvar Configurações
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
