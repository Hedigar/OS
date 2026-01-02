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
            <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
            <a href="<?php echo BASE_URL; ?>ordens/form?id=<?php echo safe_text($ordem, 'id', ''); ?>" class="btn btn-info">
                ✏️ Editar OS
            </a>
                <a href="<?php echo BASE_URL; ?>ordens/print?id=<?php echo safe_text($ordem, 'id', ''); ?>" target="_blank" class="btn btn-secondary">
                🖨️ Imprimir OS
            </a>
            <a href="<?php echo BASE_URL; ?>ordens/print-receipt?id=<?php echo safe_text($ordem, 'id', ''); ?>" target="_blank" class="btn btn-primary">
                🧾 Imprimir Recepção
            </a>
            <a href="<?php echo BASE_URL; ?>ordens/print-estimate?id=<?php echo safe_text($ordem, 'id', ''); ?>" target="_blank" class="btn btn-success">
                💲 Imprimir Orçamento
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

    <!-- CARD DE PROBLEMA E LAUDO -->
    <div class="card" style="margin-bottom: 2rem;">
        <h2 style="color: var(--text-primary); margin-bottom: 1.5rem; padding-bottom: 0.5rem; border-bottom: 2px solid var(--dark-tertiary);">
            Laudo e Serviço
        </h2>
        <form action="<?php echo BASE_URL; ?>ordens/atualizar" method="POST">
            <input type="hidden" name="id" value="<?php echo $ordem['id']; ?>">
            <input type="hidden" name="status_id" value="<?php echo $ordem['status_atual_id']; ?>">
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
                <div>
                    <h3 style="color: var(--primary-red); margin-bottom: 0.5rem;">Defeito Relatado (Recepção)</h3>
                    <textarea name="defeito" class="form-control" style="min-height: 120px; background-color: var(--dark-tertiary); cursor: not-allowed;" readonly><?php echo safe_text($ordem, 'defeito_relatado', safe_text($ordem, 'defeito', '')); ?></textarea>
                    <small style="color: #888;">* Edição bloqueada para padronização.</small>
                </div>
                <div>
                    <h3 style="color: var(--primary-red); margin-bottom: 0.5rem;">Laudo Técnico / Solução</h3>
                    <textarea name="laudo_tecnico" class="form-control" style="min-height: 120px;" placeholder="Digite aqui o laudo técnico..."><?php echo safe_text($ordem, 'laudo_tecnico', ''); ?></textarea>
                    <div style="margin-top: 10px; text-align: right;">
                        <button type="submit" class="btn btn-primary">💾 Salvar Laudo</button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- CARD DE ADICIONAR ITENS -->
    <div class="card" style="margin-bottom: 2rem;">
        <h2 style="color: var(--text-primary); margin-bottom: 1.5rem; padding-bottom: 0.5rem; border-bottom: 2px solid var(--dark-tertiary);">
            ➕ Adicionar Produto ou Serviço
        </h2>
        <form action="<?php echo BASE_URL; ?>ordens/salvar-item" method="POST" id="form-add-item">
            <input type="hidden" name="ordem_servico_id" value="<?php echo $ordem['id']; ?>">
            <input type="hidden" name="produto_id" id="item_produto_id">
            <input type="hidden" name="tipo" id="item_tipo">
            
            <div style="display: grid; grid-template-columns: 2fr 1.5fr 0.8fr 1fr 1fr 1fr auto; gap: 1rem; align-items: end;">
                <div class="form-group">
                    <label>Buscar Item</label>
                    <input type="text" id="item_search" placeholder="Digite o nome do item..." autocomplete="off">
                    <div id="item_results" class="autocomplete-results"></div>
                </div>
                <div class="form-group">
                    <label>Descrição</label>
                    <input type="text" name="descricao" id="item_descricao" required>
                </div>
                <div class="form-group">
                    <label>Qtd</label>
                    <input type="number" name="quantidade" id="item_quantidade" value="1" step="0.01" required>
                </div>
                <div class="form-group">
                    <label>Custo</label>
                    <input type="number" name="valor_custo" id="item_custo" step="0.01" value="0.00">
                </div>
                <div class="form-group">
                    <label>Venda</label>
                    <input type="number" name="valor_unitario" id="item_venda" step="0.01" required>
                </div>
                <div class="form-group">
                    <label>Mão de Obra</label>
                    <input type="number" name="valor_mao_de_obra" id="item_mao_de_obra" step="0.01" value="0.00">
                </div>
                <button type="submit" class="btn btn-primary" style="margin-bottom: 5px;">Adicionar</button>
            </div>
            
            <!-- Novos campos para compra de peça -->
            <div style="margin-top: 1rem; display: flex; align-items: center; gap: 2rem; padding: 10px; background: var(--dark-tertiary); border-radius: 8px;">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <input type="checkbox" name="comprar_peca" id="comprar_peca" value="1" style="width: 20px; height: 20px; cursor: pointer;">
                    <label for="comprar_peca" style="margin: 0; cursor: pointer; font-weight: 600; color: var(--info);">🛒 Comprar Peça?</label>
                </div>
                <div id="div_link_fornecedor" style="display: none; flex: 1;">
                    <input type="url" name="link_fornecedor" id="link_fornecedor" placeholder="Cole aqui o link do fornecedor da peça..." class="form-control">
                </div>
            </div>
        </form>
    </div>

    <!-- CARD DE ITENS -->
    <div class="card" style="margin-bottom: 2rem;">
        <h2 style="color: var(--text-primary); margin-bottom: 1.5rem; padding-bottom: 0.5rem; border-bottom: 2px solid var(--dark-tertiary);">
            🛒 Produtos e Serviços Adicionados
        </h2>

        <?php if (empty($itens)): ?>
            <div class="alert alert-info">Nenhum produto ou serviço adicionado a esta Ordem de Serviço.</div>
        <?php else: ?>
            <div style="overflow-x: auto;">
                <table>
                    <thead>
                        <tr>
                            <th style="width: 8%;">Tipo</th>
                            <th style="width: 32%;">Descrição</th>
                            <th style="width: 10%; text-align: right;">Qtd</th>
                            <th style="width: 10%; text-align: right;">Vlr Unit.</th>
                            <th style="width: 10%; text-align: right;">M. Obra</th>
                            <th style="width: 10%; text-align: right;">Vlr Total</th>
                            <th style="width: 12%; text-align: center;">Compra</th>
                            <th style="width: 8%; text-align: center;">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($itens as $item): ?>
                            <tr>
                                <td>
                                    <?php $tipoItem = $item['tipo_item'] ?? $item['tipo'] ?? 'produto'; ?>
                                    <span style="font-weight: 600; color: <?php echo ($tipoItem === 'servico') ? 'var(--info)' : 'var(--success)'; ?>;">
                                        <?php echo htmlspecialchars(ucfirst((string)$tipoItem), ENT_QUOTES, 'UTF-8'); ?>
                                    </span>
                                </td>
                                <td><?php echo safe_text($item, 'descricao', ''); ?></td>
                                <td style="text-align: right;"><?php echo htmlspecialchars((string)(safe_val($item, 'quantidade', '0')), ENT_QUOTES, 'UTF-8'); ?></td>
                                <td style="text-align: right;"><?php echo formatCurrency((float)(safe_val($item, 'valor_unitario', 0))); ?></td>
                                <td style="text-align: right;"><?php echo formatCurrency((float)(safe_val($item, 'valor_mao_de_obra', 0))); ?></td>
                                <td style="text-align: right; font-weight: 600;"><?php echo formatCurrency((float)(safe_val($item, 'valor_total', 0))); ?></td>
                                <td style="text-align: center;">
                                    <?php if (isset($item['comprar_peca']) && $item['comprar_peca']): ?>
                                        <span title="Peça para comprar" style="cursor: help;">🛒 Sim</span>
                                        <?php if (!empty($item['link_fornecedor'])): ?>
                                            <br><a href="<?php echo $item['link_fornecedor']; ?>" target="_blank" style="font-size: 0.8rem; color: var(--info);">🔗 Link</a>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span style="color: #666;">—</span>
                                    <?php endif; ?>
                                </td>
                                <td style="text-align: center;">
                                    <form action="<?php echo BASE_URL; ?>ordens/remover-item" method="POST" onsubmit="return confirm('Remover este item?')">
                                        <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>">
                                        <input type="hidden" name="ordem_servico_id" value="<?php echo $ordem['id']; ?>">
                                        <button type="submit" class="btn btn-danger btn-sm">🗑️</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="5" style="text-align: right; font-weight: 600; color: var(--primary-red);">Total Produtos:</td>
                            <td style="text-align: right; font-weight: 600; color: var(--primary-red);"><?php echo formatCurrency((float)safe_val($ordem, 'valor_total_produtos', 0)); ?></td>
                            <td colspan="2"></td>
                        </tr>
                        <tr>
                            <td colspan="5" style="text-align: right; font-weight: 600; color: var(--primary-red);">Total Serviços:</td>
                            <td style="text-align: right; font-weight: 600; color: var(--primary-red);"><?php echo formatCurrency((float)safe_val($ordem, 'valor_total_servicos', 0)); ?></td>
                            <td colspan="2"></td>
                        </tr>
                        <tr>
                            <td colspan="5" style="text-align: right; font-weight: 700; font-size: 1.1rem; color: var(--text-primary);">TOTAL GERAL:</td>
                            <td style="text-align: right; font-weight: 700; font-size: 1.1rem; color: var(--text-primary);"><?php echo formatCurrency((float)safe_val($ordem, 'valor_total_os', 0)); ?></td>
                            <td colspan="2"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const itemSearch = document.getElementById('item_search');
    const itemResults = document.getElementById('item_results');
    const itemProdutoId = document.getElementById('item_produto_id');
    const itemDescricao = document.getElementById('item_descricao');
    const itemVenda = document.getElementById('item_venda');
    const itemMaoDeObra = document.getElementById('item_mao_de_obra');
    const itemCusto = document.getElementById('item_custo');
    const itemTipo = document.getElementById('item_tipo');
    
    const comprarPecaCheck = document.getElementById('comprar_peca');
    const divLinkFornecedor = document.getElementById('div_link_fornecedor');

    // Toggle link fornecedor
    comprarPecaCheck.addEventListener('change', function() {
        divLinkFornecedor.style.display = this.checked ? 'block' : 'none';
        if (this.checked) {
            document.getElementById('link_fornecedor').focus();
        }
    });
    
    // Buscar porcentagem de configuração
    let porcentagemVenda = 0;
    fetch('<?php echo BASE_URL; ?>configuracoes/get-porcentagem-ajax')
        .then(r => r.json())
        .then(data => {
            porcentagemVenda = parseFloat(data.valor) || 0;
        });

    function calcularVenda() {
        if (itemTipo.value === 'produto' && porcentagemVenda > 0) {
            const custo = parseFloat(itemCusto.value) || 0;
            const venda = custo * (1 + (porcentagemVenda / 100));
            itemVenda.value = venda.toFixed(2);
        }
    }

    itemCusto.addEventListener('input', calcularVenda);

    itemSearch.addEventListener('input', () => {
        const termo = itemSearch.value.trim();
        if (termo.length === 0) {
            itemProdutoId.value = '';
            itemDescricao.value = '';
            itemDescricao.readOnly = false;
            itemDescricao.style.backgroundColor = '';
            itemDescricao.style.cursor = '';
        }
        if (termo.length < 2) {
            itemResults.style.display = 'none';
            return;
        }

        fetch(`<?php echo BASE_URL; ?>ordens/search-items?termo=${encodeURIComponent(termo)}`)
            .then(r => r.json())
            .then(data => {
                itemResults.innerHTML = '';
                if (data.length > 0) {
                    data.forEach(item => {
                        const div = document.createElement('div');
                        div.className = 'autocomplete-item';
                        div.innerHTML = `<strong>${item.nome}</strong> <small>(${item.tipo})</small><br>
                                         <small>Venda: R$ ${item.valor_venda} | M.Obra: R$ ${item.mao_de_obra}</small>`;
                        div.onclick = () => {
                            itemProdutoId.value = item.id;
                            itemDescricao.value = item.nome;
                            itemDescricao.readOnly = true;
                            itemDescricao.style.backgroundColor = 'var(--dark-tertiary)';
                            itemDescricao.style.cursor = 'not-allowed';
                            itemVenda.value = item.valor_venda;
                            itemMaoDeObra.value = item.mao_de_obra;
                            itemCusto.value = item.custo;
                            itemTipo.value = item.tipo;
                            itemSearch.value = item.nome;
                            itemResults.style.display = 'none';
                        };
                        itemResults.appendChild(div);
                    });
                    itemResults.style.display = 'block';
                } else {
                    itemResults.style.display = 'none';
                }
            });
    });

    document.addEventListener('click', (e) => {
        if (e.target !== itemSearch) {
            itemResults.style.display = 'none';
        }
    });
});
</script>

<style>
.autocomplete-results {
    position: absolute;
    background: var(--dark-secondary);
    border: 1px solid var(--dark-tertiary);
    border-radius: 8px;
    width: 100%;
    max-height: 200px;
    overflow-y: auto;
    z-index: 1000;
    display: none;
}
.autocomplete-item {
    padding: 10px;
    cursor: pointer;
    border-bottom: 1px solid var(--dark-tertiary);
}
.autocomplete-item:hover {
    background: var(--dark-tertiary);
}
.form-group {
    position: relative;
}
.form-control {
    width: 100%;
    padding: 0.8rem;
    border-radius: 8px;
    border: 1px solid var(--dark-tertiary);
    background-color: var(--dark-secondary);
    color: var(--text-primary);
}
</style>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
