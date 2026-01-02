<?php
/**
 * MODELO DE IMPRESSÃO DE ORÇAMENTO PROFISSIONAL
 */
$ordem = $ordem ?? [];
$itens = $itens ?? [];

// Função auxiliar para formatar moeda
function formatCurrency($value) {
    return 'R$ ' . number_format($value, 2, ',', '.');
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Orçamento OS #<?php echo $ordem['id'] ?? ''; ?></title>
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #e74c3c;
            --accent-color: #3498db;
            --text-dark: #333;
            --text-light: #7f8c8d;
            --bg-light: #f9f9f9;
            --border-color: #ecf0f1;
        }

        html, body {
            margin: 0;
            padding: 0;
            background: #fff;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: var(--text-dark);
            font-size: 12px;
        }

        @page {
            size: A4;
            margin: 15mm;
        }

        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        .container {
            width: 100%;
            max-width: 210mm;
            margin: 0 auto;
            padding: 0;
        }

        /* CABEÇALHO */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid var(--primary-color);
        }

        .logo-area img {
            max-width: 250px;
            height: auto;
        }

        .company-info {
            text-align: right;
        }

        .company-info h1 {
            margin: 0;
            color: var(--primary-color);
            font-size: 24px;
            text-transform: uppercase;
        }

        .company-info p {
            margin: 2px 0;
            color: var(--text-light);
            font-size: 11px;
        }

        /* TÍTULO DO DOCUMENTO */
        .document-title {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: var(--primary-color);
            color: #fff;
            padding: 10px 20px;
            border-radius: 5px;
            margin-bottom: 25px;
        }

        .document-title h2 {
            margin: 0;
            font-size: 18px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .os-number {
            font-size: 16px;
            font-weight: bold;
        }

        /* GRIDS DE INFORMAÇÕES */
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 25px;
        }

        .info-box {
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 15px;
            background-color: var(--bg-light);
        }

        .info-box h3 {
            margin: 0 0 10px 0;
            font-size: 13px;
            color: var(--primary-color);
            border-bottom: 1px solid var(--primary-color);
            padding-bottom: 5px;
            text-transform: uppercase;
        }

        .info-item {
            margin-bottom: 5px;
            display: flex;
        }

        .info-label {
            font-weight: bold;
            width: 100px;
            color: var(--text-dark);
        }

        .info-value {
            flex: 1;
            color: var(--text-dark);
        }

        /* TABELA DE ITENS */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }

        .items-table th {
            background-color: var(--primary-color);
            color: #fff;
            text-align: left;
            padding: 10px;
            text-transform: uppercase;
            font-size: 11px;
        }

        .items-table td {
            padding: 10px;
            border-bottom: 1px solid var(--border-color);
        }

        .items-table tr:nth-child(even) {
            background-color: #fcfcfc;
        }

        .text-right { text-align: right !important; }
        .text-center { text-align: center !important; }

        /* TOTAIS */
        .totals-area {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 30px;
        }

        .totals-box {
            width: 250px;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 10px;
            border-bottom: 1px solid var(--border-color);
        }

        .total-row.grand-total {
            background-color: var(--secondary-color);
            color: #fff;
            font-weight: bold;
            font-size: 16px;
            border-radius: 5px;
            margin-top: 5px;
        }

        /* LAUDO E OBSERVAÇÕES */
        .notes-area {
            margin-bottom: 30px;
        }

        .notes-box {
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 15px;
            min-height: 80px;
            white-space: pre-wrap;
            line-height: 1.5;
        }

        /* RODAPÉ E ASSINATURAS */
        .footer {
            margin-top: 50px;
        }

        .terms {
            font-size: 10px;
            color: var(--text-light);
            text-align: justify;
            margin-bottom: 40px;
            padding: 10px;
            border-left: 3px solid var(--secondary-color);
            background-color: #fff5f5;
        }

        .signatures {
            display: flex;
            justify-content: space-around;
            margin-top: 60px;
        }

        .signature-box {
            width: 200px;
            text-align: center;
            border-top: 1px solid var(--text-dark);
            padding-top: 5px;
        }

        @media print {
            body { background: none; }
            .container { width: 100%; }
        }
    </style>
</head>
<body>

