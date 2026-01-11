<?php
$current_page = 'atendimentos_externos';
require_once __DIR__ . '/../layout/main.php';

$atendimento = $atendimento ?? [];
$itens = $itens ?? [];
$statuses = $statuses ?? [];
$ordem = $ordem ?? ['valor_total_os' => 0]; // Garante que a variável exista para não dar erro fatal

if (!function_exists('safe_text')) {
    function safe_text(array $arr, string $key, string $default = ''): string {
        return htmlspecialchars((string)($arr[$key] ?? $default), ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('safe_val')) {
    function safe_val(array $arr, string $key, $default = null) {
        return $arr[$key] ?? $default;
    }
}

if (!function_exists('formatCurrency')) {
    function formatCurrency($value) {
        return 'R$ ' . number_format((float)$value, 2, ',', '.');
    }
}
?>

<div class="container">

    <div class="d-flex justify-between align-center mb-4 flex-wrap">
        <h1>🚗 Atendimento Externo #<?php echo safe_text($atendimento, 'id', 'N/A'); ?></h1>
        <div class="d-flex gap-2 flex-wrap">
            <a href="<?php echo BASE_URL; ?>atendimentos-externos" class="btn btn-secondary">← Voltar</a>
        </div>
    </div>

    <div class="card mb-4">
        <h2 class="card-title">📌 Dados do Cliente</h2>
        <div class="form-grid">
            <div>
                <h3>Cliente</h3>
                <p><?php echo safe_text($atendimento, 'cliente_nome', 'N/A'); ?></p>
            </div>
            <div>
                <h3>Telefone</h3>
                <p><?php echo safe_text($atendimento, 'cliente_telefone', 'N/A'); ?></p>
            </div>
            <div>
                <h3>Endereço</h3>
                <p><?php echo safe_text($atendimento, 'endereco_visita', 'N/A'); ?></p>
            </div>
            <div>
                <h3>Data da Visita</h3>
                <p>
                    <?php
                        $data = safe_val($atendimento, 'data_agendada');
                        echo $data ? date('d/m/Y H:i', strtotime($data)) : '—';
                    ?>
                </p>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <h2 class="card-title">🛠️ Relatório Técnico</h2>

        <form action="<?php echo BASE_URL; ?>atendimentos-externos/atualizar" method="POST">
            <input type="hidden" name="id" value="<?php echo $atendimento['id']; ?>">
            <input type="hidden" name="cliente_id" value="<?php echo $atendimento['cliente_id']; ?>">
            <input type="hidden" name="endereco_visita" value="<?php echo safe_text($atendimento, 'endereco_visita'); ?>">
            <input type="hidden" name="descricao_problema" value="<?php echo safe_text($atendimento, 'descricao_problema'); ?>">
            <input type="hidden" name="usuario_id" value="<?php echo safe_val($atendimento, 'usuario_id'); ?>">
	            <input type="hidden" name="data_agendada" value="<?php echo safe_val($atendimento, 'data_agendada'); ?>">
	
	            <div class="form-grid">
                <div class="form-group">
                    <h3>Problema Relatado</h3>
                    <textarea class="form-control" readonly style="min-height: 120px;"><?php echo safe_text($atendimento, 'descricao_problema'); ?></textarea>
                </div>

                <div class="form-group">
                    <h3>Solução / Laudo Técnico</h3>
                    <textarea name="observacoes_tecnicas" class="form-control" style="min-height: 120px;"><?php echo safe_text($atendimento, 'observacoes_tecnicas'); ?></textarea>
                </div>
            </div>

            <div class="form-grid mt-4">
                <div class="form-group">
                    <h3>Status</h3>
                    <select name="status" class="form-control">
                        <?php 
                        $status_opcoes = ['pendente', 'agendado', 'em_deslocamento', 'concluido', 'cancelado'];
                        $status_atual = safe_val($atendimento, 'status', 'pendente');
                        foreach ($status_opcoes as $opt): ?>
                            <option value="<?php echo $opt; ?>" <?php echo ($opt === $status_atual) ? 'selected' : ''; ?>>
                                <?php echo ucfirst(str_replace('_', ' ', $opt)); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <h3>Tempo Gasto</h3>
                    <input type="text" name="tempo_total" class="form-control" placeholder="Ex: 01:30" value="<?php echo safe_text($atendimento, 'tempo_total'); ?>">
                </div>

	                <div class="form-group">
	                    <h3>Valor Deslocamento</h3>
	                    <input type="number" name="valor_deslocamento" class="form-control" step="0.01" value="<?php echo (float)safe_val($atendimento, 'valor_deslocamento', 0); ?>">
	                </div>
	
	                <div class="form-group">
	                    <h3>Pagamento</h3>
	                    <select name="pagamento" class="form-control">
	                        <?php 
	                        $pagamento_opcoes = ['não', 'parcial', 'pago'];
	                        $pagamento_atual = safe_val($atendimento, 'pagamento', 'não');
	                        foreach ($pagamento_opcoes as $opt): ?>
	                            <option value="<?php echo $opt; ?>" <?php echo ($opt === $pagamento_atual) ? 'selected' : ''; ?>>
	                                <?php echo ucfirst($opt); ?>
	                            </option>
	                        <?php endforeach; ?>
	                    </select>
	                </div>
	            </div>

            <div class="mt-4 text-end">
                <button type="submit" class="btn btn-primary">💾 Salvar Atendimento</button>
            </div>
        </form>
    </div>

    <div class="card mb-4">
        <h2 class="card-title">➕ Adicionar Produto ou Serviço</h2>
        <form action="<?php echo BASE_URL; ?>atendimentos/saveItem" method="POST" id="form-add-item">
            <input type="hidden" name="atendimento_externo_id" value="<?php echo $atendimento['id']; ?>">
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
                    <input type="text" name="descricao" id="item_descricao" class="form-control" required readonly>
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
            
            <div class="mt-3 p-3 bg-tertiary rounded d-flex align-center gap-3">
                <div class="d-flex align-center gap-2">
                    <input type="checkbox" name="comprar_peca" id="comprar_peca" value="1" style="width: 20px; height: 20px; cursor: pointer;">
                    <label for="comprar_peca" class="m-0 cursor-pointer fw-bold text-info">🛒 Comprar Peça?</label>
                </div>
                <div id="div_link_fornecedor" class="flex-1" style="display: none;">
                    <input type="url" name="link_fornecedor" id="link_fornecedor" placeholder="Cole aqui o link do fornecedor..." class="form-control">
                </div>
            </div>
        </form>
    </div>

    <div class="card mb-4">
        <h2 class="card-title">🛒 Produtos e Serviços Adicionados</h2>

        <?php if (empty($itens)): ?>
            <div class="alert alert-info m-0">Nenhum produto ou serviço adicionado.</div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th style="width: 8%;">Tipo</th>
                            <th style="width: 25%;">Descrição</th>
                            <th style="width: 8%; text-align: right;">Qtd</th>
                            <th style="width: 10%; text-align: right;">Custo</th>
                            <th style="width: 10%; text-align: right;">Vlr Unit.</th>
                            <th style="width: 10%; text-align: right;">M. Obra</th>
                            <th style="width: 10%; text-align: right;">Vlr Total</th>
                            <th style="width: 10%; text-align: center;">Compra</th>
                            <th style="width: 9%; text-align: center;">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($itens as $item): ?>
                            <tr>
                                <form action="<?php echo BASE_URL; ?>atendimentos/atualizar-item" method="POST">
                                    <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>">
                                    <input type="hidden" name="atendimento_externo_id" value="<?php echo $atendimento['id']; ?>">
                                    <td>
                                        <span class="fw-bold" style="color: <?php echo (safe_val($item, 'tipo_item') === 'servico') ? 'var(--info)' : 'var(--success)'; ?>;">
                                            <?php echo (safe_val($item, 'tipo_item') === 'servico') ? '🛠️ Serv' : '📦 Prod'; ?>
                                        </span>
                                    </td>
                                    <td><?php echo safe_text($item, 'descricao', 'N/A'); ?></td>
                                    <td class="text-end">
                                        <input type="number" name="quantidade" value="<?php echo (float)safe_val($item, 'quantidade', 0); ?>" step="0.01" class="form-control form-control-sm text-end" style="width: 70px;">
                                    </td>
                                    <td class="text-end">
                                        <input type="number" name="custo" value="<?php echo (float)safe_val($item, 'custo', 0); ?>" step="0.01" class="form-control form-control-sm text-end" style="width: 90px;">
                                    </td>
                                    <td class="text-end">
                                        <input type="number" name="valor_unitario" value="<?php echo (float)safe_val($item, 'valor_unitario', 0); ?>" step="0.01" class="form-control form-control-sm text-end" style="width: 90px;">
                                    </td>
                                    <td class="text-end">
                                        <input type="number" name="valor_mao_de_obra" value="<?php echo (float)safe_val($item, 'valor_mao_de_obra', 0); ?>" step="0.01" class="form-control form-control-sm text-end" style="width: 90px;">
                                    </td>
                                    <td class="text-end fw-bold"><?php echo formatCurrency((float)(safe_val($item, 'valor_total', 0))); ?></td>
                                    <td class="text-center">
                                        <?php if (safe_val($item, 'comprar_peca', 0) == 1): ?>
                                            <span title="Peça para comprar" class="cursor-help">🛒 Sim</span>
                                        <?php else: ?>
                                            <span class="text-muted">—</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex gap-1 justify-center">
                                            <button type="submit" class="btn btn-sm btn-success">💾</button>
                                </form>
                                            <form action="<?php echo BASE_URL; ?>atendimentos/remover-item" method="POST" onsubmit="return confirm('Remover item?');">
                                                <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>">
                                                <input type="hidden" name="atendimento_externo_id" value="<?php echo $atendimento['id']; ?>">
                                                <button type="submit" class="btn btn-sm btn-danger">🗑️</button>
                                            </form>
                                        </div>
                                    </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr class="bg-tertiary">
                            <td colspan="6" class="text-end fw-bold">TOTAL DA OS:</td>
                            <td class="text-end fw-bold text-primary" style="font-size: 1.2rem;">
                                <?php echo formatCurrency((float)(safe_val($ordem, 'valor_total_os', 0))); ?>
                            </td>
                            <td colspan="2"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('item_search');
    const resultsDiv = document.getElementById('item_results');
    const comprarPecaCheckbox = document.getElementById('comprar_peca');
    const divLinkFornecedor = document.getElementById('div_link_fornecedor');

    if(comprarPecaCheckbox) {
        comprarPecaCheckbox.addEventListener('change', function() {
            divLinkFornecedor.style.display = this.checked ? 'block' : 'none';
        });
    }

    let timeout = null;
    searchInput.addEventListener('input', function() {
        clearTimeout(timeout);
        const termo = this.value.trim();
        if (termo.length < 2) {
            resultsDiv.style.display = 'none';
            return;
        }

        timeout = setTimeout(() => {
            fetch(`<?php echo BASE_URL; ?>atendimentos/search-items?termo=${encodeURIComponent(termo)}`)
                .then(response => response.json())
                .then(data => {
                    resultsDiv.innerHTML = '';
                    if (Array.isArray(data) && data.length > 0) {
                        data.forEach(item => {
                            const div = document.createElement('div');
                            div.className = 'autocomplete-item';
                            div.innerHTML = `<span>${item.nome}</span> <span class="badge ${item.tipo === 'servico' ? 'bg-info' : 'bg-success'}">${item.tipo}</span>`;
                            div.addEventListener('click', () => {
                                document.getElementById('item_produto_id').value = item.id;
                                document.getElementById('item_tipo').value = item.tipo;
                                document.getElementById('item_descricao').value = item.nome;
                                document.getElementById('item_custo').value = item.custo;
                                document.getElementById('item_venda').value = item.valor_venda;
                                document.getElementById('item_mao_de_obra').value = item.mao_de_obra;
                                searchInput.value = item.nome;
                                resultsDiv.style.display = 'none';
                            });
                            resultsDiv.appendChild(div);
                        });
                        resultsDiv.style.display = 'block';
                    } else {
                        resultsDiv.style.display = 'none';
                    }
                });
        }, 300);
    });
});
</script>

<style>
.autocomplete-results { position: absolute; background: var(--bg-secondary); border: 1px solid var(--border-color); width: 100%; max-height: 200px; overflow-y: auto; z-index: 1000; display: none; }
.autocomplete-item { padding: 8px 12px; cursor: pointer; border-bottom: 1px solid var(--border-color); }
.autocomplete-item:hover { background: var(--bg-tertiary); }
.cursor-pointer { cursor: pointer; }
.cursor-help { cursor: help; }
</style>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>