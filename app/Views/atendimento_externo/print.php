<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Folha de Atendimento Externo #<?php echo $atendimento['id']; ?></title>
    <style>
        /* PADRONIZAÇÃO MYRANDA INFORMÁTICA */
        @page { margin: 10mm; }
        body {
            font-family: Helvetica, Arial, sans-serif;
            font-size: 13px; /* Seguindo o padrão de 13px do seu orçamento */
            color: #333;
            line-height: 1.4;
            margin: 0;
            padding: 0;
        }
        .container { width: 100%; }
        
        /* CABEÇALHO PADRONIZADO */
        .header {
            border-bottom: 2px solid #3498db;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .header-table { width: 100%; border-collapse: collapse; }
        .company-name { font-size: 18px; font-weight: bold; color: #2980b9; margin: 0; text-transform: uppercase; }
        .company-info { font-size: 11px; color: #7f8c8d; margin: 2px 0; }
        
        /* TÍTULO DO DOCUMENTO */
        .doc-title-box {
            background-color: #3498db;
            color: #ffffff;
            padding: 8px 15px;
            border-radius: 4px;
            margin-bottom: 15px;
        }
        .doc-title-table { width: 100%; border-collapse: collapse; }
        .doc-title { font-size: 14px; font-weight: bold; text-transform: uppercase; }
        .os-number { font-size: 14px; font-weight: bold; text-align: right; }
        
        /* SEÇÕES PADRONIZADAS */
        .section-title {
            background-color: #f2f2f2;
            padding: 5px 10px;
            font-weight: bold;
            border-left: 4px solid #3498db;
            margin-bottom: 10px;
            text-transform: uppercase;
            font-size: 11px;
        }
        
        /* TABELAS DE INFORMAÇÃO */
        .info-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        .info-table td { padding: 6px 8px; border: 1px solid #eee; vertical-align: top; }
        .label { font-weight: bold; width: 120px; background-color: #fafafa; font-size: 11px; }
        
        /* TABELAS DE ITENS/EQUIPAMENTOS */
        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        .items-table th { background-color: #2980b9; color: #ffffff; text-align: left; padding: 8px; font-size: 11px; text-transform: uppercase; }
        .items-table td { padding: 8px; border: 1px solid #eee; }
        
        /* CAIXAS DE TEXTO (OBSERVAÇÕES/LAUDO) */
        .notes-box { border: 1px solid #eee; padding: 10px; min-height: 60px; background-color: #fcfcfc; margin-bottom: 15px; }
        
        /* RODAPÉ E ASSINATURAS */
        .footer { margin-top: 30px; font-size: 10px; color: #7f8c8d; }
        .signature-table { width: 100%; margin-top: 40px; }
        .signature-box { width: 45%; text-align: center; border-top: 1px solid #333; padding-top: 5px; font-weight: bold; }
        
        .text-right { text-align: right; }
        .text-center { text-align: center; }
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
                        <h1 style="margin:0; color:#3498db; font-size: 20px;">ATENDIMENTO EXTERNO</h1>
                    </td>
                </tr>
            </table>
        </div>

        <div class="doc-title-box">
            <table class="doc-title-table">
                <tr>
                    <td class="doc-title">Folha de Visita Técnica</td>
                    <td class="os-number">Nº <?php echo str_pad($atendimento['id'], 6, '0', STR_PAD_LEFT); ?></td>
                </tr>
            </table>
        </div>

        <div class="section-title">Informações do Cliente</div>
        <table class="info-table">
            <tr>
                <td class="label">Cliente:</td>
                <td><?php echo htmlspecialchars($atendimento['cliente_nome']); ?></td>
                <td class="label">CPF/CNPJ:</td>
                <td><?php echo htmlspecialchars($atendimento['cliente_documento'] ?? 'N/A'); ?></td>
            </tr>
            <tr>
                <td class="label">Endereço:</td>
                <td><?php echo htmlspecialchars($atendimento['endereco_visita']); ?></td>
                <td class="label">Telefone:</td>
                <td><?php echo htmlspecialchars($atendimento['cliente_telefone'] ?? 'N/A'); ?></td>
            </tr>
        </table>

        <div class="section-title">Dados do Atendimento</div>
        <table class="info-table">
            <tr>
                <td class="label">Técnico:</td>
                <td><?php echo htmlspecialchars($atendimento['tecnico_nome'] ?? '__________________________'); ?></td>
                <td class="label">Data Visita:</td>
                <td><?php echo date('d/m/Y', strtotime($atendimento['data_agendada'] ?? $atendimento['created_at'])); ?></td>
            </tr>
            <tr>
                <td class="label">Status:</td>
                <td colspan="3"><?php echo ucfirst($atendimento['status']); ?></td>
            </tr>
        </table>

        <div class="section-title">Descrição do Problema</div>
        <div class="notes-box">
            <?php echo nl2br(htmlspecialchars($atendimento['descricao_problema'])); ?>
        </div>

        <div class="section-title">Serviços e Equipamentos</div>
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 25%;">Equipamento</th>
                    <th style="width: 35%;">Defeito Relatado</th>
                    <th style="width: 40%;">Serviço Realizado</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $equipamentos_salvos = !empty($atendimento['equipamentos']) ? explode(',', $atendimento['equipamentos']) : [];
                for ($i = 0; $i < 2; $i++): 
                    $eq_nome = isset($equipamentos_salvos[$i]) ? htmlspecialchars(trim($equipamentos_salvos[$i])) : '';
                ?>
                <tr>
                    <td style="height: 45px;"><?php echo $eq_nome; ?></td>
                    <td></td>
                    <td></td>
                </tr>
                <?php endfor; ?>
            </tbody>
        </table>

        <div class="section-title">Registro de Horários</div>
        <table class="info-table">
            <tr>
                <td class="label">Hora Entrada:</td>
                <td class="text-center"><?php echo $atendimento['hora_entrada'] ?: '____:____'; ?></td>
                <td class="label">Hora Saída:</td>
                <td class="text-center"><?php echo $atendimento['hora_saida'] ?: '____:____'; ?></td>
                <td class="label">Total Horas:</td>
                <td class="text-center" style="background-color: #f2f2f2; font-weight: bold;">
                    <?php echo $atendimento['tempo_total'] ?: '____ h ____ min'; ?>
                </td>
            </tr>
        </table>

        <div class="section-title">Observações Técnicas / Peças</div>
        <div class="notes-box" style="min-height: 80px;">
            <?php echo nl2br(htmlspecialchars($atendimento['observacoes_tecnicas'] ?? '')); ?>
        </div>

        <table class="signature-table">
            <tr>
                <td class="signature-box">Responsável Técnico</td>
                <td style="width: 10%;"></td>
                <td class="signature-box">Assinatura do Cliente</td>
            </tr>
        </table>

    </div>
</body>
</html>