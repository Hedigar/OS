<?php
$cliente = $cliente ?? [];
$debitosOS = $debitosOS ?? [];
$debitosAE = $debitosAE ?? [];

if (!function_exists('formatCurrency')) {
    function formatCurrency($value) {
        return 'R$ ' . number_format((float)$value, 2, ',', '.');
    }
}

if (!function_exists('generatePixPayload')) {
    function generatePixPayload($key, $amount, $merchantName = 'Myranda Informatica', $merchantCity = 'Osorio') {
        // 1. Format Pix Key
        if (preg_match('/^\d{11}$/', $key)) {
            $key = '+55' . $key;
        }
        
        // 2. Merchant Account Info (Tag 26)
        $gui = "0014br.gov.bcb.pix";
        $keyTag = "01" . str_pad(strlen($key), 2, '0', STR_PAD_LEFT) . $key;
        $merchantAccountInfo = "26" . str_pad(strlen($gui . $keyTag), 2, '0', STR_PAD_LEFT) . $gui . $keyTag;
        
        // 3. Payload elements
        $parts = [
            "00" => "000201", // Payload Format Indicator
            "26" => $merchantAccountInfo,
            "52" => "52040000", // Merchant Category Code
            "53" => "5303986",  // Transaction Currency (986 = BRL)
        ];
        
        // Amount (Tag 54)
        if ($amount > 0) {
            $amountStr = number_format((float)$amount, 2, '.', '');
            $parts["54"] = "54" . str_pad(strlen($amountStr), 2, '0', STR_PAD_LEFT) . $amountStr;
        }
        
        // Country Code (Tag 58)
        $parts["58"] = "5802BR";
        
        // Merchant Name (Tag 59)
        if (function_exists('iconv')) {
            $cleanName = preg_replace('/[^A-Za-z0-9 ]/', '', @iconv('UTF-8', 'ASCII//TRANSLIT', $merchantName));
        } else {
            $cleanName = str_replace(
                ['á','à','â','ã','ä','é','è','ê','ë','í','ì','î','ï','ó','ò','ô','õ','ö','ú','ù','û','ü','ç','Á','À','Â','Ã','Ä','É','È','Ê','Ë','Í','Ì','Î','Ï','Ó','Ò','Ô','Õ','Ö','Ú','Ù','Û','Ü','Ç'],
                ['a','a','a','a','a','e','e','e','e','i','i','i','i','o','o','o','o','o','u','u','u','u','c','A','A','A','A','A','E','E','E','E','I','I','I','I','O','O','O','O','O','U','U','U','U','C'],
                $merchantName
            );
            $cleanName = preg_replace('/[^A-Za-z0-9 ]/', '', $cleanName);
        }
        $cleanName = substr(trim($cleanName), 0, 25);
        $parts["59"] = "59" . str_pad(strlen($cleanName), 2, '0', STR_PAD_LEFT) . $cleanName;
        
        // Merchant City (Tag 60)
        if (function_exists('iconv')) {
            $cleanCity = preg_replace('/[^A-Za-z0-9 ]/', '', @iconv('UTF-8', 'ASCII//TRANSLIT', $merchantCity));
        } else {
            $cleanCity = str_replace(
                ['á','à','â','ã','ä','é','è','ê','ë','í','ì','î','ï','ó','ò','ô','õ','ö','ú','ù','û','ü','ç','Á','À','Â','Ã','Ä','É','È','Ê','Ë','Í','Ì','Î','Ï','Ó','Ò','Ô','Õ','Ö','Ú','Ù','Û','Ü','Ç'],
                ['a','a','a','a','a','e','e','e','e','i','i','i','i','o','o','o','o','o','u','u','u','u','c','A','A','A','A','A','E','E','E','E','I','I','I','I','O','O','O','O','O','U','U','U','U','C'],
                $merchantCity
            );
            $cleanCity = preg_replace('/[^A-Za-z0-9 ]/', '', $cleanCity);
        }
        $cleanCity = substr(trim($cleanCity), 0, 15);
        $parts["60"] = "60" . str_pad(strlen($cleanCity), 2, '0', STR_PAD_LEFT) . $cleanCity;
        
        // Additional Data Field (Tag 62)
        $parts["62"] = "62070503***";
        
        // Assemble payload string
        $payload = $parts["00"] . $parts["26"] . $parts["52"] . $parts["53"];
        if (isset($parts["54"])) {
            $payload .= $parts["54"];
        }
        $payload .= $parts["58"] . $parts["59"] . $parts["60"] . $parts["62"];
        
        // Append CRC tag indicator
        $payload .= "6304";
        
        // Calculate CRC16 CCITT
        $crc = 0xFFFF;
        $length = strlen($payload);
        for ($i = 0; $i < $length; $i++) {
            $crc ^= (ord($payload[$i]) << 8);
            for ($j = 0; $j < 8; $j++) {
                if (($crc & 0x8000) != 0) {
                    $crc = (($crc << 1) ^ 0x1021) & 0xFFFF;
                } else {
                    $crc = ($crc << 1) & 0xFFFF;
                }
            }
        }
        
        $crcHex = strtoupper(str_pad(dechex($crc), 4, '0', STR_PAD_LEFT));
        
        return $payload . $crcHex;
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
                    <p style="font-size:10px; color:#7f8c8d; margin:0;">Av. Getulio Vargas, 1144 -  Centro - Osório</p>
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
                echo htmlspecialchars(($cliente['endereco_logradouro'] ?? '') . ', ' . ($cliente['endereco_numero'] ?? '') . ' - ' . ($cliente['endereco_bairro'] ?? '') . ' / ' . ($cliente['endereco_cidade'] ?? '')); 
                ?>
            </td>
        </tr>
    </table>

    <?php if (!empty($debitosOS)): ?>
    <div class="section-title">Ordens de Serviço (Laboratório)</div>
    <table class="items-table">
        <thead>
            <tr>
                <th width="85">Data/OS</th>
                <th>Equipamento / Laudo Técnico</th>
                <th width="100" class="text-right">Valor Líquido</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($debitosOS as $os): ?>
            <tr>
                <td>
                    <strong>#<?php echo str_pad($os['id'], 5, '0', STR_PAD_LEFT); ?></strong><br>
                    <small>Entrada: <?php echo date('d/m/Y', strtotime($os['created_at'])); ?></small>
                </td>
                <td>
                    <strong><?php echo htmlspecialchars($os['equipamento_modelo'] ?? 'Equipamento não identificado'); ?></strong><br>
                    <div style="color: #555; font-size: 11px; margin: 4px 0;">
                        <strong>Serviço realizado:</strong> <?php echo nl2br(htmlspecialchars($os['laudo_tecnico'] ?: $os['defeito_relatado'])); ?>
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
    <div class="section-title">Atendimentos Externos (Visitas)</div>
    <table class="items-table">
        <thead>
            <tr>
                <th width="85">Data</th>
                <th>Descrição do Serviço Externo</th>
                <th width="100" class="text-right">Valor Líquido</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($debitosAE as $ae): ?>
            <tr>
                <td>
    <?php 
        // Verifica se a data existe antes de usar o strtotime
        echo !empty($ae['data_agendada']) ? date('d/m/Y', strtotime($ae['data_agendada'])) : 'N/A'; 
    ?>
</td>
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
            <td>Total Bruto:</td>
            <td class="text-right"><?php echo formatCurrency($totalBrutoGeral); ?></td>
        </tr>
        <?php if($totalDescontoGeral > 0): ?>
        <tr style="color: #e74c3c;">
            <td>(-) Descontos:</td>
            <td class="text-right"><?php echo formatCurrency($totalDescontoGeral); ?></td>
        </tr>
        <?php endif; ?>
        <tr class="grand-total">
            <td>TOTAL A PAGAR:</td>
            <td class="text-right"><?php echo formatCurrency($totalBrutoGeral - $totalDescontoGeral); ?></td>
        </tr>
    </table>

    <?php 
    $valorPagar = $totalBrutoGeral - $totalDescontoGeral;
    if ($valorPagar > 0): 
        $pixPayload = generatePixPayload('51983591567', $valorPagar, 'Myranda Informatica', 'Osorio');
    ?>
    <div style="margin-top: 30px; border-top: 2px dashed #ddd; padding-top: 20px;">
        <table width="100%" style="border-collapse: collapse;">
            <tr>
                <td width="40%" style="vertical-align: top; text-align: center; padding-right: 20px;">
                    <div style="background-color: #fcfcfc; border: 1px solid #e0e0e0; border-radius: 6px; padding: 12px; text-align: center;">
                        <h4 style="margin: 0 0 10px 0; color: #27ae60; font-size: 12px; text-transform: uppercase; font-weight: bold;">Pague com Pix</h4>
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=<?php echo urlencode($pixPayload); ?>" alt="QR Code Pix" style="width: 140px; height: 140px; margin-bottom: 5px; border: 1px solid #ccc; padding: 5px; background: #fff;" />
                        <div style="font-size: 9px; color: #7f8c8d; line-height: 1.2;">Escaneie o QR Code acima com o aplicativo do seu banco para pagar.</div>
                    </div>
                </td>
                <td width="60%" style="vertical-align: top;">
                    <div style="background-color: #fcfcfc; border: 1px solid #e0e0e0; border-radius: 6px; padding: 12px; height: 154px; box-sizing: border-box;">
                        <h4 style="margin: 0 0 10px 0; color: #2980b9; font-size: 12px; text-transform: uppercase; font-weight: bold;">Pix Copia e Cola</h4>
                        <div style="font-family: monospace; font-size: 8px; border: 1px solid #ccc; border-radius: 4px; padding: 6px; background-color: #f5f5f5; color: #333; word-break: break-all; height: 50px; overflow: hidden; line-height: 1.3;">
                            <?php echo htmlspecialchars($pixPayload); ?>
                        </div>
                        <div style="font-size: 9px; color: #7f8c8d; margin-top: 5px; line-height: 1.2;">Se preferir, utilize a opção "Pix Copia e Cola" no app do seu banco com o código acima.</div>
                        <div style="font-size: 10px; margin-top: 10px; line-height: 1.4; border-top: 1px solid #eee; padding-top: 8px;">
                            <strong>Beneficiário:</strong> Myranda Informática<br>
                            <strong>Chave Pix:</strong> (51) 98359-1567 (Telefone)
                        </div>
                    </div>
                </td>
            </tr>
        </table>
    </div>
    <?php endif; ?>
</body>
</html>
