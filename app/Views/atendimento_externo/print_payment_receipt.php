<?php
/**
 * TEMPLATE DE RECIBO DE PAGAMENTO - ATENDIMENTO EXTERNO - 80mm (PDF)
 */
$atendimento = $atendimento ?? [];
$itens = $itens ?? [];
$transacoes = $transacoes ?? [];

if (!function_exists('safe_text')) {
    function safe_text(array $arr, string $key, string $default = ''): string {
        return htmlspecialchars((string)($arr[$key] ?? $default), ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('formatCurrency')) {
    function formatCurrency($value) {
        return 'R$ ' . number_format((float)$value, 2, ',', '.');
    }
}

// Logo (opcional)
$logoPath = 'assets/img/logo.png';
$fullLogoPath = $_SERVER['DOCUMENT_ROOT'] . '/' . $logoPath;
if (!file_exists($fullLogoPath)) {
    $fullLogoPath = dirname(__DIR__, 3) . '/public/' . $logoPath;
}
$logoBase64 = '';
if (extension_loaded('gd') && file_exists($fullLogoPath)) {
    try {
        $type = pathinfo($fullLogoPath, PATHINFO_EXTENSION);
        $data = file_get_contents($fullLogoPath);
        $logoBase64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
    } catch (Exception $e) {}
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Recibo de Pagamento - Atendimento #<?php echo $atendimento['id']; ?></title>
    <style>
        @page {
            margin: 0;
            size: 80mm auto;
        }
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 10px;
            margin: 2mm;
            color: #000;
            background: #fff;
        }
        .header {
            text-align: center;
            margin-bottom: 5px;
        }
        .logo {
            max-width: 60mm;
            max-height: 20mm;
            margin-bottom: 2px;
        }
        .title {
            font-weight: bold;
            font-size: 12px;
            margin: 5px 0;
            text-transform: uppercase;
        }
        .divider {
            border-top: 1px dashed #000;
            margin: 5px 0;
        }
        .info-row {
            margin-bottom: 2px;
        }
        .label {
            font-weight: bold;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }
        .table th {
            text-align: left;
            border-bottom: 1px dashed #000;
            font-size: 9px;
        }
        .table td {
            padding: 2px 0;
            font-size: 9px;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .footer {
            text-align: center;
            margin-top: 10px;
            font-size: 8px;
        }
    </style>
</head>
<body>
    <div class="header">
        <?php if ($logoBase64): ?>
            <img src="<?php echo $logoBase64; ?>" class="logo">
        <?php else: ?>
            <div style="font-weight: bold; font-size: 14px;">MYRANDA INFORMÁTICA</div>
        <?php endif; ?>
        <div>CNPJ: 13.558.678/0001-36</div>
        <div>(51) 3663-6445 / 98359-1567</div>
        <div>Avenida Getúlio Vargas, 1144 - Centro - Osório/RS</div>
    </div>

    <div class="divider"></div>

    <div class="text-center title">RECIBO DE PAGAMENTO</div>
    <div class="text-center">ATENDIMENTO EXTERNO: #<?php echo $atendimento['id']; ?></div>
    <div class="text-center"><?php echo date('d/m/Y H:i'); ?></div>

    <div class="divider"></div>

    <div class="info-row"><span class="label">Cliente:</span> <?php echo safe_text($atendimento, 'cliente_nome'); ?></div>
    <?php if (!empty($atendimento['cliente_documento'])): ?>
    <div class="info-row"><span class="label">CPF/CNPJ:</span> <?php echo safe_text($atendimento, 'cliente_documento'); ?></div>
    <?php endif; ?>

    <div class="divider"></div>

    <div class="info-row">
        <span class="label">Serviço/Equipamento:</span><br>
        <?php echo safe_text($atendimento, 'equipamentos', 'N/A'); ?>
    </div>

    <?php if (!empty($itens)): ?>
    <div class="divider"></div>
    <div class="title text-center">PRODUTOS / SERVIÇOS</div>
    <table class="table">
        <thead>
            <tr>
                <th>Item</th>
                <th class="text-right">Qtd</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $calcSomaBruta = 0;
            $calcSomaDescontos = 0;
            $calcSomaLiquida = 0;
            
            foreach ($itens as $item): 
                $vUnit = (float)($item['valor_unitario'] ?? 0);
                $vMao = (float)($item['valor_mao_de_obra'] ?? 0);
                $qtd = (float)($item['quantidade'] ?? 1);
                $desc = (float)($item['desconto'] ?? 0);
                $vTotalItem = (float)($item['valor_total'] ?? 0);
                
                $vUnitFull = $vUnit + $vMao;
                $vBrutoItem = $vUnitFull * $qtd;
                
                $calcSomaBruta += $vBrutoItem;
                $calcSomaDescontos += $desc;
                $calcSomaLiquida += $vTotalItem;
            ?>
            <tr>
                <td>
                    <?php echo safe_text($item, 'descricao'); ?><br>
                    <small><?php echo formatCurrency($vUnitFull); ?></small>
                </td>
                <td class="text-right"><?php echo $item['quantidade']; ?></td>
                <td class="text-right"><?php echo formatCurrency($vTotalItem); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php else: 
        $calcSomaBruta = 0;
        $calcSomaDescontos = 0;
        $calcSomaLiquida = 0;
    endif; ?>

    <div class="divider"></div>
    <div class="title text-center">PAGAMENTO</div>

    <?php if (empty($transacoes)): ?>
        <div class="text-center" style="margin: 10px 0;">Nenhum pagamento registrado.</div>
    <?php else: ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Data</th>
                    <th>Forma</th>
                    <th class="text-right">Valor</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($transacoes as $t): ?>
                <tr>
                    <td><?php echo date('d/m/y H:i', strtotime($t['created_at'])); ?></td>
                    <td>
                        <?php echo ucfirst($t['forma']); ?>
                        <?php if ($t['parcelas'] > 1) echo " ({$t['parcelas']}x)"; ?>
                    </td>
                    <td class="text-right"><?php echo formatCurrency($t['valor_bruto']); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <div class="divider"></div>

    <div class="totals">
        <table style="width: 100%;">
            <?php 
            $vTotal = (float)($valor_total ?? 0);
            $vDeslocamento = (float)($atendimento['valor_deslocamento'] ?? 0);
            $saldo = max(0, $vTotal - ($total_pago ?? 0));
            ?>
            <tr>
                <td class="label">Subtotal:</td>
                <td class="text-right"><?php echo formatCurrency($calcSomaBruta); ?></td>
            </tr>
            <?php if ($vDeslocamento > 0): ?>
            <tr>
                <td class="label">Deslocamento:</td>
                <td class="text-right"><?php echo formatCurrency($vDeslocamento); ?></td>
            </tr>
            <?php endif; ?>
            <?php if ($calcSomaDescontos > 0): ?>
            <tr>
                <td class="label">Desconto:</td>
                <td class="text-right">- <?php echo formatCurrency($calcSomaDescontos); ?></td>
            </tr>
            <?php endif; ?>
            <tr>
                <td class="label" style="font-size: 11px;">Total Geral:</td>
                <td class="text-right" style="font-size: 11px; font-weight: bold;"><?php echo formatCurrency($vTotal); ?></td>
            </tr>
            <tr>
                <td class="label">Total Pago:</td>
                <td class="text-right"><?php echo formatCurrency($total_pago ?? 0); ?></td>
            </tr>
            <?php if ($saldo > 0): ?>
            <tr style="color: #d32f2f;">
                <td class="label">Saldo Restante:</td>
                <td class="text-right"><?php echo formatCurrency($saldo); ?></td>
            </tr>
            <?php else: ?>
            <tr style="color: #2e7d32;">
                <td class="label" colspan="2" class="text-center">--- CONCLUÍDO / PAGO ---</td>
            </tr>
            <?php endif; ?>
        </table>
    </div>

    <div class="divider"></div>

    <div class="footer">
        Obrigado pela preferência!<br>
        Sistema de Gestão - Myranda Informática
    </div>
</body>
</html>