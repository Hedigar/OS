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

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger mb-4">
            <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <div class="card mb-4">
        <h2 class="card-title">Impostos (Nota Fiscal)</h2>
        <p class="text-muted mb-4">Defina as porcentagens de impostos que serão descontadas do lucro quando a opção "Emitir Nota Fiscal" for selecionada em uma OS ou Atendimento.</p>
        
        <form action="<?php echo BASE_URL; ?>configuracoes/salvar-financeiro" method="POST">
            <div class="form-grid mb-3">
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

            <div class="text-end">
                <button type="submit" class="btn btn-primary">💾 Salvar Impostos</button>
            </div>
        </form>
    </div>

    <div class="card">
        <h2 class="card-title">🔒 Travamento e Fechamento de Competência (DRE)</h2>
        <p class="text-muted mb-4">Uma vez fechado o período fiscal, nenhum custo ou item poderá ser adicionado, editado ou excluído para aquela competência. Isso impede duplicidade de custos e alterações retroativas após a distribuição de lucros.</p>
        
        <div class="row" style="display: flex; gap: 20px; flex-wrap: wrap;">
            <!-- Form de Fechamento -->
            <div style="flex: 1; min-width: 300px;">
                <h4 class="mb-3">Fechar Novo Período</h4>
                <form action="<?php echo BASE_URL; ?>configuracoes/fechar-periodo" method="POST">
                    <div class="form-group mb-3">
                        <label>Mês de Competência</label>
                        <select name="mes" class="form-control" required>
                            <?php for($m=1; $m<=12; $m++): ?>
                                <option value="<?php echo $m; ?>" <?php echo (int)date('m') === $m ? 'selected' : ''; ?>>
                                    <?php echo str_pad($m, 2, '0', STR_PAD_LEFT); ?>
                                </option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label>Ano de Competência</label>
                        <select name="ano" class="form-control" required>
                            <?php for($y=date('Y')-2; $y<=date('Y')+1; $y++): ?>
                                <option value="<?php echo $y; ?>" <?php echo (int)date('Y') === $y ? 'selected' : ''; ?>>
                                    <?php echo $y; ?>
                                </option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label>Observações</label>
                        <textarea name="observacoes" class="form-control" rows="3" placeholder="Ex: Fechamento mensal oficial, lucros distribuídos."></textarea>
                    </div>
                    <button type="submit" class="btn btn-danger w-100">🔒 Executar Fechamento Fiscal</button>
                </form>
            </div>

            <!-- Listagem de Fechamentos -->
            <div style="flex: 2; min-width: 300px;">
                <h4 class="mb-3">Histórico de Períodos Selados</h4>
                <?php if (empty($fechamentos)): ?>
                    <p class="text-muted text-center py-4">Nenhum período fiscal foi fechado até o momento.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Período</th>
                                    <th>Fechado em</th>
                                    <th>Usuário</th>
                                    <th>Observações</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($fechamentos as $f): ?>
                                    <tr>
                                        <td><strong><?php echo str_pad($f['mes'], 2, '0', STR_PAD_LEFT) . '/' . $f['ano']; ?></strong></td>
                                        <td><?php echo date('d/m/Y H:i', strtotime($f['fechado_em'])); ?></td>
                                        <td><?php echo htmlspecialchars($f['usuario_nome'] ?? 'Sistema'); ?></td>
                                        <td><?php echo htmlspecialchars($f['observacoes'] ?? ''); ?></td>
                                        <td>
                                            <form action="<?php echo BASE_URL; ?>configuracoes/reabrir-periodo" method="POST" onsubmit="return confirm('ATENÇÃO: Reabrir este período permitirá modificações nos custos de competência retroativos. Deseja prosseguir?');" style="display:inline;">
                                                <input type="hidden" name="mes" value="<?php echo $f['mes']; ?>">
                                                <input type="hidden" name="ano" value="<?php echo $f['ano']; ?>">
                                                <button type="submit" class="btn btn-sm btn-outline-warning">🔓 Reabrir</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
