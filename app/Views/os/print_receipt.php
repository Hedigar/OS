<?php
// Template de impressão: recepção (A4 dividido em 2 vias)
$ordem = $ordem ?? [];
$itens = $itens ?? [];

function safe_text(array $arr, string $key, string $default = ''): string
{
    $val = $arr[$key] ?? $default;
    return htmlspecialchars((string)$val, ENT_QUOTES, 'UTF-8');
}

function formatCurrency($value) {
    return 'R$ ' . number_format((float)$value, 2, ',', '.');
}
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Recepção - OS #<?php echo safe_text($ordem, 'id', 'N/A'); ?></title>
    <style>
        @page { size: A4 portrait; margin: 12mm; }
        body { margin: 0; font-family: Arial, Helvetica, sans-serif; color: #222; }
        .sheet { width: 210mm; min-height: 297mm; padding: 8mm; box-sizing: border-box; }
        .twoup { display: flex; gap: 8mm; }
        .copy { width: calc(50% - 4mm); padding: 10mm; box-sizing: border-box; border: 1px solid #ddd; position: relative; }
        .copy .header { display:flex; justify-content:space-between; align-items:center; margin-bottom: 8px; }
        .logo { font-weight:700; font-size: 1.05rem; }
        .small { font-size: 0.85rem; color: #555; }
        h2 { margin: 6px 0; font-size: 1rem; }
        .section { margin-bottom: 8px; }
        .field { display:flex; gap: 6px; margin-bottom: 4px; }
        .label { font-weight:700; width: 120px; }
        .value { flex: 1; }
        textarea { width:100%; min-height: 70px; resize: none; border: 1px dashed #bbb; padding: 6px; box-sizing: border-box; }
        .sign { display:flex; justify-content:space-between; margin-top: 14px; }
        .sign .line { width:45%; border-top: 1px solid #333; padding-top: 6px; text-align:center; }
        .dashed-middle { border-left: 2px dashed #999; height: 100%; position: absolute; left: 50%; top: 8mm; transform: translateX(-50%); }
        @media print {
            body { -webkit-print-color-adjust: exact; }
            .sheet { padding: 0; margin: 0; }
            .copy { border: none; }
        }
    </style>
</head>
<body>
    <div class="sheet">
        <div class="twoup">
            <?php for ($c = 0; $c < 2; $c++): ?>
                <div class="copy">
                    <div class="header">
                        <div class="logo">Minha Loja / Oficina</div>
                        <div class="small">ORDEM DE SERVIÇO #<?php echo safe_text($ordem, 'id', 'N/A'); ?></div>
                    </div>

                    <div class="section">
                        <h2>Dados do Cliente</h2>
                        <div class="field"><div class="label">Nome:</div><div class="value"><?php echo safe_text($ordem, 'cliente_nome', 'N/A'); ?></div></div>
                        <div class="field"><div class="label">Contato:</div><div class="value"><?php echo safe_text($ordem, 'cliente_telefone', '-') . ' • ' . safe_text($ordem, 'cliente_email', '-'); ?></div></div>
                        <div class="field"><div class="label">Documento:</div><div class="value"><?php echo safe_text($ordem, 'cliente_documento', '-'); ?></div></div>
                    </div>

                    <div class="section">
                        <h2>Equipamento</h2>
                        <div class="field"><div class="label">Tipo:</div><div class="value"><?php echo safe_text($ordem, 'equipamento_tipo', '-'); ?></div></div>
                        <div class="field"><div class="label">Marca / Modelo:</div><div class="value"><?php echo safe_text($ordem, 'equipamento_marca', '-') . ' / ' . safe_text($ordem, 'equipamento_modelo', '-'); ?></div></div>
                        <div class="field"><div class="label">Serial / Senha:</div><div class="value"><?php echo safe_text($ordem, 'equipamento_serial', '-'); ?> / <?php echo safe_text($ordem, 'equipamento_senha', '-'); ?></div></div>
                    </div>

                    <div class="section">
                        <h2>Defeito Relatado</h2>
                        <textarea readonly><?php echo safe_text($ordem, 'defeito_relatado', safe_text($ordem, 'defeito', '')); ?></textarea>
                    </div>

                    <div class="section">
                        <h2>Observações da Oficial</h2>
                        <textarea readonly><?php echo safe_text($ordem, 'laudo_tecnico', ''); ?></textarea>
                    </div>

                    <div class="sign">
                        <div class="line">Assinatura do Cliente</div>
                        <div class="line">Assinatura da Loja</div>
                    </div>
                </div>
            <?php endfor; ?>
        </div>
        <div style="height:8mm;"></div>
        <div style="font-size:0.85rem; color:#666;">Gerado em: <?php echo date('d/m/Y H:i'); ?> — Levar documento no ato da retirada.</div>
    </div>
</body>
</html>
