<?php
$current_page = 'ordens';
require_once __DIR__ . '/../layout/main.php';

$ordem = $ordem ?? [];
$itens = $itens ?? [];

// Função auxiliar para formatar moeda
function formatCurrency($value) {
    return 'R$ ' . number_format($value, 2, ',', '.');
}

// Helpers seguros para evitar warnings/deprecations ao acessar chaves
function safe_text(array $arr, string $key, string $default = ''): string
{
    $val = $arr[$key] ?? $default;
    return htmlspecialchars((string)$val, ENT_QUOTES, 'UTF-8');
}

function safe_val(array $arr, string $key, $default = null)
{
    return $arr[$key] ?? $default;
}
?>

<div class="container">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
        <h1>📋 Ordem de Serviço #<?php echo safe_text($ordem, 'id', 'N/A'); ?></h1>
        <div style="display: flex; gap: 1rem;">
            <a href="<?php echo BASE_URL; ?>ordens/form?id=<?php echo safe_text($ordem, 'id', ''); ?>" class="btn btn-info">
                ✏️ Editar OS
            </a>
                <a href="<?php echo BASE_URL; ?>ordens/print?id=<?php echo safe_text($ordem, 'id', ''); ?>" target="_blank" class="btn btn-secondary">
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
                <p style="margin: 0;"><?php echo safe_text($ordem, 'cliente_nome', 'N/A'); ?></p>
            </div>

            <!-- Status -->
            <div>
                <h3 style="color: var(--primary-red); margin-bottom: 0.5rem;">✅ Status</h3>
                <?php $status_cor = safe_text($ordem, 'status_cor', '#777'); ?>
                <span style="display: inline-block; padding: 0.4rem 0.8rem; border-radius: 6px; font-weight: 600; color: #fff; background-color: <?php echo $status_cor; ?>;">
                    <?php echo safe_text($ordem, 'status_nome', '—'); ?>
                </span>
            </div>

            <!-- Data de Abertura -->
            <div>
                <h3 style="color: var(--primary-red); margin-bottom: 0.5rem;">📅 Abertura</h3>
                <?php
                    $dataAbert = safe_val($ordem, 'data_abertura', null) ?: safe_val($ordem, 'created_at', null);
                    if (!empty($dataAbert) && strtotime((string)$dataAbert) !== false) {
                        echo '<p style="margin: 0;">' . date('d/m/Y H:i', strtotime((string)$dataAbert)) . '</p>';
                    } else {
                        echo '<p style="margin: 0;">—</p>';
                    }
                ?>
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
                <?php
                    $equipDisplay = trim((safe_text($ordem, 'equipamento_tipo', '') . ' ' . safe_text($ordem, 'equipamento_marca', '') . ' ' . safe_text($ordem, 'equipamento_modelo', '')));
                    if ($equipDisplay === '') $equipDisplay = 'N/A';
                ?>
                <p style="margin: 0;"><?php echo $equipDisplay; ?></p>
            </div>
            <div>
                <h3 style="color: var(--primary-red); margin-bottom: 0.5rem;">Tipo / Marca / Modelo</h3>
                <p style="margin: 0;"><?php echo safe_text($ordem, 'equipamento_tipo', 'N/A'); ?> / <?php echo safe_text($ordem, 'equipamento_marca', 'N/A'); ?> / <?php echo safe_text($ordem, 'equipamento_modelo', 'N/A'); ?></p>
            </div>
            <div>
                <h3 style="color: var(--primary-red); margin-bottom: 0.5rem;">Serial / Senha</h3>
                <p style="margin: 0;"><?php echo safe_text($ordem, 'equipamento_serial', 'N/A'); ?> / <?php echo safe_text($ordem, 'equipamento_senha', 'N/A'); ?></p>
            </div>
            <div>
                <h3 style="color: var(--primary-red); margin-bottom: 0.5rem;">Fonte / Acessórios</h3>
                <?php $fonte = safe_val($ordem, 'equipamento_fonte', null); ?>
                <p style="margin: 0;"><?php echo ($fonte === 1 || $fonte === '1' || $fonte === 'sim') ? 'Deixou' : 'Não Deixou'; ?> / <?php echo safe_text($ordem, 'equipamento_acessorios', 'Nenhum'); ?></p>
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
                <p style="white-space: pre-wrap;"><?php echo safe_text($ordem, 'defeito_relatado', safe_text($ordem, 'defeito', '')); ?></p>
            </div>
            <div>
                <h3 style="color: var(--primary-red); margin-bottom: 0.5rem;">Serviço Realizado / Solução (Técnico)</h3>
                <p style="white-space: pre-wrap;"><?php echo safe_text($ordem, 'servico', safe_text($ordem, 'laudo_tecnico', 'Nenhum serviço registrado.')); ?></p>
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
                                    <?php $tipoItem = safe_val($item, 'tipo', 'produto'); ?>
                                    <span style="font-weight: 600; color: <?php echo ($tipoItem === 'servico') ? 'var(--info)' : 'var(--success)'; ?>;">
                                        <?php echo htmlspecialchars(ucfirst((string)$tipoItem), ENT_QUOTES, 'UTF-8'); ?>
                                    </span>
                                </td>
                                <td><?php echo safe_text($item, 'descricao', ''); ?></td>
                                <td style="text-align: right;"><?php echo htmlspecialchars((string)(safe_val($item, 'quantidade', '0')), ENT_QUOTES, 'UTF-8'); ?></td>
                                <td style="text-align: right;"><?php echo formatCurrency((float)(safe_val($item, 'valor_unitario', 0))); ?></td>
                                <td style="text-align: right; font-weight: 600;"><?php echo formatCurrency((float)(safe_val($item, 'valor_total', 0))); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4" style="text-align: right; font-weight: 600; color: var(--primary-red);">Total Produtos:</td>
                            <td style="text-align: right; font-weight: 600; color: var(--primary-red);"><?php echo formatCurrency((float)safe_val($ordem, 'valor_total_produtos', 0)); ?></td>
                        </tr>
                        <tr>
                            <td colspan="4" style="text-align: right; font-weight: 600; color: var(--primary-red);">Total Serviços:</td>
                            <td style="text-align: right; font-weight: 600; color: var(--primary-red);"><?php echo formatCurrency((float)safe_val($ordem, 'valor_total_servicos', 0)); ?></td>
                        </tr>
                        <tr>
                            <td colspan="4" style="text-align: right; font-weight: 700; font-size: 1.1rem; color: var(--text-primary);">TOTAL GERAL:</td>
                            <td style="text-align: right; font-weight: 700; font-size: 1.1rem; color: var(--text-primary);"><?php echo formatCurrency((float)safe_val($ordem, 'valor_total_os', 0)); ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
