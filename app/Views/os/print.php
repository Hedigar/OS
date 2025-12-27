<?php
/**
 * ESTA PÁGINA É TOTALMENTE ISOLADA.
 * O CSS ABAIXO AFETA APENAS ESTA PÁGINA DE IMPRESSÃO.
 */
$ordem = $ordem ?? [];
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Impressão OS #<?php echo $ordem['id'] ?? ''; ?></title>
    <style>
        /* CSS RESET ESPECÍFICO PARA ESTA PÁGINA */
        html, body {
            margin: 0 !important;
            padding: 0 !important;
            background: #ffffff !important; /* Fundo sempre branco */
            color: #000000 !important; /* Texto sempre preto */
            font-family: Arial, Helvetica, sans-serif !important;
            font-size: 12px !important;
        }

        @page {
            size: A4;
            margin: 10mm;
        }

        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        /* CONTAINER DA FOLHA A4 */
        .folha-a4 {
            width: 190mm;
            margin: 0 auto;
            background: #fff;
        }

        /* CADA VIA DA OS (METADE DA FOLHA) */
        .via-os {
            height: 138mm;
            padding: 5mm;
            position: relative;
            overflow: hidden;
            border: 1px solid #eee; /* Borda leve apenas para visualização na tela */
            margin-bottom: 2mm;
        }

        @media print {
            .via-os {
                border: none; /* Remove borda na impressão */
            }
        }

        /* LINHA DE CORTE */
        .linha-corte {
            border-top: 1px dashed #000;
            margin: 5mm 0;
            position: relative;
            text-align: center;
        }
        
        .linha-corte:after {
            content: "✂ Corte aqui";
            position: absolute;
            top: -10px;
            background: #fff;
            padding: 0 10px;
            font-size: 10px;
            color: #666;
        }

        /* CABEÇALHO */
        .header-print {
            display: flex;
            justify-content: space-between;
            border-bottom: 2px solid #000;
            padding-bottom: 3mm;
            margin-bottom: 5mm;
        }

        .empresa-nome {
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .os-numero {
            font-size: 20px;
            font-weight: bold;
        }

        /* BLOCOS DE DADOS */
        .secao {
            margin-bottom: 4mm;
        }

        .secao-titulo {
            font-weight: bold;
            text-transform: uppercase;
            font-size: 11px;
            background: #f2f2f2 !important;
            padding: 1mm 2mm;
            border: 1px solid #000;
            display: block;
            margin-bottom: 2mm;
        }

        .dados-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2mm;
            padding: 0 2mm;
        }

        .dado-item {
            margin-bottom: 1mm;
        }

        .label {
            font-weight: bold;
        }

        /* TEXTO LEGAL */
        .termos {
            font-size: 9px;
            text-align: justify;
            line-height: 1.2;
            margin-top: 5mm;
            padding: 0 2mm;
        }

        /* ASSINATURAS */
        .assinaturas-area {
            display: flex;
            justify-content: space-between;
            margin-top: 8mm;
            padding: 0 5mm;
        }

        .campo-assinatura {
            width: 45%;
            text-align: center;
            border-top: 1px solid #000;
            padding-top: 1mm;
            font-size: 10px;
        }

        /* UTILITÁRIOS */
        .text-right { text-align: right; }
        .bold { font-weight: bold; }
    </style>
</head>
<body>

<div class="folha-a4">
    <?php for ($i = 0; $i < 2; $i++): ?>
    <div class="via-os">
        <div class="header-print">
            <div>
                <div class="empresa-nome">Myranda Informática</div>
                <div style="font-size: 10px;">
                    CNPJ: 13.558.678/0001-36<br>
                    Av. Getúlio Vargas, 1144 - Centro - Osório/RS<br>
                    Fone: (51) 3663-6445 | WhatsApp: (51) 98359-1567
                </div>
            </div>
            <div class="text-right">
                <div class="os-numero">O.S. #<?php echo str_pad($ordem['id'] ?? '', 5, '0', STR_PAD_LEFT); ?></div>
                <div class="bold">Data: <?php echo isset($ordem['created_at']) ? date('d/m/Y H:i', strtotime($ordem['created_at'])) : date('d/m/Y H:i'); ?></div>
                <div style="font-size: 11px; margin-top: 2mm;">Via: <?php echo ($i === 0) ? 'EMPRESA' : 'CLIENTE'; ?></div>
            </div>
        </div>

        <div class="secao">
            <span class="secao-titulo">Dados do Cliente</span>
            <div class="dados-grid">
                <div class="dado-item"><span class="label">Cliente:</span> <?php echo $ordem['cliente_nome'] ?? ''; ?></div>
                <div class="dado-item"><span class="label">CPF/CNPJ:</span> <?php echo $ordem['cliente_documento'] ?? ''; ?></div>
                <div class="dado-item"><span class="label">Telefone:</span> <?php echo $ordem['cliente_telefone'] ?? ''; ?></div>
                <div class="dado-item"><span class="label">Atendente:</span> <?php echo $ordem['atendente_nome'] ?? ''; ?></div>
            </div>
        </div>

        <div class="secao">
            <span class="secao-titulo">Informações do Equipamento</span>
            <div class="dados-grid">
                <div class="dado-item"><span class="label">Equipamento:</span> <?php echo $ordem['equipamento_tipo'] ?? ''; ?></div>
                <div class="dado-item"><span class="label">Marca/Modelo:</span> <?php echo $ordem['equipamento_marca'] ?? ''; ?></div>
                <div class="dado-item"><span class="label">Fonte:</span> <?php echo ($ordem['equipamento_fonte'] ?? 0) ? 'Sim' : 'Não'; ?></div>
                <div class="dado-item"><span class="label">Garantia:</span> <?php echo ($ordem['equipamento_garantia'] ?? 0) ? 'Sim' : 'Não'; ?></div>
                <div class="dado-item"><span class="label">Senha:</span> <?php echo $ordem['equipamento_senha'] ?? 'N/A'; ?></div>
            </div>
        </div>

        <div class="secao">
            <span class="secao-titulo">Problema Relatado</span>
            <div style="padding: 0 2mm; min-height: 12mm;">
                <?php echo nl2br($ordem['defeito_relatado'] ?? 'Não informado'); ?>
            </div>
        </div>

        <div class="termos">
            * Será cobrado R$ 100,00 (HORA TÉCNICA) caso o orçamento não seja autorizado.
            * Não nos responsabilizamos por software ou origem dos equipamentos.
            * Retirada apenas com esta OS ou documento do proprietário.
            * Equipamentos não retirados em 30 dias terão taxa de R$ 2,00/dia. Após 90 dias, serão considerados abandonados.
        </div>

        <div class="assinaturas-area">
            <div class="campo-assinatura">Data: ____/____/____</div>
            <div class="campo-assinatura">Assinatura</div>
        </div>
    </div>

    <?php if ($i === 0): ?>
        <div class="linha-corte"></div>
    <?php endif; ?>

    <?php endfor; ?>
</div>

<script>
    window.onload = function() {
        window.print();
        // window.onafterprint = function() { window.close(); };
    }
</script>
</body>
</html>
