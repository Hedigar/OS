<?php
/**
 * TEMPLATE DE RECIBO DE PAGAMENTO - 80mm (PDF)
 */
$ordem = $ordem ?? [];
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

// Logo (opcional, pode ser texto se não tiver imagem)
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
    <title>Recibo de Pagamento - OS #<?php echo $ordem['id']; ?></title>
    <style>
        @page {
            margin: 0;
            size: 80mm auto; /* Largura 80mm, altura automática */
        }
        body {
            font-family: 'Courier New', Courier, monospace; /* Fonte monoespaçada fica melhor em térmica */
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
        .totals {
            margin-top: 5px;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2px;
            font-weight: bold;
        }
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
    <div class="text-center">OS: #<?php echo $ordem['id']; ?></div>
    <div class="text-center"><?php echo date('d/m/Y H:i'); ?></div>

    <div class="divider"></div>

    <div class="info-row"><span class="label">Cliente:</span> <?php echo safe_text($ordem, 'cliente_nome'); ?></div>
    <?php if (!empty($ordem['cliente_documento'])): ?>
    <div class="info-row"><span class="label">CPF/CNPJ:</span> <?php echo safe_text($ordem, 'cliente_documento'); ?></div>
    <?php endif; ?>

    <div class="divider"></div>

    <div class="info-row">
        <span class="label">Equipamento:</span><br>
        <?php echo safe_text($ordem, 'equipamento_nome', 'N/A'); ?>
        <?php if (!empty($ordem['equipamento_modelo'])): ?>
            - <?php echo safe_text($ordem, 'equipamento_modelo'); ?>
        <?php endif; ?>
        <?php if (!empty($ordem['equipamento_serie'])): ?>
            (S/N: <?php echo safe_text($ordem, 'equipamento_serie'); ?>)
        <?php endif; ?>
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
            // Variáveis para recálculo preciso dos totais
            $calcSomaBruta = 0;
            $calcSomaDescontos = 0;
            $calcSomaLiquida = 0;
            
            foreach ($itens as $item): 
                $vUnit = (float)($item['valor_unitario'] ?? 0);
                $vMao = (float)($item['valor_mao_de_obra'] ?? 0);
                $qtd = (float)($item['quantidade'] ?? 1);
                $desc = (float)($item['desconto'] ?? 0);
                $vTotalItem = (float)($item['valor_total'] ?? 0);
                
                // Valor unitário completo (peça + mão de obra)
                $vUnitFull = $vUnit + $vMao;
                // Valor bruto total deste item
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
    <?php endif; ?>

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
            // Se não houver itens (improvável), usa os dados da OS
            if (empty($itens)) {
                $calcSomaLiquida = (float)($ordem['valor_total_os'] ?? 0);
                $calcSomaDescontos = (float)($ordem['valor_desconto'] ?? 0);
                $calcSomaBruta = $calcSomaLiquida + $calcSomaDescontos;
            }
            
            $saldo = max(0, $calcSomaLiquida - ($total_pago ?? 0));
            ?>
            <tr>
                <td class="label">Subtotal:</td>
                <td class="text-right"><?php echo formatCurrency($calcSomaBruta); ?></td>
            </tr>
            <?php if ($calcSomaDescontos > 0): ?>
            <tr>
                <td class="label">Desconto:</td>
                <td class="text-right">- <?php echo formatCurrency($calcSomaDescontos); ?></td>
            </tr>
            <?php endif; ?>
            <tr>
                <td class="label">Total a Pagar:</td>
                <td class="text-right"><?php echo formatCurrency($calcSomaLiquida); ?></td>
            </tr>
            <tr>
                <td class="label">Total Pago:</td>
                <td class="text-right"><?php echo formatCurrency($total_pago ?? 0); ?></td>
            </tr>
            <tr>
                <td class="label">Saldo Restante:</td>
                <td class="text-right"><?php echo formatCurrency($saldo); ?></td>
            </tr>
        </table>
    </div>

    <div class="divider"></div>
    <div class="footer">
        <br><br>
        _____________________________________<br>
        Assinatura do Responsável
        <br><br>
        Obrigado pela preferência!
    </div>
</body>
</html>