<?php
$current_page = 'configuracoes';
require_once __DIR__ . '/../layout/main.php';
?>

<div class="container">
    <div class="d-flex justify-between align-center mb-4">
        <h1>⚙️ Configurações Financeiras</h1>
        <a href="<?php echo BASE_URL; ?>configuracoes" class="btn btn-secondary">← Voltar</a>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success mb-4">
            <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>

    <div class="card">
        <h2 class="card-title">Impostos (Nota Fiscal)</h2>
        <p class="text-muted mb-4">Defina as porcentagens de impostos que serão descontadas do lucro quando a opção "Emitir Nota Fiscal" for selecionada em uma OS ou Atendimento.</p>
        
        <form action="<?php echo BASE_URL; ?>configuracoes/salvar-financeiro" method="POST">
            <div class="form-grid">
                <div class="form-group">
                    <label>Imposto sobre Produtos (%)</label>
                    <input type="number" name="nf_porcentagem_produtos" class="form-control" step="0.01" value="<?php echo htmlspecialchars($nf_porcentagem_produtos); ?>" required>
                    <small class="text-muted">Padrão sugerido: 3%</small>
                </div>
                <div class="form-group">
                    <label>Imposto sobre Serviços (%)</label>
                    <input type="number" name="nf_porcentagem_servicos" class="form-control" step="0.01" value="<?php echo htmlspecialchars($nf_porcentagem_servicos); ?>" required>
                    <small class="text-muted">Padrão sugerido: 6%</small>
                </div>
            </div>

            <div class="mt-4 text-end">
                <button type="submit" class="btn btn-primary">💾 Salvar Configurações</button>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
