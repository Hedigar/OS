<?php
/**
 * MODELO DE IMPRESSÃO DE ORDEM DE SERVIÇO PROFISSIONAL (PDF)
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
    <style>
        @page {
            margin: 10mm;
        }
        body {
            font-family: Helvetica, Arial, sans-serif;
            font-size: 11px;
            color: #333;
            line-height: 1.4;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 100%;
        }
        .header {
            border-bottom: 2px solid #2c3e50;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
        }
        .logo-cell {
            width: 40%;
        }
        .company-cell {
            width: 60%;
            text-align: right;
        }
        .company-name {
            font-size: 18px;
            font-weight: bold;
            color: #2c3e50;
            margin: 0;
            text-transform: uppercase;
        }
        .company-info {
            font-size: 10px;
            color: #7f8c8d;
            margin: 2px 0;
        }
        .doc-title-box {
            background-color: #2c3e50;
            color: #ffffff;
            padding: 8px 15px;
            border-radius: 4px;
            margin-bottom: 15px;
        }
        .doc-title-table {
            width: 100%;
        }
        .doc-title {
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .os-number {
            font-size: 14px;
            font-weight: bold;
            text-align: right;
        }
        .section-title {
            background-color: #f2f2f2;
            padding: 5px 10px;
            font-weight: bold;
            border-left: 4px solid #2c3e50;
            margin-bottom: 10px;
            text-transform: uppercase;
            font-size: 10px;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .info-table td {
            padding: 4px 8px;
            border: 1px solid #eee;
            vertical-align: top;
        }
        .label {
            font-weight: bold;
            width: 100px;
            background-color: #fafafa;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .items-table th {
            background-color: #2c3e50;
            color: #ffffff;
            text-align: left;
            padding: 8px;
            font-size: 10px;
            text-transform: uppercase;
        }
        .items-table td {
            padding: 8px;
            border-bottom: 1px solid #eee;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .totals-table {
            width: 250px;
            margin-left: auto;
            border-collapse: collapse;
        }
        .totals-table td {
            padding: 5px 10px;
            border-bottom: 1px solid #eee;
        }
        .grand-total {
            background-color: #e74c3c;
            color: #ffffff;
            font-weight: bold;
            font-size: 13px;
        }
        .notes-box {
            border: 1px solid #eee;
            padding: 10px;
            min-height: 60px;
            background-color: #fcfcfc;
            margin-bottom: 20px;
        }
        .footer {
            margin-top: 30px;
            font-size: 9px;
            color: #7f8c8d;
        }
        .terms {
            border-left: 3px solid #e74c3c;
            padding-left: 10px;
            margin-bottom: 30px;
            text-align: justify;
        }
        .signature-table {
            width: 100%;
            margin-top: 40px;
        }
        .signature-box {
            width: 45%;
            text-align: center;
            border-top: 1px solid #333;
            padding-top: 5px;
        }
        .spacer { width: 10%; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <table class="header-table">
                <tr>
                    <td class="logo-cell">
                        <!-- Se houver logo, Dompdf precisa do caminho absoluto ou base64 -->
                        <h1 style="margin:0; color:#2c3e50;">ASSISTÊNCIA TÉCNICA</h1>
                    </td>
                    <td class="company-cell">
                        <p class="company-name">Myranda Informática</p>
                        <p class="company-info">CNPJ: 13.558.678/0001-36</p>
                        <p class="company-info">Av. Getúlio Vargas, 1144 - Centro - Osório/RS</p>
                        <p class="company-info">Fone: (51) 3663-6445 | WhatsApp: (51) 98359-1567</p>
                    </td>
                </tr>
            </table>
        </div>

        <div class="doc-title-box">
            <table class="doc-title-table">
                <tr>
                    <td class="doc-title">Ordem de Serviço</td>
                    <td class="os-number">Nº <?php echo str_pad($ordem['id'] ?? '', 6, '0', STR_PAD_LEFT); ?></td>
                </tr>
            </table>
        </div>

        <div class="section-title">Informações do Cliente</div>
        <table class="info-table">
            <tr>
                <td class="label">Cliente:</td>
                <td><?php echo $ordem['cliente_nome'] ?? 'N/A'; ?></td>
                <td class="label">CPF/CNPJ:</td>
                <td><?php echo $ordem['cliente_documento'] ?? 'N/A'; ?></td>
            </tr>
            <tr>
                <td class="label">Telefone:</td>
                <td><?php echo $ordem['cliente_telefone'] ?? 'N/A'; ?></td>
                <td class="label">Data/Hora:</td>
                <td><?php echo isset($ordem['created_at']) ? date('d/m/Y H:i', strtotime($ordem['created_at'])) : date('d/m/Y H:i'); ?></td>
            </tr>
            <tr>
                <td class="label">Endereço:</td>
                <td colspan="3"><?php echo $ordem['cliente_endereco'] ?? 'Não informado'; ?></td>
            </tr>
        </table>

        <div class="section-title">Detalhes do Equipamento</div>
        <table class="info-table">
            <tr>
                <td class="label">Equipamento:</td>
                <td><?php echo $ordem['equipamento_tipo'] ?? 'N/A'; ?></td>
                <td class="label">Marca/Modelo:</td>
                <td><?php echo ($ordem['equipamento_marca'] ?? '') . ' ' . ($ordem['equipamento_modelo'] ?? ''); ?></td>
            </tr>
            <tr>
                <td class="label">Nº Serial:</td>
                <td><?php echo $ordem['equipamento_serial'] ?? 'N/A'; ?></td>
                <td class="label">Acessórios:</td>
                <td><?php echo $ordem['equipamento_acessorios'] ?? 'Nenhum'; ?></td>
            </tr>
        </table>

        <div class="section-title">Defeito Relatado</div>
        <div class="notes-box"><?php echo $ordem['defeito_relatado'] ?? 'Não informado.'; ?></div>

        <div class="section-title">Serviços e Peças</div>
        <table class="items-table">
            <thead>
                <tr>
                    <th>Descrição</th>
                    <th class="text-center" style="width: 60px;">Qtd</th>
                    <th class="text-right" style="width: 100px;">Unitário</th>
                    <th class="text-right" style="width: 100px;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($itens)): ?>
                    <tr>
                        <td colspan="4" class="text-center">Nenhum item registrado.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($itens as $item): ?>
                        <tr>
                            <td><?php echo $item['descricao'] ?? ''; ?></td>
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
            <tr class="grand-total">
                <td>TOTAL GERAL:</td>
                <td class="text-right"><?php echo formatCurrency($ordem['valor_total_os'] ?? 0); ?></td>
            </tr>
        </table>

        <div class="section-title">Laudo Técnico / Diagnóstico</div>
        <div class="notes-box"><?php echo !empty($ordem['laudo_tecnico']) ? $ordem['laudo_tecnico'] : 'Aguardando diagnóstico.'; ?></div>

        <div class="footer">
            <div class="terms">
                <strong>Termos:</strong> A garantia de serviços é de 90 dias. Peças novas conforme fabricante. Equipamentos não retirados em 90 dias serão considerados abandonados.
            </div>

            <table class="signature-table">
                <tr>
                    <td class="signature-box">Assinatura do Técnico</td>
                    <td class="spacer"></td>
                    <td class="signature-box">Assinatura do Cliente</td>
                </tr>
            </table>
            <p style="text-align: center; margin-top: 20px;">Gerado em <?php echo date('d/m/Y H:i'); ?> - Sistema de Gestão</p>
        </div>
    </div>
</body>
</html>
