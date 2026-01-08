<?php
/**
 * TEMPLATE DE RECEPÇÃO - 2 VIAS POR A4 (PDF)
 * Ajustado para se assemelhar ao Modeloantigo.php e lidar com falta de extensão GD
 */
$ordem = $ordem ?? [];

if (!function_exists('safe_text')) {
    function safe_text(array $arr, string $key, string $default = ''): string {
        return htmlspecialchars((string)($arr[$key] ?? $default), ENT_QUOTES, 'UTF-8');
    }
}

// Verificar se a extensão GD está instalada para evitar erro fatal
$hasGD = extension_loaded('gd');

// Caminho do logo para o Dompdf
$logoPath = 'assets/img/logo.png';
$fullLogoPath = $_SERVER['DOCUMENT_ROOT'] . '/' . $logoPath;
if (!file_exists($fullLogoPath)) {
    $fullLogoPath = dirname(__DIR__, 3) . '/public/' . $logoPath;
}

// Converter imagem para base64 apenas se GD estiver disponível e o arquivo existir
$logoBase64 = '';
if ($hasGD && file_exists($fullLogoPath)) {
    try {
        $type = pathinfo($fullLogoPath, PATHINFO_EXTENSION);
        $data = file_get_contents($fullLogoPath);
        $logoBase64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
    } catch (Exception $e) {
        $logoBase64 = ''; // Falha silenciosa se houver erro na leitura
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        @page { 
            margin: 5mm; 
            size: A4 portrait;
        }
        body {
            font-family: Helvetica, Arial, sans-serif;
            font-size: 13px;
            color: #000;
            margin: 0;
            padding: 0;
            background-color: #fff;
        }
        .page {
            width: 100%;
        }
        .copy {
            width: 100%;
            padding: 5mm;
            box-sizing: border-box;
            position: relative;
        }
        .header-table { 
            width: 100%; 
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        .logo-cell { width: 30%; }
        .os-cell { 
            width: 20%; 
            font-size: 24px; 
            font-weight: bold; 
            text-align: center;
            vertical-align: middle;
        }
        .company-cell { 
            width: 50%; 
            text-align: right; 
            font-size: 11px;
            font-weight: bold;
        }
        
        .divider-line {
            border-top: 1px solid #000;
            margin: 5px 0;
        }
        
        .info-table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-bottom: 10px;
        }
        .info-table td { 
            padding: 3px 0; 
            vertical-align: top;
        }
        .label { font-weight: bold; }
        
        .problem-section {
            margin-bottom: 10px;
        }
        .problem-title {
            font-weight: bold;
            font-size: 12px;
        }
        
        .terms-section {
            font-size: 10px;
            text-align: justify;
            line-height: 1.2;
            margin-bottom: 10px;
        }
        
        .signature-table {
            width: 100%;
            margin-top: 15px;
        }
        .sig-box {
            width: 45%;
            text-align: center;
            border-top: 1px solid #000;
            padding-top: 3px;
            font-size: 10px;
        }
        
        .cut-line {
            width: 100%;
            text-align: center;
            margin: 10px 0;
            border-top: 1px dashed #000;
            position: relative;
        }
        .cut-icon {
            position: absolute;
            top: -10px;
            left: 10px;
            background: #fff;
            padding: 0 5px;
        }
        
        .date-row {
            text-align: center;
            margin-top: 10px;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="page">
        <?php for ($i = 0; $i < 2; $i++): ?>
            <div class="copy">
                <table class="header-table">
                    <tr>
                        <td class="logo-cell">
                            <?php if ($logoBase64): ?>
                                <img src="<?php echo $logoBase64; ?>" alt="Logo" style="max-width: 180px; max-height: 60px;">
                            <?php else: ?>
                                <div style="font-weight: bold; font-size: 18px; color: #2c3e50;">MYRANDA INFORMÁTICA</div>
                                <?php if (!$hasGD): ?>
                                    <div style="font-size: 8px; color: #999;">(Logo em texto - Extensão GD não instalada)</div>
                                <?php endif; ?>
                            <?php endif; ?>
                        </td>
                        <td class="os-cell">
                            O.S.: <?php echo $ordem['id'] ?? ''; ?>
                        </td>
                        <td class="company-cell">
                            CNPJ: 13.558.678/0001-36 <br>
                            WhatsApp: 51 98359-1567 Fone: (51) 3663-6445<br>
                            E-mail: myrandainformatica@gmail.com <br>
                            Avenida Getúlio Vargas, 1144 - Centro - Osório/RS
                        </td>
                    </tr>
                </table>

                <div class="divider-line"></div>

                <table class="info-table">
                    <tr>
                        <td style="width: 50%;"><span class="label">Cliente:</span> <?php echo safe_text($ordem, 'cliente_nome', 'N/A'); ?></td>
                        <td style="width: 25%;"><span class="label">CPF:</span> <?php echo safe_text($ordem, 'cliente_documento', 'N/A'); ?></td>
                        <td style="width: 25%;"><span class="label">Data e Hora:</span> <?php echo isset($ordem['created_at']) ? date('d/m/Y H:i', strtotime($ordem['created_at'])) : date('d/m/Y H:i'); ?></td>
                    </tr>
                    <tr>
                        <td><span class="label">Equipamento:</span> <?php echo safe_text($ordem, 'equipamento_tipo', 'N/A'); ?></td>
                        <td><span class="label">Marca:</span> <?php echo safe_text($ordem, 'equipamento_marca', 'N/A'); ?></td>
                        <td><span class="label">Fonte:</span> <?php echo ($ordem['equipamento_fonte'] ?? 0) ? 'Sim' : 'Não'; ?></td>
                    </tr>
                    <tr>
                        <td><span class="label">Senha:</span> <?php echo safe_text($ordem, 'equipamento_senha', 'N/A'); ?></td>
                        <td><span class="label">Garantia:</span> <?php echo (isset($ordem['status_nome']) && $ordem['status_nome'] == 'Garantia') ? 'Sim' : 'Não'; ?></td>
                        <td><span class="label">Serial:</span> <?php echo safe_text($ordem, 'equipamento_serial', 'N/A'); ?></td>
                    </tr>
                </table>

                <div class="problem-section">
                    <span class="problem-title">Problema Relatado:</span> <?php echo safe_text($ordem, 'defeito_relatado', 'Não informado'); ?>
                </div>

                <div class="divider-line"></div>

                <div class="terms-section">
                    <?php if (isset($ordem['status_nome']) && $ordem['status_nome'] == 'Garantia'): ?>
                        <p style="font-size: 18px; font-weight: bold; margin: 5px 0;">*EM GARANTIA</p>
                    <?php else: ?>
                        <p style="font-weight: bold; margin: 5px 0;">*Será cobrado um valor R$ 100,00 mão de obra (HORA TÉCNICA), caso o cliente não autorize a realização do serviço (ORÇAMENTO).</p>
                    <?php endif; ?>
                    <p style="margin: 2px 0;">*Não nos responsabilizamos pela origem e software dos equipamentos depositados para orçamentos.<br>
                    *O equipamento somente será entregue com a apresentação da ordem de serviço ou documento com foto somente para o proprietário.</p>
                    <p style="margin: 2px 0;">*Equipamentos não retirados no prazo de 30 dias após a da data de conclusão do serviço, serão considerado abandonados e será cobrado uma taxa diária de R$ 2,00 (dois reais) para fins de armazenamento, contar a partir da data de conclusão do serviço até a data de retirada do equipamento. Caso esse prazo de armazenamento seja superior a 90 dias, autorizo desde já a doação do equipamento à Myranda Informatica para que essa possa cobrir todos os custos de armazenagem, bem como doar, vender, reciclar ou mesmo descartar de forma correta o equipamento.</p>
                </div>

                <?php if ($i == 1): // Apenas na segunda via (via do cliente) ?>
                    <div class="date-row">
                        ______de___________________de <?php echo date('Y'); ?>
                        <br>
                        <span style="font-size: 10px;">Data de Retirada</span>
                    </div>

                    <table class="signature-table">
                        <tr>
                            <td class="sig-box">Autorização de Orçamento</td>
                            <td style="width: 10%;"></td>
                            <td class="sig-box">Retirada do Equipamento</td>
                        </tr>
                    </table>
                <?php else: ?>
                    <div style="height: 60px;"></div> <!-- Espaço para manter o tamanho similar -->
                    <table class="signature-table">
                        <tr>
                            <td class="sig-box">Assinatura do Cliente</td>
                            <td style="width: 10%;"></td>
                            <td class="sig-box">Assinatura da Empresa</td>
                        </tr>
                    </table>
                <?php endif; ?>
            </div>

            <?php if ($i == 0): ?>
                <div class="cut-line">
                    <span class="cut-icon">✂--------------------------------------------------------------------------------------------------</span>
                </div>
            <?php endif; ?>
        <?php endfor; ?>
    </div>
</body>
</html>
