<?php
$current_page = 'ordens';
require_once __DIR__ . '/../layout/main.php';

$ordem = $ordem ?? [];
$itens = $itens ?? [];
$historico = $historico ?? [];
$statuses = $statuses ?? [];
$transacoes = $transacoes ?? [];
$maquinas = $maquinas ?? [];
$formas = $formas ?? [];
$bandeiras = $bandeiras ?? [];
$margemLucro = $margemLucro ?? 0;
// Cálculos financeiros (fornecidos pelo controller; defaults para compatibilidade)
$calcSomaLiquida = $calcSomaLiquida ?? 0;
$calcSomaCusto = $calcSomaCusto ?? 0;
$totalLiquidoRecebido = $totalLiquidoRecebido ?? 0;
$custoNF = $custoNF ?? 0;
$totalPago = $totalPago ?? 0;
$saldo = $saldo ?? 0;
$lucroLiquidoReal = $lucroLiquidoReal ?? 0;

// Função auxiliar para formatar moeda
if (!function_exists('formatCurrency')) {
    function formatCurrency($value) {
        return 'R$ ' . number_format($value, 2, ',', '.');
    }
}

// Helpers seguros para evitar warnings/deprecations ao acessar chaves
if (!function_exists('safe_text')) {
    function safe_text(array $arr, string $key, string $default = ''): string
    {
        $val = $arr[$key] ?? $default;
        return htmlspecialchars((string)$val, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('safe_val')) {
    function safe_val(array $arr, string $key, $default = null)
    {
        return $arr[$key] ?? $default;
    }
}
?>

<div class="container">
    <div class="d-flex justify-between align-center mb-4 flex-wrap">
        <h1>📋 Ordem de Serviço #<?php echo safe_text($ordem, 'id', 'N/A'); ?></h1>
        <div class="d-flex gap-2 flex-wrap">
            <a href="<?php echo BASE_URL; ?>ordens/form?id=<?php echo safe_text($ordem, 'id', ''); ?>" class="btn btn-info">✏️ Editar OS</a>
            <button type="button" onclick="abrirModalRetorno()" class="btn btn-danger" style="background-color: #d32f2f; border-color: #c62828; color: white;">🔄 Retorno</button>
            <a href="<?php echo BASE_URL; ?>ordens/print-receipt?id=<?php echo safe_text($ordem, 'id', ''); ?>" target="_blank" class="btn btn-primary">🖨️ Imprimir OS</a>
            <a href="<?php echo BASE_URL; ?>ordens/print-payment-receipt?id=<?php echo safe_text($ordem, 'id', ''); ?>" target="_blank" class="btn btn-warning" style="background-color: #ff9800; border-color: #f57c00; color: white;">🧾 Recibo (80mm)</a>
            <a href="<?php echo BASE_URL; ?>ordens/print-estimate?id=<?php echo safe_text($ordem, 'id', ''); ?>" target="_blank" class="btn btn-success">💲 Imprimir Orçamento</a>
            <a href="<?php echo BASE_URL; ?>ordens" class="btn btn-secondary">← Voltar</a>
        </div>
    </div>

    <!-- CARD DE DETALHES GERAIS -->
    <div class="card mb-4">
        <h2 class="card-title">Detalhes Gerais</h2>
        <div class="form-grid">
            <div>
                <h3 class="mb-2">👥 Cliente</h3>
                <p class="m-0"><a href="<?php echo BASE_URL; ?>clientes/view?id=<?php echo safe_text($ordem, 'cliente_id', ''); ?>" style="text-decoration: none; color: var(--info); font-weight: bold;"><?php echo safe_text($ordem, 'cliente_nome', 'N/A'); ?></a></p>
            </div>
            <div>
                <h3 class="mb-2">📞 Celular</h3>
                <p class="m-0">
                    <?php
                        $telRaw = $ordem['cliente_telefone'] ?? '';
                        $tel = preg_replace('/\D+/', '', (string)$telRaw);
                        if ($tel) {
                            $nomeCli = trim((string)($ordem['cliente_nome'] ?? ''));
                            $primeiroNome = $nomeCli !== '' ? explode(' ', $nomeCli)[0] : '';
                            $hora = (int)date('H');
                            $saudacao = ($hora >= 5 && $hora < 12) ? 'Bom dia' : (($hora >= 12 && $hora < 18) ? 'Boa tarde' : 'Boa noite');
                            $usuarioNomeRaw = isset($user['nome']) ? (string)$user['nome'] : 'Equipe';
                            $usuarioNome = ucfirst($usuarioNomeRaw);
                            $mensagem = $saudacao . ', ' . $primeiroNome . ', Tudo bem? Aqui é o ' . $usuarioNome . ' da Myranda informatica.';
                            $wa = "https://wa.me/55{$tel}?text=" . urlencode($mensagem);
                            echo '<a href="' . $wa . '" target="_blank" rel="noopener">' . htmlspecialchars($telRaw) . '</a>';
                        } else { echo 'N/A'; }
                    ?>
                </p>
            </div>
            <div>
                <h3 class="mb-2">✅ Status Atual</h3>
                <?php $status_cor = safe_text($ordem, 'status_cor', '#777'); ?>
                <span class="badge" style="background-color: <?php echo $status_cor; ?>; color: #fff;"><?php echo safe_text($ordem, 'status_nome', '—'); ?></span>
            </div>
            <div>
                <h3 class="mb-2">💰 Pagamento</h3>
                <?php
                    $status_pag = safe_text($ordem, 'status_pagamento', 'pendente');
                    $pag_cor = ($status_pag === 'pago') ? '#2ecc71' : (($status_pag === 'parcial') ? '#f1c40f' : '#e74c3c');
                    $pag_label = ($status_pag === 'pago') ? 'Pago' : (($status_pag === 'parcial') ? 'Parcial' : 'Pendente');
                ?>
                <span class="badge" style="background-color: <?php echo $pag_cor; ?>; color: #fff;"><?php echo $pag_label; ?></span>
            </div>
            <div>
                <h3 class="mb-2">📦 Entrega</h3>
                <?php
                    $status_ent = safe_text($ordem, 'status_entrega', 'nao_entregue');
                    $ent_cor = ($status_ent === 'entregue') ? '#2ecc71' : '#e74c3c';
                    $ent_label = ($status_ent === 'entregue') ? 'Entregue' : 'Não Entregue';
                ?>
                <span class="badge" style="background-color: <?php echo $ent_cor; ?>; color: #fff;"><?php echo $ent_label; ?></span>
            </div>
            <div>
                <h3 class="mb-2">📅 Abertura</h3>
                <?php
                    $dataAbert = safe_val($ordem, 'data_abertura', null) ?: safe_val($ordem, 'created_at', null);
                    if (!empty($dataAbert) && strtotime((string)$dataAbert) !== false) {
                        echo '<p class="m-0">' . date('d/m/Y H:i', strtotime((string)$dataAbert)) . '</p>';
                    } else { echo '<p class="m-0">—</p>'; }
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
                <p class="m-0"><?php echo trim((safe_text($ordem, 'equipamento_tipo', '') . ' ' . safe_text($ordem, 'equipamento_marca', '') . ' ' . safe_text($ordem, 'equipamento_modelo', ''))) ?: 'N/A'; ?></p>
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
                <h3 class="mb-2">Fonte / SN Fonte / Acessórios</h3>
                <?php $fonte = safe_val($ordem, 'equipamento_fonte', null); ?>
                <p class="m-0">
                    <?php echo ($fonte === 1 || $fonte === '1' || $fonte === 'sim') ? 'Deixou' : 'Não Deixou'; ?>
                    <?php echo !empty($ordem['equipamento_sn_fonte']) ? ' (SN: ' . safe_text($ordem, 'equipamento_sn_fonte', '') . ')' : ''; ?>
                    / <?php echo safe_text($ordem, 'equipamento_acessorios', 'Nenhum'); ?>
                </p>
            </div>
        </div>
    </div>
</div>

<!-- TABS -->
<div class="container">
<div class="card mb-4">
<ul class="nav nav-tabs" id="osTabs" role="tablist">
    <li class="nav-item" role="presentation"><button class="nav-link active" id="tab-laudo" data-bs-toggle="tab" data-bs-target="#painel-laudo" type="button">🔧 Laudo e Status</button></li>
    <li class="nav-item" role="presentation"><button class="nav-link" id="tab-itens" data-bs-toggle="tab" data-bs-target="#painel-itens" type="button">🛒 Itens</button></li>
    <li class="nav-item" role="presentation"><button class="nav-link" id="tab-cobranca" data-bs-toggle="tab" data-bs-target="#painel-cobranca" type="button">💳 Cobrança / Pagamento</button></li>
    <li class="nav-item" role="presentation"><button class="nav-link" id="tab-historico" data-bs-toggle="tab" data-bs-target="#painel-historico" type="button">📜 Histórico de Status</button></li>
    <li class="nav-item" role="presentation"><button class="nav-link" id="tab-relatorio" data-bs-toggle="tab" data-bs-target="#painel-relatorio" type="button">📊 Relatório</button></li>
</ul>
<div class="tab-content p-3">

    <!-- TAB LAUDO E STATUS -->
    <div class="tab-pane fade show active" id="painel-laudo" role="tabpanel">
        <h3 class="card-title">🔧 Laudo e Status</h3>
        <form action="<?php echo BASE_URL; ?>ordens/atualizar" method="POST">
            <input type="hidden" name="id" value="<?php echo $ordem['id']; ?>">
            <div class="form-grid">
                <div class="form-group">
                    <h3 class="mb-2">Defeito Relatado (Recepção)</h3>
                    <?php $isAdmin = \App\Core\Auth::isAdmin(); ?>
                    <textarea name="defeito" class="form-control" style="min-height: 120px;" <?php echo (!$isAdmin) ? 'readonly' : ''; ?>><?php echo safe_text($ordem, 'defeito_relatado', safe_text($ordem, 'defeito', '')); ?></textarea>
                </div>
                <div class="form-group">
                    <h3 class="mb-2">Laudo Técnico / Solução</h3>
                    <textarea name="laudo_tecnico" class="form-control" style="min-height: 120px;" placeholder="Digite aqui o laudo técnico..."><?php echo safe_text($ordem, 'laudo_tecnico', ''); ?></textarea>
                </div>
            </div>
            <div class="form-grid mt-4">
                <div class="form-group">
                    <h3 class="mb-2">Alterar Status</h3>
                    <select name="status_id" class="form-control">
                        <?php foreach ($statuses as $st): ?>
                            <option value="<?php echo $st['id']; ?>" <?php echo ($st['id'] == $ordem['status_atual_id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($st['nome']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <h3 class="mb-2">Status de Pagamento</h3>
                    <select name="status_pagamento" class="form-control">
                        <option value="pendente" <?php echo (safe_text($ordem, 'status_pagamento') === 'pendente') ? 'selected' : ''; ?>>Pendente</option>
                        <option value="parcial" <?php echo (safe_text($ordem, 'status_pagamento') === 'parcial') ? 'selected' : ''; ?>>Parcial</option>
                        <option value="pago" <?php echo (safe_text($ordem, 'status_pagamento') === 'pago') ? 'selected' : ''; ?>>Pago</option>
                    </select>
                </div>
                <div class="form-group">
                    <h3 class="mb-2">Status de Entrega</h3>
                    <select name="status_entrega" class="form-control">
                        <option value="nao_entregue" <?php echo (safe_text($ordem, 'status_entrega') === 'nao_entregue') ? 'selected' : ''; ?>>Não Entregue</option>
                        <option value="entregue" <?php echo (safe_text($ordem, 'status_entrega') === 'entregue') ? 'selected' : ''; ?>>Entregue</option>
                    </select>
                </div>
                <div class="form-group">
                    <h3 class="mb-2">Observação do Status (Histórico)</h3>
                    <input type="text" name="status_observacao" class="form-control" placeholder="Ex: Peça encomendada no Mercado Livre">
                </div>
                <div class="form-group">
                    <h3 class="mb-2">📋 Impostos (Nota Fiscal)</h3>
                    <div class="d-flex align-center gap-2 mt-2">
                        <input type="checkbox" id="check_emitir_nf" <?php echo (safe_val($ordem, 'emitir_nf', 0) == 1) ? 'checked' : ''; ?> onchange="toggleNF(<?php echo $ordem['id']; ?>, this.checked ? 1 : 0, 'os')" style="width: 20px; height: 20px; cursor: pointer;">
                        <label for="check_emitir_nf" class="m-0 cursor-pointer fw-bold <?php echo (safe_val($ordem, 'emitir_nf', 0) == 1) ? 'text-success' : 'text-muted'; ?>">Emitir Nota Fiscal (Descontar custo)</label>
                    </div>
                    <?php if (safe_val($ordem, 'valor_taxa_nf', 0) > 0): ?>
                        <small class="text-danger d-block mt-1"><i class="fas fa-calculator"></i> Custo estimado do imposto: <strong><?php echo formatCurrency((float)$ordem['valor_taxa_nf']); ?></strong></small>
                    <?php endif; ?>
                </div>
            </div>
            <div class="mt-4 text-end">
                <button type="submit" class="btn btn-primary">💾 Salvar Alterações</button>
            </div>
        </form>
    </div>

    <!-- TAB ITENS -->
    <div class="tab-pane fade" id="painel-itens" role="tabpanel">
        <h3 class="card-title">➕ Adicionar Produto ou Serviço</h3>
        <form action="<?php echo BASE_URL; ?>ordens/salvar-item" method="POST" id="form-add-item" class="mb-4">
            <input type="hidden" name="ordem_servico_id" value="<?php echo $ordem['id']; ?>">
            <input type="hidden" name="produto_id" id="item_produto_id">
            <input type="hidden" name="tipo" id="item_tipo">
            <div class="form-grid align-end" style="grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap:0.75rem;">
                <div class="form-group">
                    <label>Buscar Item</label>
                    <input type="text" id="item_search" class="form-control" placeholder="Digite o nome do item..." autocomplete="off">
                    <div id="item_results" class="autocomplete-results"></div>
                </div>
                <div class="form-group"><label>Descrição</label><input type="text" name="descricao" id="item_descricao" class="form-control" required readonly title="Preenchido automaticamente a partir dos itens cadastrados"></div>
                <div class="form-group"><label>Qtd</label><input type="number" name="quantidade" id="item_quantidade" class="form-control" value="1" step="0.01" required></div>
                <div class="form-group"><label>Custo</label><input type="number" name="valor_custo" id="item_custo" class="form-control" step="0.01" value="0.00"></div>
                <div class="form-group"><label>Venda</label><input type="number" name="valor_unitario" id="item_venda" class="form-control" step="0.01" required></div>
                <div class="form-group"><label>Mão de Obra</label><input type="number" name="valor_mao_de_obra" id="item_mao_de_obra" class="form-control" step="0.01" value="0.00"></div>
                <div class="form-group"><label>Desconto</label><input type="number" name="desconto" id="item_desconto" class="form-control" step="0.01" value="0.00"></div>
                <div class="d-flex align-end"><button type="submit" class="btn btn-primary mb-1">Adicionar</button></div>
            </div>
            <div class="mt-3 p-3 bg-tertiary rounded d-flex align-center gap-3 flex-wrap">
                <div class="d-flex align-center gap-2"><input type="checkbox" name="comprar_peca" id="comprar_peca" value="1" style="width: 20px; height: 20px; cursor: pointer;"><label for="comprar_peca" class="m-0 cursor-pointer fw-bold text-info">🛒 Comprar Peça?</label></div>
                <div id="div_link_fornecedor" class="flex-1" style="display: none; min-width: 250px;"><input type="url" name="link_fornecedor" id="link_fornecedor" placeholder="Cole aqui o link do fornecedor da peça..." class="form-control"></div>
            </div>
            <div class="mt-3 p-3 bg-tertiary rounded d-flex align-center gap-3 flex-wrap">
                <div class="d-flex align-center gap-2"><input type="checkbox" name="comprado" id="comprado_add" value="1" style="width: 20px; height: 20px; cursor: pointer;"><label for="comprado_add" class="m-0 cursor-pointer fw-bold text-success">✅ Item Comprado?</label></div>
                <div class="flex-1" style="min-width: 200px;"><input type="date" name="data_compra" id="data_compra_add" class="form-control" value="<?php echo date('Y-m-d'); ?>"></div>
            </div>
        </form>

        <h3 class="card-title">🛒 Produtos e Serviços Adicionados</h3>
        <?php if (empty($itens)): ?>
            <div class="alert alert-info m-0">Nenhum produto ou serviço adicionado a esta Ordem de Serviço.</div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table>
                    <thead><tr>
                        <th style="width: 6%;">Tipo</th><th style="width: 22%;">Descrição</th><th style="width: 6%; text-align: right;">Qtd</th><th style="width: 8%; text-align: right;">Custo</th><th style="width: 8%; text-align: right;">Vlr Unit.</th><th style="width: 8%; text-align: right;">M. Obra</th><th style="width: 8%; text-align: right;">Desc.</th><th style="width: 9%; text-align: right;">Vlr Total</th><th style="width: 10%; text-align: center;">Comprado</th><th style="width: 9%; text-align: center;">Ações</th>
                    </tr></thead>
                    <tbody>
                        <?php foreach ($itens as $item): ?>
                            <?php $tipoItem = safe_val($item, 'tipo', 'produto'); ?>
                            <tr>
                                <td><span class="fw-bold" style="color: <?php echo ($tipoItem === 'servico') ? 'var(--info)' : 'var(--success)'; ?>;"><?php echo ($tipoItem === 'servico') ? '🛠️ Serv' : '📦 Prod'; ?></span></td>
                                <td><?php echo safe_text($item, 'descricao', 'N/A'); ?></td>
                                <td class="text-end"><?php echo (float)safe_val($item, 'quantidade', 0); ?></td>
                                <td class="text-end"><?php echo formatCurrency((float)safe_val($item, 'custo', 0)); ?></td>
                                <td class="text-end"><?php echo formatCurrency((float)safe_val($item, 'valor_unitario', 0)); ?></td>
                                <td class="text-end"><?php echo formatCurrency((float)safe_val($item, 'valor_mao_de_obra', 0)); ?></td>
                                <td class="text-end"><?php echo formatCurrency((float)safe_val($item, 'desconto', 0)); ?></td>
                                <td class="text-end fw-bold"><?php echo formatCurrency((float)(safe_val($item, 'valor_total', 0))); ?></td>
                                <td class="text-center"><?php echo (safe_val($item, 'comprado', 0) == 1) ? '<span class="badge" style="background:#2ecc71; color:#fff;">✅ Sim</span>' : '<span class="badge" style="background:#e74c3c; color:#fff;">❌ Não</span>'; ?></td>
                                <td class="text-center"><div class="d-flex gap-1 justify-center">
                                    <form action="<?php echo BASE_URL; ?>ordens/atualizar-item" method="POST" style="display:inline;">
                                        <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>">
                                        <input type="hidden" name="ordem_servico_id" value="<?php echo $ordem['id']; ?>">
                                        <input type="hidden" name="descricao" value="<?php echo safe_text($item, 'descricao', ''); ?>">
                                        <input type="hidden" name="quantidade" value="<?php echo (float)safe_val($item, 'quantidade', 0); ?>">
                                        <input type="hidden" name="custo" value="<?php echo (float)safe_val($item, 'custo', 0); ?>">
                                        <input type="hidden" name="valor_unitario" value="<?php echo (float)safe_val($item, 'valor_unitario', 0); ?>">
                                        <input type="hidden" name="valor_mao_de_obra" value="<?php echo (float)safe_val($item, 'valor_mao_de_obra', 0); ?>">
                                        <input type="hidden" name="desconto" value="<?php echo (float)safe_val($item, 'desconto', 0); ?>">
                                        <input type="hidden" name="comprado" value="<?php echo (safe_val($item, 'comprado', 0) == 1) ? 1 : 0; ?>">
                                        <input type="hidden" name="data_compra" value="<?php echo safe_text($item, 'data_compra', date('Y-m-d')); ?>">
                                        <button type="submit" class="btn btn-sm btn-success" title="Salvar alterações">💾</button>
                                    </form>
                                    <form action="<?php echo BASE_URL; ?>ordens/remover-item" method="POST" onsubmit="return confirm('Remover este item?');" style="display:inline;">
                                        <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>">
                                        <input type="hidden" name="ordem_servico_id" value="<?php echo $ordem['id']; ?>">
                                        <button type="submit" class="btn btn-sm btn-danger" title="Remover item">🗑️</button>
                                    </form>
                                </div></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr class="bg-tertiary"><td colspan="7" class="text-end fw-bold">TOTAL DA OS (itens comprados):</td><td class="text-end fw-bold text-primary" style="font-size: 1.2rem;"><?php echo formatCurrency($calcSomaLiquida); ?></td><td colspan="2"></td></tr>
                    </tfoot>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <!-- TAB COBRANÇA -->
    <div class="tab-pane fade" id="painel-cobranca" role="tabpanel">
        <h3 class="card-title">💳 Pagamentos</h3>
        <div class="form-grid mb-3">
            <div class="col-span-3">
                <p class="m-0">Total Final OS: <strong class="text-primary" style="font-size: 1.1rem;"><?php echo formatCurrency($calcSomaLiquida); ?></strong></p>
                <p class="m-0">Total Pago: <strong class="text-success" style="font-size: 1.1rem;"><?php echo formatCurrency($totalPago); ?></strong></p>
                <p class="m-0">Saldo Devedor: <strong class="text-warning" style="font-size: 1.1rem;"><?php echo formatCurrency($saldo); ?></strong></p>
            </div>
        </div>
        <form id="form-pagamento" onsubmit="return registrarPagamentoOS(event)" class="mb-3">
            <input type="hidden" name="tipo_origem" value="os">
            <input type="hidden" name="origem_id" value="<?php echo safe_text($ordem, 'id', ''); ?>">
            <div class="form-grid">
                <div class="form-group"><label>Máquina</label>
                    <select name="maquina" class="form-control">
                        <?php if (empty($maquinas)): ?><option value="">Nenhuma máquina habilitada</option><?php else: foreach ($maquinas as $mq): ?><option value="<?php echo htmlspecialchars($mq); ?>"><?php echo htmlspecialchars($mq); ?></option><?php endforeach; endif; ?>
                    </select>
                </div>
                <div class="form-group"><label>Forma</label>
                    <?php $formasPadrao = ['dinheiro','boleto','pix','debito','credito']; $listaFormas = array_unique(array_merge($formas, $formasPadrao)); ?>
                    <select name="forma" class="form-control" id="select-forma-os">
                        <?php foreach ($listaFormas as $f): ?><option value="<?php echo htmlspecialchars($f); ?>"><?php echo htmlspecialchars(ucfirst($f)); ?></option><?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group"><label>Bandeira</label>
                    <select name="bandeira" class="form-control"><option value="">Selecione</option><?php foreach ($bandeiras as $b): ?><option value="<?php echo htmlspecialchars($b); ?>"><?php echo htmlspecialchars(ucfirst($b)); ?></option><?php endforeach; ?></select>
                </div>
                <div class="form-group"><label>Parcelas</label><input type="number" name="parcelas" class="form-control" value="1" min="1" id="input-parcelas-os"></div>
                <div class="form-group"><label>Valor</label><input type="number" name="valor_bruto" class="form-control" step="0.01" min="0" required></div>
            </div>
            <div class="mt-3 text-end"><button type="submit" class="btn btn-primary">Registrar</button></div>
        </form>
        <div id="pagamento-msg" class="mt-2"></div>
        <div class="mt-3">
            <h3 class="mb-2">Transações</h3>
            <?php if (empty($transacoes)): ?>
                <div class="alert alert-info m-0">Nenhuma transação registrada.</div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="table">
                        <thead><tr><th>Data</th><th>Máquina</th><th>Forma</th><th>Bandeira</th><th>Parcelas</th><th class="text-end">Bruto</th><th class="text-end">Taxa (%)</th><th class="text-end">Taxa (R$)</th><th class="text-end">Líquido</th><th class="text-center">Ações</th></tr></thead>
                        <tbody>
                            <?php foreach (($transacoes ?? []) as $t): ?>
                                <tr>
                                    <td><?php echo date('d/m/Y H:i', strtotime($t['created_at'])); ?></td>
                                    <td><?php echo htmlspecialchars($t['maquina'] ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($t['forma'] ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($t['bandeira'] ?? ''); ?></td>
                                    <td><?php echo (int)($t['parcelas'] ?? 1); ?></td>
                                    <td class="text-end"><?php echo formatCurrency((float)$t['valor_bruto']); ?></td>
                                    <td class="text-end"><?php echo number_format((float)($t['taxa_percentual'] ?? 0), 2, ',', '.'); ?>%</td>
                                    <td class="text-end"><?php echo formatCurrency((float)($t['valor_taxa'] ?? 0)); ?></td>
                                    <td class="text-end"><?php echo formatCurrency((float)($t['valor_liquido'] ?? 0)); ?></td>
                                    <td class="text-center"><button class="btn btn-danger btn-sm" onclick="return excluirTransacao('os', <?php echo (int)$ordem['id']; ?>, <?php echo (int)$t['id']; ?>)">Excluir</button></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr class="bg-tertiary"><td colspan="8" class="text-end fw-bold">VALOR LÍQUIDO RECEBIDO (CARTÃO/PIX/DINHEIRO):</td><td class="text-end fw-bold text-info"><?php echo formatCurrency($totalLiquidoRecebido); ?></td><td></td></tr>
                            <tr class="bg-tertiary"><td colspan="8" class="text-end text-danger">(-) CUSTO TOTAL DE PEÇAS:</td><td class="text-end text-danger fw-bold">- <?php echo formatCurrency($calcSomaCusto); ?></td><td></td></tr>
                            <?php if ($custoNF > 0): ?>
                            <tr class="bg-tertiary"><td colspan="8" class="text-end text-danger">(-) IMPOSTOS (NOTA FISCAL):</td><td class="text-end text-danger fw-bold">- <?php echo formatCurrency($custoNF); ?></td><td></td></tr>
                            <?php endif; ?>
                            <tr style="background: #f8f9fa; border-top: 2px solid #333;"><td colspan="8" class="text-end fw-bold" style="font-size: 1.1rem;">LUCRO LÍQUIDO FINAL (CAIXA):</td><td class="text-end fw-bold <?php echo $lucroLiquidoReal >= 0 ? 'text-success' : 'text-danger'; ?>" style="font-size: 1.3rem; border: 2px solid #333; border-radius: 4px;"><?php echo formatCurrency($lucroLiquidoReal); ?></td><td></td></tr>
                        </tfoot>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- TAB HISTÓRICO DE STATUS -->
    <div class="tab-pane fade" id="painel-historico" role="tabpanel">
        <h3 class="card-title">📜 Histórico de Status</h3>
        <?php if (empty($historico)): ?>
            <div class="alert alert-info m-0" style="background-color: var(--bg-tertiary); color: var(--text-primary); border: 1px solid var(--border-color);">Nenhum histórico registrado.</div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="table">
                    <thead><tr><th>Data/Hora</th><th>Status</th><th>Usuário</th><th>Observação</th></tr></thead>
                    <tbody>
                        <?php foreach ($historico as $h): ?>
                            <tr>
                                <td><?php echo date('d/m/Y H:i', strtotime($h['created_at'])); ?></td>
                                <td><span class="badge" style="background-color: <?php echo $h['status_cor']; ?>; color: #fff;"><?php echo htmlspecialchars($h['status_nome']); ?></span></td>
                                <td><?php echo htmlspecialchars($h['usuario_nome'] ?? 'Sistema'); ?></td>
                                <td><?php echo htmlspecialchars($h['observacao'] ?? '—'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <!-- TAB LAUDO E STATUS -->
    <div class="tab-pane fade" id="painel-laudo" role="tabpanel">
        <h3 class="card-title">🔧 Laudo e Status</h3>
        <form action="<?php echo BASE_URL; ?>ordens/atualizar" method="POST">
            <input type="hidden" name="id" value="<?php echo $ordem['id']; ?>">
            <div class="form-grid">
                <div class="form-group">
                    <h3 class="mb-2">Defeito Relatado (Recepção)</h3>
                    <?php $isAdmin = \App\Core\Auth::isAdmin(); ?>
                    <textarea name="defeito" class="form-control" style="min-height: 120px;" <?php echo (!$isAdmin) ? 'readonly' : ''; ?>><?php echo safe_text($ordem, 'defeito_relatado', safe_text($ordem, 'defeito', '')); ?></textarea>
                </div>
                <div class="form-group">
                    <h3 class="mb-2">Laudo Técnico / Solução</h3>
                    <textarea name="laudo_tecnico" class="form-control" style="min-height: 120px;" placeholder="Digite aqui o laudo técnico..."><?php echo safe_text($ordem, 'laudo_tecnico', ''); ?></textarea>
                </div>
            </div>
            <div class="form-grid mt-4">
                <div class="form-group">
                    <h3 class="mb-2">Alterar Status</h3>
                    <select name="status_id" class="form-control">
                        <?php foreach ($statuses as $st): ?>
                            <option value="<?php echo $st['id']; ?>" <?php echo ($st['id'] == $ordem['status_atual_id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($st['nome']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <h3 class="mb-2">Status de Pagamento</h3>
                    <select name="status_pagamento" class="form-control">
                        <option value="pendente" <?php echo (safe_text($ordem, 'status_pagamento') === 'pendente') ? 'selected' : ''; ?>>Pendente</option>
                        <option value="parcial" <?php echo (safe_text($ordem, 'status_pagamento') === 'parcial') ? 'selected' : ''; ?>>Parcial</option>
                        <option value="pago" <?php echo (safe_text($ordem, 'status_pagamento') === 'pago') ? 'selected' : ''; ?>>Pago</option>
                    </select>
                </div>
                <div class="form-group">
                    <h3 class="mb-2">Status de Entrega</h3>
                    <select name="status_entrega" class="form-control">
                        <option value="nao_entregue" <?php echo (safe_text($ordem, 'status_entrega') === 'nao_entregue') ? 'selected' : ''; ?>>Não Entregue</option>
                        <option value="entregue" <?php echo (safe_text($ordem, 'status_entrega') === 'entregue') ? 'selected' : ''; ?>>Entregue</option>
                    </select>
                </div>
                <div class="form-group">
                    <h3 class="mb-2">Observação do Status (Histórico)</h3>
                    <input type="text" name="status_observacao" class="form-control" placeholder="Ex: Peça encomendada no Mercado Livre">
                </div>
                <div class="form-group">
                    <h3 class="mb-2">📋 Impostos (Nota Fiscal)</h3>
                    <div class="d-flex align-center gap-2 mt-2">
                        <input type="checkbox" id="check_emitir_nf" <?php echo (safe_val($ordem, 'emitir_nf', 0) == 1) ? 'checked' : ''; ?> onchange="toggleNF(<?php echo $ordem['id']; ?>, this.checked ? 1 : 0, 'os')" style="width: 20px; height: 20px; cursor: pointer;">
                        <label for="check_emitir_nf" class="m-0 cursor-pointer fw-bold <?php echo (safe_val($ordem, 'emitir_nf', 0) == 1) ? 'text-success' : 'text-muted'; ?>">Emitir Nota Fiscal (Descontar custo)</label>
                    </div>
                    <?php if (safe_val($ordem, 'valor_taxa_nf', 0) > 0): ?>
                        <small class="text-danger d-block mt-1"><i class="fas fa-calculator"></i> Custo estimado do imposto: <strong><?php echo formatCurrency((float)$ordem['valor_taxa_nf']); ?></strong></small>
                    <?php endif; ?>
                </div>
            </div>
            <div class="mt-4 text-end">
                <button type="submit" class="btn btn-primary">💾 Salvar Alterações</button>
            </div>
        </form>
    </div>

    <!-- TAB RELATÓRIO -->
    <div class="tab-pane fade" id="painel-relatorio" role="tabpanel">
        <h3 class="card-title">📊 Relatório da OS</h3>
        <div class="overflow-x-auto">
            <table class="table table-bordered">
                <tbody>
                    <tr><td class="fw-bold" style="width:200px;">OS</td><td>#<?php echo $ordem['id']; ?></td></tr>
                    <tr><td class="fw-bold">Cliente</td><td><?php echo safe_text($ordem, 'cliente_nome', '—'); ?></td></tr>
                    <tr><td class="fw-bold">Equipamento</td><td><?php echo safe_text($ordem, 'equipamento_tipo', '') . ' ' . safe_text($ordem, 'equipamento_marca', '') . ' ' . safe_text($ordem, 'equipamento_modelo', ''); ?></td></tr>
                    <tr><td class="fw-bold">Defeito</td><td><?php echo safe_text($ordem, 'defeito_relatado', '—'); ?></td></tr>
                    <tr><td class="fw-bold">Laudo Técnico</td><td><?php echo safe_text($ordem, 'laudo_tecnico', '—'); ?></td></tr>
                    <tr><td class="fw-bold">Status</td><td><span class="badge" style="background-color: <?php echo $status_cor; ?>; color: #fff;"><?php echo safe_text($ordem, 'status_nome', '—'); ?></span></td></tr>
                    <tr><td class="fw-bold">Pagamento</td><td><?php echo $pag_label; ?></td></tr>
                    <tr><td class="fw-bold">Entrega</td><td><?php echo $ent_label; ?></td></tr>
                    <tr><td class="fw-bold">Emitir NF</td><td><?php echo safe_val($ordem, 'emitir_nf', 0) == 1 ? 'Sim' : 'Não'; ?></td></tr>
                    <tr><td class="fw-bold">Valor Total OS</td><td><?php echo formatCurrency($calcSomaLiquida); ?></td></tr>
                    <tr><td class="fw-bold">Total Pago</td><td><?php echo formatCurrency($totalPago); ?></td></tr>
                    <tr><td class="fw-bold">Saldo Devedor</td><td><?php echo formatCurrency($saldo); ?></td></tr>
                    <tr><td class="fw-bold">Custo de Peças</td><td class="text-danger">- <?php echo formatCurrency($calcSomaCusto); ?></td></tr>
                    <tr><td class="fw-bold">Taxa NF</td><td class="text-danger">- <?php echo formatCurrency($custoNF); ?></td></tr>
                    <tr style="background: #f8f9fa;"><td class="fw-bold" style="font-size: 1.1rem;">LUCRO LÍQUIDO FINAL (CAIXA)</td><td class="fw-bold <?php echo $lucroLiquidoReal >= 0 ? 'text-success' : 'text-danger'; ?>" style="font-size: 1.3rem;"><?php echo formatCurrency($lucroLiquidoReal); ?></td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
</div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('item_search');
    const resultsDiv = document.getElementById('item_results');
    const form = document.getElementById('form-add-item');
    const comprarPecaCheckbox = document.getElementById('comprar_peca');
    const divLinkFornecedor = document.getElementById('div_link_fornecedor');

    comprarPecaCheckbox.addEventListener('change', function() { divLinkFornecedor.style.display = this.checked ? 'block' : 'none'; });

    window.registrarPagamentoOS = function(e) {
        e.preventDefault();
        const form = document.getElementById('form-pagamento');
        const msgDiv = document.getElementById('pagamento-msg');
        const formData = new FormData(form);
        fetch('<?php echo BASE_URL; ?>pagamentos/registrar', { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest' }, credentials: 'include', body: formData })
        .then(r => r.json())
        .then(res => {
            if (res && res.success) { msgDiv.innerHTML = '<div class="alert alert-success">Pagamento registrado com sucesso.</div>'; setTimeout(() => { window.location.reload(); }, 800); }
            else { msgDiv.innerHTML = '<div class="alert alert-danger">Erro ao registrar pagamento.</div>'; }
        })
        .catch(() => { msgDiv.innerHTML = '<div class="alert alert-danger">Erro de comunicação.</div>'; });
        return false;
    }

    window.toggleNF = function(id, value, type) {
        const fd = new FormData(); fd.append('id', id); fd.append('value', value); fd.append('type', type);
        fetch('<?php echo BASE_URL; ?>ordens/toggle-nf', { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest' }, credentials: 'include', body: fd })
        .then(r => r.json())
        .then(res => { if (res && res.success) { window.location.reload(); } else { alert('Erro ao atualizar opção de NF: ' + (res.error || 'Erro desconhecido')); } })
        .catch(() => { alert('Erro de comunicação ao atualizar NF.'); });
    }

    window.excluirTransacao = function(tipo, origemId, transacaoId) {
        const msgDiv = document.getElementById('pagamento-msg');
        const fd = new FormData(); fd.append('transacao_id', transacaoId);
        fetch('<?php echo BASE_URL; ?>pagamentos/deletar', { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest' }, credentials: 'include', body: fd })
        .then(r => r.json())
        .then(res => { if (res && res.success) { msgDiv.innerHTML = '<div class="alert alert-success">Transação excluída.</div>'; setTimeout(() => { window.location.reload(); }, 600); } else { msgDiv.innerHTML = '<div class="alert alert-danger">Erro ao excluir transação.</div>'; } })
        .catch(() => { msgDiv.innerHTML = '<div class="alert alert-danger">Erro de comunicação.</div>'; });
        return false;
    }

    let timeout = null;
    searchInput.addEventListener('input', function() {
        clearTimeout(timeout);
        const termo = this.value.trim();
        if (termo.length < 2) { resultsDiv.style.display = 'none'; return; }
        timeout = setTimeout(() => {
            fetch(`<?php echo BASE_URL; ?>ordens/search-items?termo=${encodeURIComponent(termo)}`).then(response => response.json()).then(data => {
                resultsDiv.innerHTML = '';
                if (data.length > 0) {
                    data.forEach(item => {
                        const div = document.createElement('div'); div.className = 'autocomplete-item';
                        div.innerHTML = `<div class="d-flex justify-between"><span>${item.nome}</span><span class="badge ${item.tipo === 'servico' ? 'bg-info' : 'bg-success'}">${item.tipo}</span></div>`;
                        div.addEventListener('click', () => {
                            document.getElementById('item_produto_id').value = item.id; document.getElementById('item_tipo').value = item.tipo;
                            document.getElementById('item_descricao').value = item.nome; document.getElementById('item_custo').value = item.custo;
                            document.getElementById('item_venda').value = item.valor_venda; document.getElementById('item_mao_de_obra').value = item.mao_de_obra;
                            searchInput.value = item.nome; resultsDiv.style.display = 'none';
                        });
                        resultsDiv.appendChild(div);
                    });
                    resultsDiv.style.display = 'block';
                } else { resultsDiv.style.display = 'none'; }
            });
        }, 300);
    });
    document.addEventListener('click', function(e) { if (e.target !== searchInput) { resultsDiv.style.display = 'none'; } });
});
</script>

<style>
/* === MELHORIA VISUAL DAS ABAS === */
.nav-tabs {
    border-bottom: 2px solid var(--border-color, #e0e0e0);
    margin-bottom: 1.5rem;
}
.nav-tabs .nav-link {
    padding: 0.75rem 1.5rem;
    font-size: 1rem;
    font-weight: 600;
    color: var(--text-muted, #888);
    border: 1px solid transparent;
    border-radius: 8px 8px 0 0;
    margin-right: 4px;
    transition: all 0.2s ease;
    background: transparent;
}
.nav-tabs .nav-link:hover {
    color: var(--info, #2196F3);
    background: var(--bg-tertiary, #f5f5f5);
    border-color: var(--border-color, #e0e0e0);
    border-bottom-color: transparent;
}
.nav-tabs .nav-link.active {
    color: var(--info, #2196F3);
    background: var(--bg-secondary, #fff);
    border-color: var(--border-color, #e0e0e0);
    border-bottom-color: transparent;
    font-weight: 700;
    box-shadow: 0 -2px 4px rgba(0,0,0,0.05);
}
.tab-content {
    padding: 1.5rem;
    background: var(--bg-secondary, #fff);
    border: 1px solid var(--border-color, #e0e0e0);
    border-radius: 0 8px 8px 8px;
}
/* Melhorar cards gerais */
.card {
    border-radius: 10px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    border: 1px solid var(--border-color, #eaeaea);
}
.card-title {
    font-size: 1.15rem;
    font-weight: 700;
    color: var(--text-primary, #333);
    margin-bottom: 1rem;
}
/* Melhorar tabelas */
.table thead th {
    background-color: var(--bg-tertiary, #f8f9fa);
    color: var(--text-primary, #333);
    font-weight: 700;
    font-size: 0.9rem;
    padding: 10px 12px;
    border-bottom: 2px solid var(--border-color, #e0e0e0);
}
.table tbody td {
    color: var(--text-primary, #333);
    border-bottom: 1px solid var(--border-color, #eaeaea);
    padding: 10px 12px;
    vertical-align: middle;
}
.table tfoot td {
    padding: 10px 12px;
    font-size: 1rem;
}
/* Melhorar badges */
.badge {
    font-size: 0.8rem;
    padding: 4px 10px;
    border-radius: 12px;
    font-weight: 600;
}
/* Botões gerais */
.btn {
    border-radius: 6px;
    font-weight: 600;
    padding: 6px 16px;
    font-size: 0.9rem;
}
/* Formulários */
.form-control {
    border-radius: 6px;
    border: 1px solid var(--border-color, #ddd);
    padding: 8px 12px;
}
.form-control:focus {
    border-color: var(--info, #2196F3);
    box-shadow: 0 0 0 3px rgba(33,150,243,0.1);
}
.form-group {
    margin-bottom: 0.75rem;
}
</style>

<!-- MODAL RETORNO -->
<div id="modalRetorno" class="modal" style="display:none; position:fixed; z-index:1000; left:0; top:0; width:100%; height:100%; background:rgba(0,0,0,0.6); align-items: center; justify-content: center;">
    <div class="modal-content card" style="background: var(--bg-secondary, #fff); color: var(--text-primary, #000); margin: 10% auto; padding: 20px; width: 500px; border-radius: 8px; border: 1px solid var(--border-color, #ccc);">
        <div class="d-flex justify-between mb-3" style="display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0;">🔄 Gerar Retorno de OS</h3>
            <span style="cursor:pointer; font-size:24px;" onclick="fecharModalRetorno()">&times;</span>
        </div>
        <form action="<?php echo BASE_URL; ?>ordens/retorno" method="POST">
            <input type="hidden" name="parent_id" value="<?php echo $ordem['id']; ?>">
            <p style="margin-bottom: 15px;">Você está gerando uma nova OS de retorno para o cliente <strong><?php echo safe_text($ordem, 'cliente_nome', 'N/A'); ?></strong> referente ao equipamento <strong><?php echo safe_text($ordem, 'equipamento_tipo', '') . ' ' . safe_text($ordem, 'equipamento_marca', '') . ' ' . safe_text($ordem, 'equipamento_modelo', ''); ?></strong>.</p>
            <div class="form-group mb-4"><label class="form-label d-block mb-2" style="font-weight: bold;">Detalhes do Retorno (o que houve):</label><textarea name="detalhes" class="form-control" style="width: 100%; min-height: 100px; box-sizing: border-box;" placeholder="Descreva se voltou o mesmo problema ou se gerou algo novo... (deixe em branco se não houver detalhes)"></textarea></div>
            <div class="d-flex justify-end gap-2" style="display: flex; justify-content: flex-end; gap: 10px;">
                <button type="button" class="btn btn-secondary" onclick="fecharModalRetorno()">Cancelar</button>
                <button type="submit" class="btn btn-danger" style="background-color: #d32f2f; border-color: #c62828; color: white;">Confirmar e Gerar OS</button>
            </div>
        </form>
    </div>
</div>

<script>
function abrirModalRetorno() { document.getElementById('modalRetorno').style.display = 'flex'; }
function fecharModalRetorno() { document.getElementById('modalRetorno').style.display = 'none'; }
window.addEventListener('click', function(event) { const modal = document.getElementById('modalRetorno'); if (event.target == modal) { modal.style.display = 'none'; } });
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