<div class="container">
    <!-- CABEÇALHO -->
    <div class="header">
        <div class="logo-area">
            <img src="<?php echo BASE_URL; ?>assets/img/logo.png" alt="Logo Empresa">
        </div>
        <div class="company-info">
            <h1>Myranda Informática</h1>
            <p>CNPJ: 13.558.678/0001-36</p>
            <p>Av. Getúlio Vargas, 1144 - Centro - Osório/RS</p>
            <p>Fone: (51) 3663-6445 | WhatsApp: (51) 98359-1567</p>
            <p>E-mail: contato@myrandainformatica.com.br</p>
        </div>
    </div>

    <!-- TÍTULO DO DOCUMENTO -->
    <div class="document-title">
        <h2>Orçamento de Serviço</h2>
        <div class="os-number">Nº OS: <?php echo str_pad($ordem['id'] ?? '', 6, '0', STR_PAD_LEFT); ?></div>
    </div>

    <!-- INFORMAÇÕES DO CLIENTE E EQUIPAMENTO -->
    <div class="info-grid">
        <div class="info-box">
            <h3>Informações do Cliente</h3>
            <div class="info-item">
                <span class="info-label">Cliente:</span>
                <span class="info-value"><?php echo $ordem['cliente_nome'] ?? 'N/A'; ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">CPF/CNPJ:</span>
                <span class="info-value"><?php echo $ordem['cliente_documento'] ?? 'N/A'; ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">Telefone:</span>
                <span class="info-value"><?php echo $ordem['cliente_telefone'] ?? 'N/A'; ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">Data:</span>
                <span class="info-value"><?php echo isset($ordem['created_at']) ? date('d/m/Y H:i', strtotime($ordem['created_at'])) : date('d/m/Y H:i'); ?></span>
            </div>
        </div>
        <div class="info-box">
            <h3>Detalhes do Equipamento</h3>
            <div class="info-item">
                <span class="info-label">Equipamento:</span>
                <span class="info-value"><?php echo $ordem['equipamento_tipo'] ?? 'N/A'; ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">Marca/Mod.:</span>
                <span class="info-value"><?php echo ($ordem['equipamento_marca'] ?? '') . ' ' . ($ordem['equipamento_modelo'] ?? ''); ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">Serial:</span>
                <span class="info-value"><?php echo $ordem['equipamento_serial'] ?? 'N/A'; ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">Acessórios:</span>
                <span class="info-value"><?php echo $ordem['equipamento_acessorios'] ?? 'Nenhum'; ?></span>
            </div>
        </div>
    </div>

    <!-- TABELA DE ITENS -->
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 10%;">Tipo</th>
                <th style="width: 50%;">Descrição do Produto / Serviço</th>
                <th style="width: 10%;" class="text-center">Qtd</th>
                <th style="width: 15%;" class="text-right">Vlr. Unit.</th>
                <th style="width: 15%;" class="text-right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($itens)): ?>
                <tr>
                    <td colspan="5" class="text-center">Nenhum item adicionado a este orçamento.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($itens as $item): ?>
                    <tr>
                        <td class="text-center"><?php echo ucfirst($item['tipo_item'] ?? 'Item'); ?></td>
                        <td><?php echo $item['descricao'] ?? ''; ?></td>
                        <td class="text-center"><?php echo number_format($item['quantidade'] ?? 1, 2, ',', '.'); ?></td>
                        <td class="text-right"><?php echo formatCurrency(($item['valor_unitario'] ?? 0) + ($item['valor_mao_de_obra'] ?? 0)); ?></td>
                        <td class="text-right"><?php echo formatCurrency($item['valor_total'] ?? 0); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- TOTAIS -->
    <div class="totals-area">
        <div class="totals-box">
            <div class="total-row">
                <span>Total Produtos:</span>
                <span><?php echo formatCurrency($ordem['valor_total_produtos'] ?? 0); ?></span>
            </div>
            <div class="total-row">
                <span>Total Serviços:</span>
                <span><?php echo formatCurrency($ordem['valor_total_servicos'] ?? 0); ?></span>
            </div>
            <div class="total-row grand-total">
                <span>TOTAL GERAL:</span>
                <span><?php echo formatCurrency($ordem['valor_total_os'] ?? 0); ?></span>
            </div>
        </div>
    </div>

    <!-- LAUDO TÉCNICO -->
    <div class="notes-area">
        <div class="info-box" style="background-color: #fff;">
            <h3>Laudo Técnico / Diagnóstico</h3>
            <div class="notes-box"><?php echo !empty($ordem['laudo_tecnico']) ? $ordem['laudo_tecnico'] : 'Aguardando diagnóstico detalhado.'; ?></div>
        </div>
    </div>

    <!-- TERMOS E CONDIÇÕES -->
    <div class="footer">
        <div class="terms">
            <strong>Termos e Condições:</strong><br>
            1. Este orçamento é válido por 5 (cinco) dias a partir da data de emissão.<br>
            2. A garantia de serviços é de 90 dias, conforme o Código de Defesa do Consumidor.<br>
            3. Peças novas possuem garantia conforme o fabricante (mínimo 90 dias).<br>
            4. Equipamentos não retirados em até 90 dias após a conclusão do serviço serão considerados abandonados.<br>
            5. A aprovação deste orçamento autoriza a execução imediata dos serviços descritos.
        </div>

        <div class="signatures">
            <div class="signature-box">
                Myranda Informática
            </div>
            <div class="signature-box">
                Assinatura do Cliente
            </div>
        </div>
    </div>
</div>

<script>
    window.onload = function() {
        window.print();
    }
</script>
</body>
</html>
