<?php
// Template de impressão: orçamento (A4)
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
    <title>Orçamento - OS #<?php echo safe_text($ordem, 'id', 'N/A'); ?></title>
    <style>
        @page { size: A4 portrait; margin: 14mm; }
        body { margin: 0; font-family: Arial, Helvetica, sans-serif; color: #222; }
        .sheet { width: 210mm; min-height: 297mm; padding: 12mm; box-sizing: border-box; }
        .header { display:flex; justify-content:space-between; align-items:center; margin-bottom: 12px; }
        .logo { font-weight:700; font-size: 1.2rem; }
        .meta { text-align:right; font-size:0.85rem; color:#555; }
        table { width:100%; border-collapse: collapse; margin-top: 8px; }
        th, td { padding: 8px 6px; border: 1px solid #ddd; font-size: 0.95rem; }
        th { background: #f7f7f7; text-align:left; }
        .right { text-align: right; }
        .totals { margin-top: 12px; width: 100%; display:flex; justify-content:flex-end; }
        .totals table { width: 320px; }
        .laudo { margin-top: 16px; }
        .signature { display:flex; justify-content:space-between; margin-top: 20px; }
        .sign .line { width:45%; border-top: 1px solid #333; padding-top: 6px; text-align:center; }
        @media print {
            body { -webkit-print-color-adjust: exact; }
            .sheet { padding: 0; }
        }
    </style>
</head>
<body>
    <div class="sheet">
        <div class="header">
            <div>
                <div class="logo">Minha Loja / Oficina</div>
                <div style="font-size:0.95rem; color:#555;">Endereço • Telefone • CNPJ</div>
            </div>
            <div class="meta">
                <div>ORDEM DE SERVIÇO #<?php echo safe_text($ordem, 'id', 'N/A'); ?></div>
                <div>Emissão: <?php echo date('d/m/Y H:i'); ?></div>
                <div>Cliente: <?php echo safe_text($ordem, 'cliente_nome', '-'); ?></div>
            </div>
        </div>

        <div style="margin-top:6px;">
            <strong>Equipamento:</strong> <?php echo safe_text($ordem, 'equipamento_tipo', '-'); ?> • <?php echo safe_text($ordem, 'equipamento_marca', '-'); ?> / <?php echo safe_text($ordem, 'equipamento_modelo', '-'); ?>
        </div>

        <table>
            <thead>
                <tr>
                    <th style="width:8%;">#</th>
                    <th>Descrição</th>
                    <th style="width:12%;" class="right">Qtd</th>
                    <th style="width:16%;" class="right">Vlr Unit.</th>
                    <th style="width:16%;" class="right">M. Obra</th>
                    <th style="width:16%;" class="right">Total</th>
                </tr>
            </thead>
            <tbody>
                <?php $i = 1; foreach ($itens as $item): ?>
                    <tr>
                        <td><?php echo $i++; ?></td>
                        <td><?php echo safe_text($item, 'descricao', ''); ?></td>
                        <td class="right"><?php echo number_format((float)($item['quantidade'] ?? 1), 2, ',', '.'); ?></td>
                        <td class="right"><?php echo formatCurrency($item['valor_unitario'] ?? 0); ?></td>
                        <td class="right"><?php echo formatCurrency($item['valor_mao_de_obra'] ?? 0); ?></td>
                        <td class="right" style="font-weight:700;"><?php echo formatCurrency($item['valor_total'] ?? 0); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="totals">
            <table>
                <tr>
                    <td style="padding:8px;">Subtotal</td>
                    <td class="right" style="padding:8px;"><?php echo formatCurrency((float)($ordem['valor_total_produtos'] ?? 0) + (float)($ordem['valor_total_servicos'] ?? 0)); ?></td>
                </tr>
                <tr>
                    <td style="padding:8px;">Desconto</td>
                    <td class="right" style="padding:8px;"><?php echo formatCurrency(0); ?></td>
                </tr>
                <tr style="font-weight:700;">
                    <td style="padding:8px;">TOTAL</td>
                    <td class="right" style="padding:8px;"><?php echo formatCurrency((float)($ordem['valor_total_os'] ?? 0)); ?></td>
                </tr>
            </table>
        </div>

        <div class="laudo">
            <h3>Laudo Técnico / Observações</h3>
            <div style="border:1px dashed #bbb; padding:10px; min-height:100px;">
                <?php echo nl2br(htmlspecialchars($ordem['laudo_tecnico'] ?? '', ENT_QUOTES, 'UTF-8')); ?>
            </div>
        </div>

        <div class="signature">
            <div class="sign"><div class="line">Assinatura do Cliente</div></div>
            <div class="sign"><div class="line">Assinatura do Técnico / Loja</div></div>
        </div>

    </div>
</body>
</html>
