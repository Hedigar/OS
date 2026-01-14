<?php
/**
 * MODELO DE IMPRESSÃO DE ORÇAMENTO PROFISSIONAL (PDF)
 */
$ordem = $ordem ?? [];
$itens = $itens ?? [];

if (!function_exists('formatCurrency')) {
    function formatCurrency($value) {
        return 'R$ ' . number_format((float)$value, 2, ',', '.');
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <?php
        $configModel = new \App\Models\ConfiguracaoGeral();
        $fontSize = $configModel->getValor('impressao_fonte_tamanho') ?: '13';
        $showObs = $configModel->getValor('impressao_exibir_observacoes') !== '0';
        
        $textoPadrao = "Será cobrado um valor R$ 100,00 mão de obra (HORA TÉCNICA), caso o cliente não autorize a realização do serviço (ORÇAMENTO).\n" .
                       "*Não nos responsabilizamos pela origem e software dos equipamentos depositados para orçamentos.\n" .
                       "*O equipamento somente será entregue com a apresentação da ordem de serviço ou documento com foto somente para o proprietário.\n" .
                       "*Equipamentos não retirados no prazo de 30 dias após a da data de conclusão do serviço, serão considerado abandonados e será cobrado uma taxa diária de R$ 2,00(dois reais) para fins de armazenamento, contar a partir da data de conclusão do serviço até a data de retirada do equipamento. Caso esse prazo de armazenamentoseja superior a 90 dias, autorizo desde já a doação do equipamento à Myranda Informatica para que essa possa cobrir todos os custos de armazenagem, bem comodoar, vender, reciclar ou mesmo descartar de forma correta o equipamento.";
        
        $textoObs = $configModel->getValor('impressao_texto_observacoes') ?: $textoPadrao;
    ?>
    <style>
        @page { margin: 10mm; }
        body {
            font-family: Helvetica, Arial, sans-serif;
            font-size: <?php echo $fontSize; ?>px;
            color: #333;
            line-height: 1.4;
            margin: 0;
            padding: 0;
        }
        .container { width: 100%; }
        .header {
            border-bottom: 2px solid #3498db;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .header-table { width: 100%; border-collapse: collapse; }
        .company-name { font-size: 18px; font-weight: bold; color: #2980b9; margin: 0; text-transform: uppercase; }
        .company-info { font-size: 11px; color: #7f8c8d; margin: 2px 0; }
        
        .doc-title-box {
            background-color: #3498db;
            color: #ffffff;
            padding: 8px 15px;
            border-radius: 4px;
            margin-bottom: 15px;
        }
        .doc-title-table { width: 100%; }
        .doc-title { font-size: 14px; font-weight: bold; text-transform: uppercase; }
        .os-number { font-size: 14px; font-weight: bold; text-align: right; }
        
        .section-title {
            background-color: #f2f2f2;
            padding: 5px 10px;
            font-weight: bold;
            border-left: 4px solid #3498db;
            margin-bottom: 10px;
            text-transform: uppercase;
            font-size: 11px;
        }
        .info-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        .info-table td { padding: 4px 8px; border: 1px solid #eee; vertical-align: top; }
        .label { font-weight: bold; width: 100px; background-color: #fafafa; }
        
        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        .items-table th { background-color: #2980b9; color: #ffffff; text-align: left; padding: 8px; font-size: 11px; text-transform: uppercase; }
        .items-table td { padding: 8px; border-bottom: 1px solid #eee; }
        
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        
        .totals-table { width: 250px; margin-left: auto; border-collapse: collapse; }
        .totals-table td { padding: 5px 10px; border-bottom: 1px solid #eee; }
        .grand-total { background-color: #27ae60; color: #ffffff; font-weight: bold; font-size: 13px; }
        .discount-row { background-color: #fff3cd; color: #856404; font-weight: bold; }
        .discount-badge { background-color: #e74c3c; color: #ffffff; padding: 2px 5px; border-radius: 3px; font-size: 10px; margin-left: 5px; }
        
        .notes-box { border: 1px solid #eee; padding: 10px; min-height: 60px; background-color: #fcfcfc; margin-bottom: 20px; }
        
        .footer { margin-top: 30px; font-size: 10px; color: #7f8c8d; }
        .terms { border-left: 3px solid #3498db; padding-left: 10px; margin-bottom: 30px; text-align: justify; }
        
        .signature-table { width: 100%; margin-top: 40px; }
        .signature-box { width: 45%; text-align: center; border-top: 1px solid #333; padding-top: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <table class="header-table">
                <tr>
                    <td>
                        <p class="company-name">Myranda Informática</p>
                        <p class="company-info">CNPJ: 13.558.678/0001-36 | Fone: (51) 3663-6445</p>
                        <p class="company-info">Av. Getúlio Vargas, 1144 - Centro - Osório/RS</p>
                    </td>
                    <td style="text-align: right;">
                        <h1 style="margin:0; color:#3498db;">ORÇAMENTO</h1>
                    </td>
                </tr>
            </table>
        </div>

        <div class="doc-title-box">
            <table class="doc-title-table">
                <tr>
                    <td class="doc-title">Orçamento de Serviço</td>
                    <td class="os-number">Nº <?php echo str_pad($ordem['id'] ?? '', 6, '0', STR_PAD_LEFT); ?></td>
                </tr>
            </table>
        </div>

        <div class="section-title">Dados do Cliente</div>
        <table class="info-table">
            <tr>
                <td class="label">Cliente:</td>
                <td><?php echo $ordem['cliente_nome'] ?? 'N/A'; ?></td>
                <td class="label">Data Emissão:</td>
                <td><?php echo date('d/m/Y H:i'); ?></td>
            </tr>
            <tr>
                <td class="label">Telefone:</td>
                <td><?php echo $ordem['cliente_telefone'] ?? 'N/A'; ?></td>
                <td class="label">Validade:</td>
                <td>5 dias</td>
         134	        </table>
135	
136	        <div class="section-title">Dados do Equipamento</div>
137	        <table class="info-table">
138	            <tr>
139	                <td class="label">Equipamento:</td>
140	                <td><?php echo ($ordem['equipamento_tipo'] ?? '') . ' ' . ($ordem['equipamento_marca'] ?? '') . ' ' . ($ordem['equipamento_modelo'] ?? ''); ?></td>
141	                <td class="label">Serial:</td>
142	                <td><?php echo $ordem['equipamento_serial'] ?? 'N/A'; ?></td>
143	            </tr>
144	            <tr>
145	                <td class="label">Acessórios:</td>
146	                <td colspan="3"><?php echo $ordem['equipamento_acessorios'] ?? 'Nenhum'; ?></td>
147	            </tr>
148	        </table>
149	
150	        <div class="section-title">Itens do Orçamento</div>      <table class="items-table">
            <thead>
                <tr>
                    <th>Descrição do Produto / Serviço</th>
                    <th class="text-center" style="width: 50px;">Qtd</th>
                    <th class="text-right" style="width: 90px;">Unitário</th>
                    <th class="text-right" style="width: 90px;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($itens)): ?>
                    <tr>
                        <td colspan="4" class="text-center">Nenhum item adicionado.</td>
                    </tr>
                <?php else: ?>
                    <?php 
                    $totalDescontoGeral = 0;
                    foreach ($itens as $item): 
                        $descontoItem = (float)($item['desconto'] ?? 0);
                        $totalDescontoGeral += $descontoItem;
                    ?>
                        <tr>
                            <td>
                                <?php echo $item['descricao'] ?? ''; ?>
                                <?php if ($descontoItem > 0): ?>
                                    <span class="discount-badge">DESC. -<?php echo formatCurrency($descontoItem); ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center"><?php echo number_format($item['quantidade'] ?? 1, 0, ',', '.'); ?></td>
                            <td class="text-right"><?php echo formatCurrency(($item['valor_unitario'] ?? 0) + ($item['valor_mao_de_obra'] ?? 0)); ?></td>
                            <td class="text-right"><?php echo formatCurrency($item['valor_total'] ?? 0); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <table class="totals-table">
            <tr>
                <td>Total Produtos:</td>
                <td class="text-right"><?php echo formatCurrency($ordem['valor_total_produtos'] ?? 0); ?></td>
            </tr>
            <tr>
                <td>Total Serviços:</td>
                <td class="text-right"><?php echo formatCurrency($ordem['valor_total_servicos'] ?? 0); ?></td>
            </tr>
            <?php if ($totalDescontoGeral > 0): ?>
            <tr class="discount-row">
                <td>DESCONTO TOTAL:</td>
                <td class="text-right">- <?php echo formatCurrency($totalDescontoGeral); ?></td>
            </tr>
            <?php endif; ?>
            <tr class="grand-total">
                <td>TOTAL ORÇAMENTO:</td>
                <td class="text-right"><?php echo formatCurrency($ordem['valor_total_os'] ?? 0); ?></td>
            </tr>
        </table>

        <?php if ($showObs): ?>
        <div class="section-title">Observações / Laudo</div>
        <div class="notes-box"><?php echo !empty($ordem['laudo_tecnico']) ? $ordem['laudo_tecnico'] : 'Diagnóstico técnico conforme relatado.'; ?></div>
        <?php endif; ?>

        <div class="footer">
            <div class="terms">
                <strong>Informações Importantes:</strong><br>
                <?php echo nl2br($textoObs); ?>
            </div>

            <table class="signature-table">
                <tr>
                    <td class="signature-box">Aprovação do Cliente</td>
                    <td style="width: 10%;"></td>
                    <td class="signature-box">Responsável Técnico</td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>
</html>
