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
    <div class="d-flex justify-between align-center mb-4 flex-wrap">
        <h1>📋 Ordem de Serviço #<?php echo safe_text($ordem, 'id', 'N/A'); ?></h1>
        <div class="d-flex gap-2 flex-wrap">
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
    <div class="card mb-4">
        <h2 class="card-title">Detalhes Gerais</h2>
        <div class="form-grid">
            <!-- Cliente -->
            <div>
                <h3 class="mb-2">👥 Cliente</h3>
                <p class="m-0"><?php echo safe_text($ordem, 'cliente_nome', 'N/A'); ?></p>
            </div>

            <!-- Status -->
            <div>
                <h3 class="mb-2">✅ Status</h3>
                <?php $status_cor = safe_text($ordem, 'status_cor', '#777'); ?>
                <span class="badge" style="background-color: <?php echo $status_cor; ?>; color: #fff;">
                    <?php echo safe_text($ordem, 'status_nome', '—'); ?>
                </span>
            </div>

            <!-- Data de Abertura -->
            <div>
                <h3 class="mb-2">📅 Abertura</h3>
                <?php
                    $dataAbert = safe_val($ordem, 'data_abertura', null) ?: safe_val($ordem, 'created_at', null);
                    if (!empty($dataAbert) && strtotime((string)$dataAbert) !== false) {
                        echo '<p class="m-0">' . date('d/m/Y H:i', strtotime((string)$dataAbert)) . '</p>';
                    } else {
                        echo '<p class="m-0">—</p>';
                    }
                ?>
            </div>
        </div>
    </div>

    <!-- CARD DE EQUIPAMENTO -->
    <div class="card mb-4">
        <h2 class="card-title">💻 Detalhes do Equipamento</h2>
        <div class="form-grid">
            <div>
                <h3 class="mb-2">Equipamento</h3>
                <?php
                    $equipDisplay = trim((safe_text($ordem, 'equipamento_tipo', '') . ' ' . safe_text($ordem, 'equipamento_marca', '') . ' ' . safe_text($ordem, 'equipamento_modelo', '')));
                    if ($equipDisplay === '') $equipDisplay = 'N/A';
                ?>
                <p class="m-0"><?php echo $equipDisplay; ?></p>
            </div>
            <div>
                <h3 class="mb-2">Tipo / Marca / Modelo</h3>
                <p class="m-0"><?php echo safe_text($ordem, 'equipamento_tipo', 'N/A'); ?> / <?php echo safe_text($ordem, 'equipamento_marca', 'N/A'); ?> / <?php echo safe_text($ordem, 'equipamento_modelo', 'N/A'); ?></p>
            </div>
            <div>
                <h3 class="mb-2">Serial / Senha</h3>
                <p class="m-0"><?php echo safe_text($ordem, 'equipamento_serial', 'N/A'); ?> / <?php echo safe_text($ordem, 'equipamento_senha', 'N/A'); ?></p>
            </div>
            <div>
                <h3 class="mb-2">Fonte / Acessórios</h3>
                <?php $fonte = safe_val($ordem, 'equipamento_fonte', null); ?>
                <p class="m-0"><?php echo ($fonte === 1 || $fonte === '1' || $fonte === 'sim') ? 'Deixou' : 'Não Deixou'; ?> / <?php echo safe_text($ordem, 'equipamento_acessorios', 'Nenhum'); ?></p>
            </div>
        </div>
    </div>

    <!-- CARD DE PROBLEMA E LAUDO -->
    <div class="card mb-4">
        <h2 class="card-title">Laudo e Serviço</h2>
        <form action="<?php echo BASE_URL; ?>ordens/atualizar" method="POST">
            <input type="hidden" name="id" value="<?php echo $ordem['id']; ?>">
            <input type="hidden" name="status_id" value="<?php echo $ordem['status_atual_id']; ?>">
            
            <div class="form-grid">
                <div class="form-group">
                    <h3 class="mb-2">Defeito Relatado (Recepção)</h3>
                    <textarea name="defeito" class="form-control" style="min-height: 120px;" readonly><?php echo safe_text($ordem, 'defeito_relatado', safe_text($ordem, 'defeito', '')); ?></textarea>
                    <small class="text-muted">* Edição bloqueada para padronização.</small>
                </div>
                <div class="form-group">
                    <h3 class="mb-2">Laudo Técnico / Solução</h3>
                    <textarea name="laudo_tecnico" class="form-control" style="min-height: 120px;" placeholder="Digite aqui o laudo técnico..."><?php echo safe_text($ordem, 'laudo_tecnico', ''); ?></textarea>
                    <div class="mt-2 text-end">
                        <button type="submit" class="btn btn-primary">💾 Salvar Laudo</button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- CARD DE ADICIONAR ITENS -->
    <div class="card mb-4">
        <h2 class="card-title">➕ Adicionar Produto ou Serviço</h2>
        <form action="<?php echo BASE_URL; ?>ordens/salvar-item" method="POST" id="form-add-item">
            <input type="hidden" name="ordem_servico_id" value="<?php echo $ordem['id']; ?>">
            <input type="hidden" name="produto_id" id="item_produto_id">
            <input type="hidden" name="tipo" id="item_tipo">
            
            <div class="form-grid align-end">
                <div class="form-group">
                    <label>Buscar Item</label>
                    <input type="text" id="item_search" class="form-control" placeholder="Digite o nome do item..." autocomplete="off">
                    <div id="item_results" class="autocomplete-results"></div>
                </div>
                <div class="form-group">
                    <label>Descrição</label>
                    <input type="text" name="descricao" id="item_descricao" class="form-control" required readonly title="Preenchido automaticamente a partir dos itens cadastrados">
                </div>
                <div class="form-group">
                    <label>Qtd</label>
                    <input type="number" name="quantidade" id="item_quantidade" class="form-control" value="1" step="0.01" required>
                </div>
                <div class="form-group">
                    <label>Custo</label>
                    <input type="number" name="valor_custo" id="item_custo" class="form-control" step="0.01" value="0.00">
                </div>
                <div class="form-group">
                    <label>Venda</label>
                    <input type="number" name="valor_unitario" id="item_venda" class="form-control" step="0.01" required>
                </div>
                <div class="form-group">
                    <label>Mão de Obra</label>
                    <input type="number" name="valor_mao_de_obra" id="item_mao_de_obra" class="form-control" step="0.01" value="0.00">
                </div>
                <button type="submit" class="btn btn-primary mb-1">Adicionar</button>
            </div>
            
            <!-- Novos campos para compra de peça -->
            <div class="mt-3 p-3 bg-tertiary rounded d-flex align-center gap-3">
                <div class="d-flex align-center gap-2">
                    <input type="checkbox" name="comprar_peca" id="comprar_peca" value="1" style="width: 20px; height: 20px; cursor: pointer;">
                    <label for="comprar_peca" class="m-0 cursor-pointer fw-bold text-info">🛒 Comprar Peça?</label>
                </div>
                <div id="div_link_fornecedor" class="flex-1" style="display: none;">
                    <input type="url" name="link_fornecedor" id="link_fornecedor" placeholder="Cole aqui o link do fornecedor da peça..." class="form-control">
                </div>
            </div>
        </form>
    </div>

    <!-- CARD DE ITENS -->
    <div class="card mb-4">
        <h2 class="card-title">🛒 Produtos e Serviços Adicionados</h2>

        <?php if (empty($itens)): ?>
            <div class="alert alert-info m-0">Nenhum produto ou serviço adicionado a esta Ordem de Serviço.</div>
        <?php else: ?>
            <div class="overflow-x-auto">
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
                            <?php $tipoItem = safe_val($item, 'tipo', 'produto'); ?>
                            <tr>
                                <td>
                                    <span class="fw-bold" style="color: <?php echo ($tipoItem === 'servico') ? 'var(--info)' : 'var(--success)'; ?>;">
                                        <?php echo ($tipoItem === 'servico') ? '🛠️ Serv' : '📦 Prod'; ?>
                                    </span>
                                </td>
                                <td><?php echo safe_text($item, 'descricao', 'N/A'); ?></td>
                                <td class="text-end"><?php echo htmlspecialchars((string)(safe_val($item, 'quantidade', '0')), ENT_QUOTES, 'UTF-8'); ?></td>
                                <td class="text-end"><?php echo formatCurrency((float)(safe_val($item, 'valor_unitario', 0))); ?></td>
                                <td class="text-end"><?php echo formatCurrency((float)(safe_val($item, 'valor_mao_de_obra', 0))); ?></td>
                                <td class="text-end fw-bold"><?php echo formatCurrency((float)(safe_val($item, 'valor_total', 0))); ?></td>
                                <td class="text-center">
                                    <?php if (safe_val($item, 'comprar_peca', 0) == 1): ?>
                                        <span title="Peça para comprar" class="cursor-help">🛒 Sim</span>
                                        <?php if (!empty($item['link_fornecedor'])): ?>
                                            <br><a href="<?php echo $item['link_fornecedor']; ?>" target="_blank" class="fs-sm text-info">🔗 Link</a>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="text-muted">—</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <form action="<?php echo BASE_URL; ?>ordens/deletar-item" method="POST" class="d-inline" onsubmit="return confirm('Remover este item?');">
                                        <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
                                        <input type="hidden" name="ordem_servico_id" value="<?php echo $ordem['id']; ?>">
                                        <button type="submit" class="btn btn-danger btn-sm">🗑️</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="5" class="text-end fw-bold text-red">Total Produtos:</td>
                            <td class="text-end fw-bold text-red"><?php echo formatCurrency((float)safe_val($ordem, 'valor_total_produtos', 0)); ?></td>
                            <td colspan="2"></td>
                        </tr>
                        <tr>
                            <td colspan="5" class="text-end fw-bold text-red">Total Serviços:</td>
                            <td class="text-end fw-bold text-red"><?php echo formatCurrency((float)safe_val($ordem, 'valor_total_servicos', 0)); ?></td>
                            <td colspan="2"></td>
                        </tr>
                        <tr>
                            <td colspan="5" class="text-end fw-bold fs-5">TOTAL GERAL:</td>
                            <td class="text-end fw-bold fs-5"><?php echo formatCurrency((float)safe_val($ordem, 'valor_total_os', 0)); ?></td>
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
    const itemTipo = document.getElementById('item_tipo');
    const itemDescricao = document.getElementById('item_descricao');
    const itemVenda = document.getElementById('item_venda');
    const itemCusto = document.getElementById('item_custo');
    const itemMaoDeObra = document.getElementById('item_mao_de_obra');
    const checkComprar = document.getElementById('comprar_peca');
    const divLink = document.getElementById('div_link_fornecedor');
    const margemLucro = parseFloat('<?php echo $margem_lucro; ?>') || 0;

    function calcularVenda() {
        const custo = parseFloat(itemCusto.value) || 0;
        if (custo > 0 && margemLucro > 0) {
            const venda = custo * (1 + (margemLucro / 100));
            itemVenda.value = venda.toFixed(2);
        }
    }

    itemCusto.addEventListener('input', calcularVenda);

    checkComprar.addEventListener('change', () => {
        divLink.style.display = checkComprar.checked ? 'block' : 'none';
    });

    itemSearch.addEventListener('input', () => {
        const termo = itemSearch.value.trim();
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
                        const preco = (item.valor_venda !== undefined) ? item.valor_venda : (item.preco_venda !== undefined ? item.preco_venda : '0.00');
                        div.innerHTML = `<span>${item.nome} (${item.tipo})</span> <strong>R$ ${preco}</strong>`;
                        div.onclick = () => {
                            itemProdutoId.value = item.id;
                            itemTipo.value = item.tipo;
                            itemDescricao.value = item.nome;
                            itemVenda.value = (item.valor_venda !== undefined) ? item.valor_venda : (item.preco_venda !== undefined ? item.preco_venda : 0);
                            itemCusto.value = (item.custo !== undefined) ? item.custo : (item.preco_custo !== undefined ? item.preco_custo : 0);
                            itemMaoDeObra.value = 0;
                            itemResults.style.display = 'none';
                            itemSearch.value = item.nome;
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
        if (e.target !== itemSearch) itemResults.style.display = 'none';
    });
});
</script>

<style>
.align-end { align-items: flex-end !important; }
.text-end { text-align: right !important; }
.text-center { text-align: center !important; }
.cursor-help { cursor: help !important; }
.cursor-pointer { cursor: pointer !important; }
.fs-5 { font-size: 1.25rem !important; }
.bg-tertiary { background-color: var(--bg-tertiary) !important; }
</style>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
