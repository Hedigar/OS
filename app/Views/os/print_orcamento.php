<?php
$cliente = $cliente ?? [];
$debitosOS = $debitosOS ?? [];
$debitosAE = $debitosAE ?? [];

if (!function_exists('formatCurrency')) {
    function formatCurrency($value) {
        return 'R$ ' . number_format((float)$value, 2, ',', '.');
    }
}

$totalBrutoGeral = 0;
$totalDescontoGeral = 0;

foreach ($debitosOS as $os) {
    $desc = (float)($os['valor_desconto'] ?? 0);
    $totalDescontoGeral += $desc;
    $totalBrutoGeral += (float)($os['valor_total_os'] ?? 0) + $desc;
}
foreach ($debitosAE as $ae) {
    $descAE = (float)($ae['valor_desconto'] ?? 0);
    $totalDescontoGeral += $descAE;
    $totalBrutoGeral += (float)($ae['valor_total'] ?? 0) + $descAE;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <style>
        @page { margin: 10mm; }
        body { font-family: Helvetica, Arial, sans-serif; font-size: 12px; color: #333; line-height: 1.4; }
        .header { border-bottom: 2px solid #3498db; padding-bottom: 10px; margin-bottom: 15px; }
        .company-name { font-size: 18px; font-weight: bold; color: #2980b9; text-transform: uppercase; margin: 0; }
        .doc-title-box { background-color: #3498db; color: #ffffff; padding: 8px 15px; border-radius: 4px; margin-bottom: 15px; }
        .section-title { background-color: #f2f2f2; padding: 5px 10px; font-weight: bold; border-left: 4px solid #3498db; margin: 15px 0 8px 0; text-transform: uppercase; font-size: 11px; }
        .info-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        .info-table td { padding: 5px 8px; border: 1px solid #eee; font-size: 11px; }
        .label { font-weight: bold; background-color: #fafafa; width: 15%; }
        .items-table { width: 100%; border-collapse: collapse; }
        .items-table th { background-color: #2980b9; color: #ffffff; padding: 8px; font-size: 10px; text-transform: uppercase; text-align: left; }
        .items-table td { padding: 10px 8px; border-bottom: 1px solid #eee; vertical-align: top; }
        .text-right { text-align: right; }
        .status-badge { padding: 2px 5px; border-radius: 3px; font-size: 9px; color: #fff; font-weight: bold; }
        .discount-badge { background-color: #e74c3c; color: #ffffff; padding: 2px 5px; border-radius: 3px; font-size: 9px; font-weight: bold; }
        .totals-table { width: 300px; margin-left: auto; border-collapse: collapse; margin-top: 20px; }
        .totals-table td { padding: 6px 10px; border-bottom: 1px solid #eee; }
        .grand-total { background-color: #27ae60; color: #ffffff; font-weight: bold; font-size: 14px; }
    </style>
</head>
<body>
    <div class="header">
        <table width="100%">
            <tr>
                <td>
                    <p class="company-name">Myranda Informática</p>
                    <p style="font-size:10px; color:#7f8c8d; margin:0;">CNPJ: 13.558.678/0001-36 | (51) 3663-6445</p>
                </td>
                <td align="right"><h2 style="color:#3498db; margin:0;">EXTRATO DE DÉBITOS</h2></td>
            </tr>
        </table>
    </div>

    <div class="doc-title-box">
        <table width="100%">
            <tr>
                <td><strong>DETALHAMENTO FINANCEIRO</strong></td>
                <td align="right">Data de Emissão: <?php echo date('d/m/Y H:i'); ?></td>
            </tr>
        </table>
    </div>

    <div class="section-title">Informações do Cliente</div>
    <table class="info-table">
        <tr>
            <td class="label">Cliente:</td>
            <td colspan="3"><strong><?php echo htmlspecialchars($cliente['nome_completo'] ?? 'N/A'); ?></strong></td>
        </tr>
        <tr>
            <td class="label">CPF/CNPJ:</td>
            <td><?php echo htmlspecialchars($cliente['documento'] ?? 'N/A'); ?></td>
            <td class="label">Telefone:</td>
            <td><?php echo htmlspecialchars($cliente['telefone_principal'] ?? 'N/A'); ?></td>
        </tr>
        <tr>
            <td class="label">Endereço:</td>
            <td colspan="3">
                <?php 
                $end = ($cliente['endereco_logradouro'] ?? '') . ', ' . ($cliente['endereco_numero'] ?? '') . ' - ' . ($cliente['endereco_bairro'] ?? '') . ' / ' . ($cliente['endereco_cidade'] ?? '');
                echo htmlspecialchars($end);
                ?>
            </td>
        </tr>
    </table>

    <?php if (!empty($debitosOS)): ?>
    <div class="section-title">Ordens de Serviço Pendentes</div>
    <table class="items-table">
        <thead>
            <tr>
                <th width="85">Data/OS</th>
                <th>Equipamento / O que foi feito (Laudo)</th>
                <th width="100" class="text-right">Valor Líquido</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($debitosOS as $os): ?>
            <tr>
                <td>
                    <strong>#<?php echo str_pad($os['id'], 5, '0', STR_PAD_LEFT); ?></strong><br>
                    <small>Abertura: <?php echo date('d/m/Y', strtotime($os['created_at'])); ?></small>
                </td>
                <td>
                    <strong><?php echo htmlspecialchars($os['equipamento_modelo'] ?? 'Equipamento'); ?></strong><br>
                    <div style="color: #555; font-size: 11px; margin: 5px 0;">
                        <strong>Laudo/Serviço:</strong> <?php echo nl2br(htmlspecialchars($os['laudo_tecnico'] ?: $os['defeito_relatado'])); ?>
                    </div>
                    <span class="status-badge" style="background-color:<?php echo $os['status_cor']; ?>"><?php echo $os['status_nome']; ?></span>
                    <?php if(($os['valor_desconto'] ?? 0) > 0): ?>
                        <span class="discount-badge">DESC: -<?php echo formatCurrency($os['valor_desconto']); ?></span>
                    <?php endif; ?>
                </td>
                <td class="text-right"><strong><?php echo formatCurrency($os['valor_total_os']); ?></strong></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>

    <?php if (!empty($debitosAE)): ?>
    <div class="section-title">Atendimentos Externos Pendentes</div>
    <table class="items-table">
        <thead>
            <tr>
                <th width="85">Data</th>
                <th>Descrição do Serviço</th>
                <th width="100" class="text-right">Valor Líquido</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($debitosAE as $ae): ?>
            <tr>
                <td><?php echo date('d/m/Y', strtotime($ae['data_agendada'])); ?></td>
                <td>
                    <?php echo nl2br(htmlspecialchars($ae['detalhes_servico'] ?: $ae['descricao_problema'])); ?>
                    <?php if(($ae['valor_desconto'] ?? 0) > 0): ?>
                        <br><span class="discount-badge">DESC: -<?php echo formatCurrency($ae['valor_desconto']); ?></span>
                    <?php endif; ?>
                </td>
                <td class="text-right"><strong><?php echo formatCurrency($ae['valor_total']); ?></strong></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>

    <table class="totals-table">
        <tr>
            <td>Subtotal Bruto:</td>
            <td class="text-right"><?php echo formatCurrency($totalBrutoGeral); ?></td>
        </tr>
        <?php if($totalDescontoGeral > 0): ?>
        <tr style="color: #e74c3c;">
            <td>(-) Total Descontos:</td>
            <td class="text-right"><?php echo formatCurrency($totalDescontoGeral); ?></td>
        </tr>
        <?php endif; ?>
        <tr class="grand-total">
            <td>TOTAL A PAGAR:</td>
            <td class="text-right"><?php echo formatCurrency($totalBrutoGeral - $totalDescontoGeral); ?></td>
        </tr>
    </table>
</body>
</html>