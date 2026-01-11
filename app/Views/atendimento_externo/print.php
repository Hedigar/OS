<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Folha de Atendimento Externo #<?php echo $atendimento['id']; ?></title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; color: #333; line-height: 1.3; margin: 0; padding: 0; }
        .container { padding: 20px; }
        .header { text-align: center; margin-bottom: 15px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .header h1 { margin: 0; font-size: 16px; text-transform: uppercase; }
        .section { margin-bottom: 12px; }
        .section-title { font-weight: bold; background: #f2f2f2; padding: 4px 8px; border: 1px solid #333; margin-bottom: 4px; text-transform: uppercase; font-size: 10px; }
        .grid { display: table; width: 100%; border-collapse: collapse; }
        .row { display: table-row; }
        .col { display: table-cell; padding: 5px; border: 1px solid #333; vertical-align: top; }
        .label { font-weight: bold; }
        .field-box { border: 1px solid #333; min-height: 40px; padding: 5px; margin-top: 2px; }
        .footer { margin-top: 20px; text-align: center; font-size: 9px; border-top: 1px solid #ccc; padding-top: 5px; }
        .signature-grid { margin-top: 40px; width: 100%; }
        .signature-box { width: 45%; border-top: 1px solid #000; text-align: center; padding-top: 5px; display: inline-block; }
        .spacer { width: 8%; display: inline-block; }
        .equipment-table { width: 100%; border-collapse: collapse; margin-top: 5px; }
        .equipment-table th, .equipment-table td { border: 1px solid #333; padding: 6px; text-align: left; }
        .equipment-table th { background: #f9f9f9; font-size: 9px; }
        .time-box { border: 1px solid #333; padding: 8px; margin-top: 5px; background: #fff; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Folha de Atendimento Externo</h1>
            <p>Atendimento #<?php echo str_pad($atendimento['id'], 5, '0', STR_PAD_LEFT); ?> | Data: <?php echo date('d/m/Y', strtotime($atendimento['data_agendada'] ?? $atendimento['created_at'])); ?></p>
        </div>

        <div class="section">
            <div class="section-title">Informações do Cliente</div>
            <div class="grid">
                <div class="row">
                    <div class="col" style="width: 60%;"><span class="label">Cliente:</span> <?php echo htmlspecialchars($atendimento['cliente_nome']); ?></div>
                    <div class="col" style="width: 40%;"><span class="label">CPF/CNPJ:</span> <?php echo htmlspecialchars($atendimento['cliente_documento'] ?? 'N/A'); ?></div>
                </div>
                <div class="row">
                    <div class="col"><span class="label">Endereço:</span> <?php echo htmlspecialchars($atendimento['endereco_visita']); ?></div>
                    <div class="col"><span class="label">Telefone:</span> <?php echo htmlspecialchars($atendimento['cliente_telefone'] ?? 'N/A'); ?></div>
                </div>
            </div>
        </div>

        <div class="section">
            <div class="section-title">Dados do Atendimento</div>
            <div class="grid">
                <div class="row">
                    <div class="col" style="width: 50%;"><span class="label">Técnico Responsável:</span> <?php echo htmlspecialchars($atendimento['tecnico_nome'] ?? '__________________________'); ?></div>
                    <div class="col" style="width: 50%;"><span class="label">Status Inicial:</span> <?php echo ucfirst($atendimento['status']); ?></div>
                </div>
            </div>
        </div>

        <div class="section">
            <div class="section-title">Descrição do Problema / Solicitação Inicial</div>
            <div class="field-box">
                <?php echo nl2br(htmlspecialchars($atendimento['descricao_problema'])); ?>
            </div>
        </div>

        <div class="section">
            <div class="section-title">Equipamentos e Serviços Realizados</div>
            <table class="equipment-table">
                <thead>
                    <tr>
                        <th style="width: 25%;">Tipo de Equipamento</th>
                        <th style="width: 35%;">Defeito Relatado</th>
                        <th style="width: 40%;">O que foi feito</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    // Se já houver equipamentos salvos, podemos tentar listar, senão deixamos em branco
                    $equipamentos_salvos = !empty($atendimento['equipamentos']) ? explode(',', $atendimento['equipamentos']) : [];
                    for ($i = 0; $i < 3; $i++): 
                        $eq_nome = isset($equipamentos_salvos[$i]) ? htmlspecialchars(trim($equipamentos_salvos[$i])) : '';
                    ?>
                    <tr>
                        <td style="height: 60px;"><?php echo $eq_nome; ?></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <?php endfor; ?>
                </tbody>
            </table>
        </div>

        <div class="section">
            <div class="section-title">Registro de Horários e Tempo</div>
            <div class="time-box">
                <div class="grid">
                    <div class="row">
                        <div class="col" style="border: none;"><span class="label">Hora de Entrada:</span> <?php echo $atendimento['hora_entrada'] ?: '____:____'; ?></div>
                        <div class="col" style="border: none;"><span class="label">Hora de Saída:</span> <?php echo $atendimento['hora_saida'] ?: '____:____'; ?></div>
                        <div class="col" style="border: none;"><span class="label">Tempo Total:</span> <?php echo $atendimento['tempo_total'] ?: '________________'; ?></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="section">
            <div class="section-title">Observações Técnicas / Peças Utilizadas</div>
            <div class="field-box" style="min-height: 80px;">
                <?php echo nl2br(htmlspecialchars($atendimento['observacoes_tecnicas'] ?? '')); ?>
            </div>
        </div>

        <div class="signature-grid">
            <div class="signature-box">
                Assinatura do Técnico
            </div>
            <div class="spacer"></div>
            <div class="signature-box">
                Assinatura do Cliente
            </div>
        </div>

        <div class="footer">
            <p>Este documento serve como comprovante de prestação de serviço externo. Gerado em <?php echo date('d/m/Y H:i:s'); ?></p>
        </div>
    </div>
</body>
</html>
