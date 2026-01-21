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
                    <h5 class="mb-2">TOM – Taxas por Bandeira</h5>
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Débito (%)</label>
                            <input type="number" step="0.01" name="tom_taxa_debito" class="form-control" value="<?= htmlspecialchars($tom['taxa_debito'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label>Visa/Master (%)</label>
                            <input type="number" step="0.01" name="tom_taxa_visa_master" class="form-control" value="<?= htmlspecialchars($tom['bandeiras_taxas']['Visa'] ?? $tom['bandeiras_taxas']['Mastercard'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label>Elo/American Express (%)</label>
                            <input type="number" step="0.01" name="tom_taxa_elo_amex" class="form-control" value="<?= htmlspecialchars($tom['bandeiras_taxas']['Elo'] ?? $tom['bandeiras_taxas']['American Express'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label>Outras Bandeiras (%)</label>
                            <input type="number" step="0.01" name="tom_taxa_outros" class="form-control" value="<?= htmlspecialchars($tom['taxa_padrao'] ?? '') ?>">
                        </div>
                    </div>
                </div>
                <div class="mb-4">
                    <h5 class="mb-2">Mercado Pago – Taxas por Bandeira</h5>
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Débito (%)</label>
                            <input type="number" step="0.01" name="mp_taxa_debito" class="form-control" value="<?= htmlspecialchars($mp['taxa_debito'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label>Visa/Master (%)</label>
                            <input type="number" step="0.01" name="mp_taxa_visa_master" class="form-control" value="<?= htmlspecialchars($mp['bandeiras_taxas']['Visa'] ?? $mp['bandeiras_taxas']['Mastercard'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label>Elo/American Express (%)</label>
                            <input type="number" step="0.01" name="mp_taxa_elo_amex" class="form-control" value="<?= htmlspecialchars($mp['bandeiras_taxas']['Elo'] ?? $mp['bandeiras_taxas']['American Express'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label>Outras Bandeiras (%)</label>
                            <input type="number" step="0.01" name="mp_taxa_outros" class="form-control" value="<?= htmlspecialchars($mp['taxa_padrao'] ?? '') ?>">
                        </div>
                    </div>
                </div>
                <div class="mb-4">
                    <h5 class="mb-2">Moderninha – Taxas por Bandeira</h5>
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Débito (%)</label>
                            <input type="number" step="0.01" name="mod_taxa_debito" class="form-control" value="<?= htmlspecialchars($mod['taxa_debito'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label>Visa/Master (%)</label>
                            <input type="number" step="0.01" name="mod_taxa_visa_master" class="form-control" value="<?= htmlspecialchars($mod['bandeiras_taxas']['Visa'] ?? $mod['bandeiras_taxas']['Mastercard'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label>Elo/American Express (%)</label>
                            <input type="number" step="0.01" name="mod_taxa_elo_amex" class="form-control" value="<?= htmlspecialchars($mod['bandeiras_taxas']['Elo'] ?? $mod['bandeiras_taxas']['American Express'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label>Outras Bandeiras (%)</label>
                            <input type="number" step="0.01" name="mod_taxa_outros" class="form-control" value="<?= htmlspecialchars($mod['taxa_padrao'] ?? '') ?>">
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Salvar</button>
            </form>
        </div>
    </div>
</div>
