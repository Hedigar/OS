<?php
$current_page = 'configuracoes_pagamentos';
require_once __DIR__ . '/../layout/main.php';
?>
<div class="container mt-4">
    <h2><?= $title ?></h2>
    <?php if (!empty($_SESSION['success'])): ?>
        <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php endif; ?>
    <?php if (!empty($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form action="<?= BASE_URL ?>configuracoes/pagamentos/salvar" method="POST">
                <div class="mb-3">
                    <label class="form-label">Máquinas habilitadas</label>
                    <?php
                        $cfg = $config ?? [];
                        $enabled = [];
                        if (!empty($cfg['maquinas'])) {
                            foreach ($cfg['maquinas'] as $m) { if (!empty($m['habilitada'])) { $enabled[] = $m['nome']; } }
                        }
                        function isEnabled($enabled, $nome) { return in_array($nome, $enabled, true); }
                        function getProvider($cfg, $nome) {
                            if (!empty($cfg['maquinas'])) { foreach ($cfg['maquinas'] as $m) { if (($m['nome'] ?? '') === $nome) return $m; } }
                            return [];
                        }
                        $tom = getProvider($cfg, 'TOM');
                        $mp = getProvider($cfg, 'Mercado Pago');
                        $mod = getProvider($cfg, 'Moderninha');
                    ?>
                    <div class="d-flex gap-3 flex-wrap">
                        <label class="d-flex align-items-center gap-2">
                            <input type="checkbox" name="maquinas_enabled[]" value="TOM" <?= isEnabled($enabled,'TOM')?'checked':''; ?>> TOM
                        </label>
                        <label class="d-flex align-items-center gap-2">
                            <input type="checkbox" name="maquinas_enabled[]" value="Mercado Pago" <?= isEnabled($enabled,'Mercado Pago')?'checked':''; ?>> Mercado Pago
                        </label>
                        <label class="d-flex align-items-center gap-2">
                            <input type="checkbox" name="maquinas_enabled[]" value="Moderninha" <?= isEnabled($enabled,'Moderninha')?'checked':''; ?>> Moderninha
                        </label>
                    </div>
                </div>
                <div class="mb-4">
                    <h5 class="mb-2">TOM – Taxas</h5>
                    <div class="mt-3">
                        <h6>Crédito – Visa/Master</h6>
                        <div class="form-grid">
                            <div class="form-group">
                                <label>Débito – Visa/Master (%)</label>
                                <input type="number" step="0.01" name="tom_vm_debito" class="form-control" value="<?= htmlspecialchars($tom['debito_grupos']['visa_master'] ?? '') ?>">
                            </div>
                        </div>
                        <div class="form-grid">
                            <?php for ($i=1; $i<=12; $i++): ?>
                                <div class="form-group">
                                    <label><?php echo $i; ?>x (%)</label>
                                    <input type="number" step="0.01" name="tom_vm_<?php echo $i; ?>x" class="form-control" value="<?= htmlspecialchars($tom['credito_taxas']['visa_master'][$i] ?? '') ?>">
                                </div>
                            <?php endfor; ?>
                        </div>
                    </div>
                    <div class="mt-3">
                        <h6>Crédito – Elo/American Express</h6>
                        <div class="form-grid">
                            <div class="form-group">
                                <label>Débito – Elo/American Express (%)</label>
                                <input type="number" step="0.01" name="tom_ea_debito" class="form-control" value="<?= htmlspecialchars($tom['debito_grupos']['elo_amex'] ?? '') ?>">
                            </div>
                        </div>
                        <div class="form-grid">
                            <?php for ($i=1; $i<=12; $i++): ?>
                                <div class="form-group">
                                    <label><?php echo $i; ?>x (%)</label>
                                    <input type="number" step="0.01" name="tom_ea_<?php echo $i; ?>x" class="form-control" value="<?= htmlspecialchars($tom['credito_taxas']['elo_amex'][$i] ?? '') ?>">
                                </div>
                            <?php endfor; ?>
                        </div>
                    </div>
                </div>
                <div class="mb-4">
                    <h5 class="mb-2">Mercado Pago – Taxas</h5>
                    <div class="mt-3">
                        <h6>Crédito – Visa/Master</h6>
                        <div class="form-grid">
                            <div class="form-group">
                                <label>Débito – Visa/Master (%)</label>
                                <input type="number" step="0.01" name="mp_vm_debito" class="form-control" value="<?= htmlspecialchars($mp['debito_grupos']['visa_master'] ?? '') ?>">
                            </div>
                        </div>
                        <div class="form-grid">
                            <?php for ($i=1; $i<=12; $i++): ?>
                                <div class="form-group">
                                    <label><?php echo $i; ?>x (%)</label>
                                    <input type="number" step="0.01" name="mp_vm_<?php echo $i; ?>x" class="form-control" value="<?= htmlspecialchars($mp['credito_taxas']['visa_master'][$i] ?? '') ?>">
                                </div>
                            <?php endfor; ?>
                        </div>
                    </div>
                    <div class="mt-3">
                        <h6>Crédito – Elo/American Express</h6>
                        <div class="form-grid">
                            <div class="form-group">
                                <label>Débito – Elo/American Express (%)</label>
                                <input type="number" step="0.01" name="mp_ea_debito" class="form-control" value="<?= htmlspecialchars($mp['debito_grupos']['elo_amex'] ?? '') ?>">
                            </div>
                        </div>
                        <div class="form-grid">
                            <?php for ($i=1; $i<=12; $i++): ?>
                                <div class="form-group">
                                    <label><?php echo $i; ?>x (%)</label>
                                    <input type="number" step="0.01" name="mp_ea_<?php echo $i; ?>x" class="form-control" value="<?= htmlspecialchars($mp['credito_taxas']['elo_amex'][$i] ?? '') ?>">
                                </div>
                            <?php endfor; ?>
                        </div>
                    </div>
                </div>
                <div class="mb-4">
                    <h5 class="mb-2">Moderninha – Taxas</h5>
                    <div class="mt-3">
                        <h6>Crédito – Visa/Master</h6>
                        <div class="form-grid">
                            <div class="form-group">
                                <label>Débito – Visa/Master (%)</label>
                                <input type="number" step="0.01" name="mod_vm_debito" class="form-control" value="<?= htmlspecialchars($mod['debito_grupos']['visa_master'] ?? '') ?>">
                            </div>
                        </div>
                        <div class="form-grid">
                            <?php for ($i=1; $i<=12; $i++): ?>
                                <div class="form-group">
                                    <label><?php echo $i; ?>x (%)</label>
                                    <input type="number" step="0.01" name="mod_vm_<?php echo $i; ?>x" class="form-control" value="<?= htmlspecialchars($mod['credito_taxas']['visa_master'][$i] ?? '') ?>">
                                </div>
                            <?php endfor; ?>
                        </div>
                    </div>
                    <div class="mt-3">
                        <h6>Crédito – Elo/American Express</h6>
                        <div class="form-grid">
                            <div class="form-group">
                                <label>Débito – Elo/American Express (%)</label>
                                <input type="number" step="0.01" name="mod_ea_debito" class="form-control" value="<?= htmlspecialchars($mod['debito_grupos']['elo_amex'] ?? '') ?>">
                            </div>
                        </div>
                        <div class="form-grid">
                            <?php for ($i=1; $i<=12; $i++): ?>
                                <div class="form-group">
                                    <label><?php echo $i; ?>x (%)</label>
                                    <input type="number" step="0.01" name="mod_ea_<?php echo $i; ?>x" class="form-control" value="<?= htmlspecialchars($mod['credito_taxas']['elo_amex'][$i] ?? '') ?>">
                                </div>
                            <?php endfor; ?>
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Salvar</button>
            </form>
        </div>
    </div>
</div>
