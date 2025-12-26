<?php
// Não inclui o layout principal para ter uma página limpa para impressão

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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Imprimir OS #<?php echo htmlspecialchars($ordem['id']); ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            color: #000;
            background-color: #fff;
        }
        .os-container {
            max-width: 800px;
            margin: 0 auto;
            border: 1px solid #ccc;
            padding: 20px;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #e74c3c;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header h1 {
            color: #e74c3c;
            font-size: 1.5em;
            margin: 0;
        }
        .header p {
            font-size: 0.9em;
            margin: 0;
        }
        .section-title {
            font-size: 1.1em;
            color: #e74c3c;
            border-bottom: 1px solid #eee;
            padding-bottom: 5px;
            margin-top: 20px;
            margin-bottom: 10px;
        }
        .details-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            margin-bottom: 10px;
        }
        .details-grid div {
            padding: 5px 0;
        }
        .details-grid div strong {
            display: block;
            font-size: 0.8em;
            color: #555;
        }
        .details-grid div span {
            font-size: 0.9em;
            color: #000;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            font-size: 0.9em;
        }
        th {
            background-color: #f2f2f2;
            color: #333;
        }
        .totals-table {
            width: 50%;
            float: right;
            margin-top: 10px;
        }
        .totals-table td {
            border: none;
            padding: 5px 8px;
        }
        .totals-table tr:last-child td {
            border-top: 2px solid #e74c3c;
            font-weight: bold;
            font-size: 1em;
        }
        .notes {
            margin-top: 30px;
            border-top: 1px solid #ccc;
            padding-top: 10px;
        }
        .notes p {
            font-size: 0.85em;
            white-space: pre-wrap;
        }
        @media print {
            .os-container {
                border: none;
                padding: 0;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="os-container">
        <div class="header">
            <div>
                <h1>ORDEM DE SERVIÇO</h1>
                <p>Nº: <?php echo htmlspecialchars($ordem['id']); ?></p>
            </div>
            <div>
                <p>Data de Abertura: <?php echo date('d/m/Y H:i', strtotime($ordem['data_abertura'])); ?></p>
                <p>Status: <span style="color: <?php echo htmlspecialchars($ordem['status_cor']); ?>; font-weight: bold;"><?php echo htmlspecialchars($ordem['status_nome']); ?></span></p>
            </div>
        </div>

        <div class="section-title">DADOS DO CLIENTE</div>
        <div class="details-grid">
            <div>
                <strong>Nome:</strong>
                <span><?php echo htmlspecialchars($ordem['cliente_nome']); ?></span>
            </div>
            <div>
                <strong>Telefone:</strong>
                <span><?php echo htmlspecialchars($ordem['telefone_principal'] ?? 'N/A'); ?></span>
            </div>
            <div>
                <strong>E-mail:</strong>
                <span><?php echo htmlspecialchars($ordem['email'] ?? 'N/A'); ?></span>
            </div>
        </div>

        <div class="section-title">DETALHES DO EQUIPAMENTO</div>
        <div class="details-grid" style="grid-template-columns: 1fr 1fr 1fr;">
            <div>
                <strong>Equipamento:</strong>
                <span><?php echo htmlspecialchars($ordem['equipamento']); ?></span>
            </div>
            <div>
                <strong>Tipo / Marca / Modelo:</strong>
                <span><?php echo htmlspecialchars($ordem['tipo_equipamento'] ?? 'N/A'); ?> / <?php echo htmlspecialchars($ordem['marca_equipamento'] ?? 'N/A'); ?> / <?php echo htmlspecialchars($ordem['modelo_equipamento'] ?? 'N/A'); ?></span>
            </div>
            <div>
                <strong>Serial / Senha:</strong>
                <span><?php echo htmlspecialchars($ordem['serial_equipamento'] ?? 'N/A'); ?> / <?php echo htmlspecialchars($ordem['senha_equipamento'] ?? 'N/A'); ?></span>
            </div>
            <div style="grid-column: 1 / -1;">
                <strong>Acessórios Deixados:</strong>
                <span><?php echo htmlspecialchars($ordem['acessorios_equipamento'] ?? 'Nenhum'); ?></span>
            </div>
            <div>
                <strong>Fonte de Alimentação:</strong>
                <span><?php echo ($ordem['fonte_equipamento'] == 'sim' ? 'Deixou' : 'Não Deixou'); ?></span>
            </div>
        </div>

        <div class="section-title">DEFEITO E SERVIÇO</div>
        <div class="details-grid" style="grid-template-columns: 1fr 1fr;">
            <div>
                <strong>Defeito Relatado (Recepção):</strong>
                <p style="margin: 5px 0;"><?php echo htmlspecialchars($ordem['defeito']); ?></p>
            </div>
            <div>
                <strong>Serviço Realizado (Técnico):</strong>
                <p style="margin: 5px 0;"><?php echo htmlspecialchars($ordem['servico'] ?? 'Aguardando serviço.'); ?></p>
            </div>
            <div style="grid-column: 1 / -1;">
                <strong>Observações Técnicas:</strong>
                <p style="margin: 5px 0;"><?php echo htmlspecialchars($ordem['observacoes_tecnicas'] ?? 'Nenhuma observação técnica.'); ?></p>
            </div>
        </div>

        <div class="section-title">ITENS (PRODUTOS E SERVIÇOS)</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 10%;">Tipo</th>
                    <th style="width: 50%;">Descrição</th>
                    <th style="width: 10%; text-align: right;">Qtd</th>
                    <th style="width: 15%; text-align: right;">Vlr Unit.</th>
                    <th style="width: 15%; text-align: right;">Vlr Total</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($itens)): ?>
                    <tr>
                        <td colspan="5" style="text-align: center;">Nenhum item registrado.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($itens as $item): ?>
                        <tr>
                            <td><?php echo ucfirst($item['tipo']); ?></td>
                            <td><?php echo htmlspecialchars($item['descricao']); ?></td>
                            <td style="text-align: right;"><?php echo htmlspecialchars($item['quantidade']); ?></td>
                            <td style="text-align: right;"><?php echo formatCurrency($item['valor_unitario']); ?></td>
                            <td style="text-align: right;"><?php echo formatCurrency($item['valor_total']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <table class="totals-table">
            <tr>
                <td style="text-align: right;">Total Produtos:</td>
                <td style="text-align: right;"><?php echo formatCurrency($ordem['valor_total_produtos']); ?></td>
            </tr>
            <tr>
                <td style="text-align: right;">Total Serviços:</td>
                <td style="text-align: right;"><?php echo formatCurrency($ordem['valor_total_servicos']); ?></td>
            </tr>
            <tr>
                <td style="text-align: right;">TOTAL GERAL:</td>
                <td style="text-align: right;"><?php echo formatCurrency($ordem['valor_total_os']); ?></td>
            </tr>
        </table>

        <div style="clear: both;"></div>

        <div class="notes">
            <div style="width: 48%; float: left;">
                <p style="font-weight: bold;">Assinatura do Cliente:</p>
                <div style="border-bottom: 1px solid #000; height: 40px;"></div>
            </div>
            <div style="width: 48%; float: right;">
                <p style="font-weight: bold;">Assinatura do Técnico:</p>
                <div style="border-bottom: 1px solid #000; height: 40px;"></div>
            </div>
            <div style="clear: both;"></div>
        </div>
    </div>
</body>
</html>
