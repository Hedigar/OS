<?php
$current_page = 'ordens';
require_once __DIR__ . '/../layout/main.php';

$ordem = $ordem ?? [];
$itens = $itens ?? [];

// Função auxiliar para formatar moeda
function formatCurrency($value) {
    return 'R$ ' . number_format($value, 2, ',', '.');
}
?>

<div class="container">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
        <h1>📋 Ordem de Serviço #<?php echo htmlspecialchars($ordem['id']); ?></h1>
        <div style="display: flex; gap: 1rem;">
            <a href="<?php echo BASE_URL; ?>ordens/form?id=<?php echo htmlspecialchars($ordem['id']); ?>" class="btn btn-info">
                ✏️ Editar OS
            </a>
            <a href="<?php echo BASE_URL; ?>ordens/print?id=<?php echo htmlspecialchars($ordem['id']); ?>" target="_blank" class="btn btn-secondary">
                🖨️ Imprimir
            </a>
            <a href="<?php echo BASE_URL; ?>ordens" class="btn btn-secondary">
                ← Voltar
            </a>
        </div>
    </div>

    <!-- CARD DE DETALHES GERAIS -->
    <div class="card" style="margin-bottom: 2rem;">
        <h2 style="color: var(--text-primary); margin-bottom: 1.5rem; padding-bottom: 0.5rem; border-bottom: 2px solid var(--dark-tertiary);">
            Detalhes Gerais
        </h2>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem;">
            <!-- Cliente -->
            <div>
                <h3 style="color: var(--primary-red); margin-bottom: 0.5rem;">👥 Cliente</h3>
                <p style="margin: 0;"><?php echo htmlspecialchars($ordem['cliente_nome']); ?></p>
            </div>

            <!-- Status -->
            <div>
                <h3 style="color: var(--primary-red); margin-bottom: 0.5rem;">✅ Status</h3>
                <span style="display: inline-block; padding: 0.4rem 0.8rem; border-radius: 6px; font-weight: 600; color: #fff; background-color: <?php echo htmlspecialchars($ordem['status_cor']); ?>;">
                    <?php echo htmlspecialchars($ordem['status_nome']); ?>
                </span>
            </div>

            <!-- Data de Abertura -->
            <div>
                <h3 style="color: var(--primary-red); margin-bottom: 0.5rem;">📅 Abertura</h3>
                <p style="margin: 0;"><?php echo date('d/m/Y H:i', strtotime($ordem['data_abertura'])); ?></p>
            </div>
        </div>
    </div>

    <!-- CARD DE EQUIPAMENTO -->
    <div class="card" style="margin-bottom: 2rem;">
        <h2 style="color: var(--text-primary); margin-bottom: 1.5rem; padding-bottom: 0.5rem; border-bottom: 2px solid var(--dark-tertiary);">
            💻 Detalhes do Equipamento
        </h2>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem;">
            <div>
                <h3 style="color: var(--primary-red); margin-bottom: 0.5rem;">Equipamento</h3>
                <p style="margin: 0;"><?php echo htmlspecialchars($ordem['equipamento']); ?></p>
            </div>
            <div>
                <h3 style="color: var(--primary-red); margin-bottom: 0.5rem;">Tipo / Marca / Modelo</h3>
                <p style="margin: 0;"><?php echo htmlspecialchars($ordem['tipo_equipamento'] ?? 'N/A'); ?> / <?php echo htmlspecialchars($ordem['marca_equipamento'] ?? 'N/A'); ?> / <?php echo htmlspecialchars($ordem['modelo_equipamento'] ?? 'N/A'); ?></p>
            </div>
            <div>
                <h3 style="color: var(--primary-red); margin-bottom: 0.5rem;">Serial / Senha</h3>
                <p style="margin: 0;"><?php echo htmlspecialchars($ordem['serial_equipamento'] ?? 'N/A'); ?> / <?php echo htmlspecialchars($ordem['senha_equipamento'] ?? 'N/A'); ?></p>
            </div>
            <div>
                <h3 style="color: var(--primary-red); margin-bottom: 0.5rem;">Fonte / Acessórios</h3>
                <p style="margin: 0;"><?php echo ($ordem['fonte_equipamento'] == 'sim' ? 'Deixou' : 'Não Deixou'); ?> / <?php echo htmlspecialchars($ordem['acessorios_equipamento'] ?? 'Nenhum'); ?></p>
            </div>
        </div>
    </div>

    <!-- CARD DE PROBLEMA E SOLUÇÃO -->
    <div class="card" style="margin-bottom: 2rem;">
        <h2 style="color: var(--text-primary); margin-bottom: 1.5rem; padding-bottom: 0.5rem; border-bottom: 2px solid var(--dark-tertiary);">
            Laudo e Serviço
        </h2>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
            <div>
                <h3 style="color: var(--primary-red); margin-bottom: 0.5rem;">Defeito Relatado (Recepção)</h3>
                <p style="white-space: pre-wrap;"><?php echo htmlspecialchars($ordem['defeito']); ?></p>
            </div>
            <div>
                <h3 style="color: var(--primary-red); margin-bottom: 0.5rem;">Serviço Realizado / Solução (Técnico)</h3>
                <p style="white-space: pre-wrap;"><?php echo htmlspecialchars($ordem['servico'] ?? 'Nenhum serviço registrado.'); ?></p>
            </div>
            <div style="grid-column: 1 / -1;">
                <h3 style="color: var(--primary-red); margin-bottom: 0.5rem;">Observações Técnicas</h3>
                <p style="white-space: pre-wrap;"><?php echo htmlspecialchars($ordem['observacoes_tecnicas'] ?? 'Nenhuma observação técnica.'); ?></p>
            </div>
        </div>
    </div>

    <!-- CARD DE ITENS -->
    <div class="card" style="margin-bottom: 2rem;">
        <h2 style="color: var(--text-primary); margin-bottom: 1.5rem; padding-bottom: 0.5rem; border-bottom: 2px solid var(--dark-tertiary);">
            🛒 Produtos e Serviços
        </h2>

        <?php if (empty($itens)): ?>
            <div class="alert alert-info">Nenhum produto ou serviço adicionado a esta Ordem de Serviço.</div>
        <?php else: ?>
            <div style="overflow-x: auto;">
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
                        <?php foreach ($itens as $item): ?>
                            <tr>
                                <td>
                                    <span style="font-weight: 600; color: <?php echo ($item['tipo'] == 'servico') ? 'var(--info)' : 'var(--success)'; ?>;">
                                        <?php echo ucfirst($item['tipo']); ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars($item['descricao']); ?></td>
                                <td style="text-align: right;"><?php echo htmlspecialchars($item['quantidade']); ?></td>
                                <td style="text-align: right;"><?php echo formatCurrency($item['valor_unitario']); ?></td>
                                <td style="text-align: right; font-weight: 600;"><?php echo formatCurrency($item['valor_total']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4" style="text-align: right; font-weight: 600; color: var(--primary-red);">Total Produtos:</td>
                            <td style="text-align: right; font-weight: 600; color: var(--primary-red);"><?php echo formatCurrency($ordem['valor_total_produtos']); ?></td>
                        </tr>
                        <tr>
                            <td colspan="4" style="text-align: right; font-weight: 600; color: var(--primary-red);">Total Serviços:</td>
                            <td style="text-align: right; font-weight: 600; color: var(--primary-red);"><?php echo formatCurrency($ordem['valor_total_servicos']); ?></td>
                        </tr>
                        <tr>
                            <td colspan="4" style="text-align: right; font-weight: 700; font-size: 1.1rem; color: var(--text-primary);">TOTAL GERAL:</td>
                            <td style="text-align: right; font-weight: 700; font-size: 1.1rem; color: var(--text-primary);"><?php echo formatCurrency($ordem['valor_total_os']); ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
