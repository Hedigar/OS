<?php
/**
 * TEMPLATE DE RECEPÇÃO - 2 VIAS POR A4 (PDF)
 * Otimizado para caber perfeitamente em uma folha A4 para corte no meio.
 */
$ordem = $ordem ?? [];

if (!function_exists('safe_text')) {
    function safe_text(array $arr, string $key, string $default = ''): string {
        return htmlspecialchars((string)($arr[$key] ?? $default), ENT_QUOTES, 'UTF-8');
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        @page { 
            margin: 0; 
            size: A4 portrait;
        }
        body {
            font-family: Helvetica, Arial, sans-serif;
            font-size: 10px;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #fff;
        }
        .page {
            width: 210mm;
            height: 297mm;
            position: relative;
            overflow: hidden;
        }
        .copy {
            width: 190mm;
            height: 138mm; /* Ajustado para caber 2 vias com folga para a linha de corte */
            margin: 5mm auto;
            padding: 8mm;
            border: 1px solid #eee; /* Borda leve para visualização, pode ser removida */
            box-sizing: border-box;
            position: relative;
        }
        .header {
            border-bottom: 1px solid #2c3e50;
            padding-bottom: 5px;
            margin-bottom: 8px;
        }
        .header-table { width: 100%; }
        .company-name { font-size: 14px; font-weight: bold; color: #2c3e50; }
        .os-title { font-size: 12px; font-weight: bold; text-align: right; }
        
        .section-title {
            background-color: #f2f2f2;
            padding: 3px 8px;
            font-weight: bold;
            margin: 8px 0 4px 0;
            border-left: 3px solid #2c3e50;
            text-transform: uppercase;
            font-size: 9px;
        }
        .info-table { width: 100%; border-collapse: collapse; }
        .info-table td { padding: 3px 5px; border: 1px solid #eee; }
        .label { font-weight: bold; width: 80px; background-color: #fafafa; }
        
        .defeito-box {
            border: 1px solid #eee;
            padding: 6px;
            min-height: 35px;
            background-color: #fcfcfc;
            font-style: italic;
        }
        .footer-info {
            margin-top: 10px;
            font-size: 8px;
            color: #7f8c8d;
            text-align: justify;
            line-height: 1.2;
        }
        .signature-area {
            margin-top: 15px;
            width: 100%;
        }
        .sig-box {
            width: 45%;
            text-align: center;
            border-top: 1px solid #333;
            padding-top: 3px;
            display: inline-block;
            font-size: 9px;
        }
        .divider {
            width: 100%;
            border-top: 1px dashed #999;
            position: absolute;
            top: 148.5mm; /* Exatamente no meio da folha A4 (297 / 2) */
            left: 0;
        }
        .via-indicator {
            position: absolute;
            top: 5px;
            right: 10mm;
            font-size: 7px;
            color: #999;
            text-transform: uppercase;
        }
    </style>
</head>
<body>
    <div class="page">
        <?php for ($i = 0; $i < 2; $i++): ?>
            <div class="copy" style="<?php echo $i == 1 ? 'margin-top: 10mm;' : ''; ?>">
                <div class="via-indicator">
                    <?php echo $i == 0 ? "Via da Empresa" : "Via do Cliente"; ?>
                </div>
                
                <div class="header">
                    <table class="header-table">
                        <tr>
                            <td class="company-name">Myranda Informática</td>
                            <td class="os-title">ORDEM DE SERVIÇO #<?php echo str_pad($ordem['id'] ?? '', 6, '0', STR_PAD_LEFT); ?></td>
                        </tr>
                    </table>
                </div>

                <table class="info-table">
                    <tr>
                        <td class="label">Cliente:</td>
                        <td><?php echo safe_text($ordem, 'cliente_nome', 'N/A'); ?></td>
                        <td class="label">Data/Hora:</td>
                        <td><?php echo date('d/m/Y H:i'); ?></td>
                    </tr>
                    <tr>
                        <td class="label">Telefone:</td>
                        <td><?php echo safe_text($ordem, 'cliente_telefone', 'N/A'); ?></td>
                        <td class="label">Documento:</td>
                        <td><?php echo safe_text($ordem, 'cliente_documento', 'N/A'); ?></td>
                    </tr>
                </table>

                <div class="section-title">Equipamento</div>
                <table class="info-table">
                    <tr>
                        <td class="label">Tipo:</td>
                        <td><?php echo safe_text($ordem, 'equipamento_tipo', 'N/A'); ?></td>
                        <td class="label">Marca/Mod:</td>
                        <td><?php echo safe_text($ordem, 'equipamento_marca', '') . ' ' . safe_text($ordem, 'equipamento_modelo', ''); ?></td>
                    </tr>
                    <tr>
                        <td class="label">Serial:</td>
                        <td><?php echo safe_text($ordem, 'equipamento_serial', 'N/A'); ?></td>
                        <td class="label">Acessórios:</td>
                        <td><?php echo safe_text($ordem, 'equipamento_acessorios', 'Nenhum'); ?></td>
                    </tr>
                </table>

                <div class="section-title">Defeito Relatado</div>
                <div class="defeito-box"><?php echo safe_text($ordem, 'defeito_relatado', 'Não informado'); ?></div>

                <div class="footer-info">
                    <strong>Termos:</strong> O cliente declara estar ciente de que a empresa não se responsabiliza por perda de dados. Equipamentos não retirados em 90 dias serão considerados abandonados. É obrigatória a apresentação deste canhoto para retirada do equipamento. Garantia de 90 dias para serviços executados.
                </div>

                <div class="signature-area">
                    <div class="sig-box">Assinatura da Empresa</div>
                    <div style="width: 8%; display: inline-block;"></div>
                    <div class="sig-box">Assinatura do Cliente</div>
                </div>
            </div>
        <?php endfor; ?>
        <div class="divider"></div>
    </div>
</body>
</html>
