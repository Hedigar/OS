<?php
$current_page = 'ordens';
require_once __DIR__ . '/../layout/main.php';

$ordem = $ordem ?? [];
$itens = $itens ?? [];
$historico = $historico ?? [];
$statuses = $statuses ?? [];

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
            <a href="<?php echo BASE_URL; ?>ordens/form?id=<?php echo safe_text($ordem, 'id', ''); ?>" class="btn btn-info">
                ✏️ Editar OS
            </a>
            <a href="<?php echo BASE_URL; ?>ordens/print-receipt?id=<?php echo safe_text($ordem, 'id', ''); ?>" target="_blank" class="btn btn-primary">
                🖨️ Imprimir OS
            </a>
            <a href="<?php echo BASE_URL; ?>ordens/print-payment-receipt?id=<?php echo safe_text($ordem, 'id', ''); ?>" target="_blank" class="btn btn-warning" style="background-color: #ff9800; border-color: #f57c00; color: white;">
                🧾 Recibo (80mm)
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
                    <p class="m-0">
                        <a href="<?php echo BASE_URL; ?>clientes/view?id=<?php echo safe_text($ordem, 'cliente_id', ''); ?>" 
           style="text-decoration: none; color: var(--info); font-weight: bold;">
                        <?php echo safe_text($ordem, 'cliente_nome', 'N/A'); ?>
                        </a>
                     </p>
            </div>

            <!-- Celular -->
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
                        } else {
                            echo 'N/A';
                        }
                    ?>
                </p>
            </div>

            <!-- Status -->
            <div>
                <h3 class="mb-2">✅ Status Atual</h3>
                <?php $status_cor = safe_text($ordem, 'status_cor', '#777'); ?>
                <span class="badge" style="background-color: <?php echo $status_cor; ?>; color: #fff;">
                    <?php echo safe_text($ordem, 'status_nome', '—'); ?>
                </span>
            </div>

            <!-- Status Pagamento -->
            <div>
                <h3 class="mb-2">💰 Pagamento</h3>
                <?php 
                    $status_pag = safe_text($ordem, 'status_pagamento', 'pendente');
                    $pag_cor = ($status_pag === 'pago') ? '#2ecc71' : (($status_pag === 'parcial') ? '#f1c40f' : '#e74c3c');
                    $pag_label = ($status_pag === 'pago') ? 'Pago' : (($status_pag === 'parcial') ? 'Parcial' : 'Pendente');
                ?>
                <span class="badge" style="background-color: <?php echo $pag_cor; ?>; color: #fff;">
                    <?php echo $pag_label; ?>
                </span>
            </div>

            <!-- Status Entrega -->
            <div>
                <h3 class="mb-2">📦 Entrega</h3>
                <?php 
                    $status_ent = safe_text($ordem, 'status_entrega', 'nao_entregue');
                    $ent_cor = ($status_ent === 'entregue') ? '#2ecc71' : '#e74c3c';
                    $ent_label = ($status_ent === 'entregue') ? 'Entregue' : 'Não Entregue';
                ?>
                <span class="badge" style="background-color: <?php echo $ent_cor; ?>; color: #fff;">
                    <?php echo $ent_label; ?>
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

    <!-- CARD DE PROBLEMA E LAUDO -->
    <div class="card mb-4">
        <h2 class="card-title">Laudo e Status</h2>
        <form action="<?php echo BASE_URL; ?>ordens/atualizar" method="POST">
            <input type="hidden" name="id" value="<?php echo $ordem['id']; ?>">
            
            <div class="form-grid">
                <div class="form-group">
                    <h3 class="mb-2">Defeito Relatado (Recepção)</h3>
                    <?php 
                        $isAdmin = \App\Core\Auth::isAdmin();
                    ?>
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
                            <option value="<?php echo $st['id']; ?>" <?php echo ($st['id'] == $ordem['status_atual_id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($st['nome']); ?>
                            </option>
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
            </div>

            <div class="mt-4 text-end">
                <button type="submit" class="btn btn-primary">💾 Salvar Alterações</button>
            </div>
        </form>
    </div>

    <!-- CARD DE HISTÓRICO DE STATUS -->
    <div class="card mb-4">
        <h2 class="card-title">📜 Histórico de Status</h2>
        <?php if (empty($historico)): ?>
            <div class="alert alert-info m-0" style="background-color: var(--bg-tertiary); color: var(--text-primary); border: 1px solid var(--border-color);">Nenhum histórico registrado.</div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Data/Hora</th>
                            <th>Status</th>
                            <th>Usuário</th>
                            <th>Observação</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($historico as $h): ?>
                            <tr>
                                <td><?php echo date('d/m/Y H:i', strtotime($h['created_at'])); ?></td>
                                <td>
                                    <span class="badge" style="background-color: <?php echo $h['status_cor']; ?>; color: #fff;">
                                        <?php echo htmlspecialchars($h['status_nome']); ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars($h['usuario_nome'] ?? 'Sistema'); ?></td>
                                <td><?php echo htmlspecialchars($h['observacao'] ?? '—'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- CARD DE PAGAMENTOS -->
    <div class="card mb-4">
        <h2 class="card-title">💳 Pagamentos</h2>
        <?php
            // Recálculo preciso dos totais baseado nos itens
            $calcSomaBruta = 0;
            $calcSomaDescontos = 0;
            $calcSomaLiquida = 0;

            if (!empty($itens)) {
                foreach ($itens as $item) {
                    $vUnit = (float)($item['valor_unitario'] ?? 0);
                    $vMao = (float)($item['valor_mao_de_obra'] ?? 0);
                    $qtd = (float)($item['quantidade'] ?? 1);
                    $desc = (float)($item['desconto'] ?? 0);
                    $vTotalItem = (float)($item['valor_total'] ?? 0);
                    
                    // Valor unitário completo (peça + mão de obra)
                    $vUnitFull = $vUnit + $vMao;
                    // Valor bruto total deste item
                    $vBrutoItem = $vUnitFull * $qtd;
                    
                    $calcSomaBruta += $vBrutoItem;
                    $calcSomaDescontos += $desc;
                    $calcSomaLiquida += $vTotalItem;
                }
            } else {
                // Fallback se não houver itens carregados
                $calcSomaLiquida = (float)(safe_val($ordem, 'valor_total_os', 0));
                $calcSomaDescontos = (float)(safe_val($ordem, 'valor_desconto', 0));
                $calcSomaBruta = $calcSomaLiquida + $calcSomaDescontos;
            }

            $totalPago = (float)($total_pago ?? 0);
            $saldo = max(0, $calcSomaLiquida - $totalPago);
        ?>
        <div class="form-grid">
            <div>
                <h3 class="mb-2">Resumo</h3>
                <p class="m-0">Subtotal: <strong><?php echo formatCurrency($calcSomaBruta); ?></strong></p>
                <?php if ($calcSomaDescontos > 0): ?>
                <p class="m-0">Descontos: <strong>- <?php echo formatCurrency($calcSomaDescontos); ?></strong></p>
                <?php endif; ?>
                <p class="m-0">Total a Pagar: <strong class="text-primary"><?php echo formatCurrency($calcSomaLiquida); ?></strong></p>
                <p class="m-0">Pago: <strong class="text-success"><?php echo formatCurrency($totalPago); ?></strong></p>
                <p class="m-0">Saldo: <strong class="text-warning"><?php echo formatCurrency($saldo); ?></strong></p>
            </div>
            <div>
                <h3 class="mb-2">Registrar Pagamento</h3>
                <form id="form-pagamento" onsubmit="return registrarPagamentoOS(event)">
                    <input type="hidden" name="tipo_origem" value="os">
                    <input type="hidden" name="origem_id" value="<?php echo safe_text($ordem, 'id', ''); ?>">
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Máquina</label>
                            <?php $listaMaquinas = $maquinas ?? []; ?>
                            <select name="maquina" class="form-control">
                                <?php if (empty($listaMaquinas)): ?>
                                    <option value="">Nenhuma máquina habilitada</option>
                                <?php else: ?>
                                    <?php foreach ($listaMaquinas as $mq): ?>
                                        <option value="<?php echo htmlspecialchars($mq); ?>"><?php echo htmlspecialchars($mq); ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Forma</label>
                            <?php 
                            $formasMaquinas = isset($formas) && is_array($formas) ? $formas : [];
                            // Garante que dinheiro e boleto (e outros comuns) estejam sempre disponíveis
                            $formasPadrao = ['dinheiro', 'boleto', 'pix', 'debito', 'credito'];
                            $listaFormas = array_unique(array_merge($formasMaquinas, $formasPadrao));
                            ?>
                            <select name="forma" class="form-control" id="select-forma-os">
                                <?php foreach ($listaFormas as $f): ?>
                                    <option value="<?php echo htmlspecialchars($f); ?>"><?php echo htmlspecialchars(ucfirst($f)); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Bandeira</label>
                            <?php $listaBandeiras = isset($bandeiras) && is_array($bandeiras) && count($bandeiras) ? $bandeiras : ['visa','mastercard','elo','amex','hipercard','outros']; ?>
                            <select name="bandeira" class="form-control">
                                <option value="">Selecione</option>
                                <?php foreach ($listaBandeiras as $b): ?>
                                    <option value="<?php echo htmlspecialchars($b); ?>"><?php echo htmlspecialchars(ucfirst($b)); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Parcelas</label>
                            <input type="number" name="parcelas" class="form-control" value="1" min="1" id="input-parcelas-os">
                        </div>
                        <script>
                            document.getElementById('input-parcelas-os').addEventListener('input', function() {
                                var val = parseInt(this.value);
                                if (val > 1) {
                                    var select = document.getElementById('select-forma-os');
                                    // Tenta selecionar 'credito' se existir
                                    for (var i = 0; i < select.options.length; i++) {
                                        if (select.options[i].value === 'credito') {
                                            select.selectedIndex = i;
                                            break;
                                        }
                                    }
                                }
                            });
                        </script>
                        <div class="form-group">
                            <label>Valor</label>
                            <input type="number" name="valor_bruto" class="form-control" step="0.01" min="0" required>
                        </div>
                    </div>
                    <div class="mt-3 text-end">
                        <button type="submit" class="btn btn-primary">Registrar</button>
                    </div>
                </form>
                <div id="pagamento-msg" class="mt-2"></div>
            </div>
        </div>
        <div class="mt-3">
            <h3 class="mb-2">Transações</h3>
            <?php if (empty($transacoes ?? [])): ?>
                <div class="alert alert-info m-0">Nenhuma transação registrada.</div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Data</th>
                                <th>Máquina</th>
                                <th>Forma</th>
                                <th>Bandeira</th>
                                <th>Parcelas</th>
                                <th class="text-end">Bruto</th>
                                <th class="text-end">Taxa (%)</th>
                                <th class="text-end">Taxa (R$)</th>
                                <th class="text-end">Líquido</th>
                                <th class="text-center">Ações</th>
                            </tr>
                        </thead>
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
                                    <td class="text-center">
                                        <button class="btn btn-danger btn-sm" onclick="return excluirTransacao('os', <?php echo (int)$ordem['id']; ?>, <?php echo (int)$t['id']; ?>)">Excluir</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
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
                <div class="form-group">
                    <label>Desconto</label>
                    <input type="number" name="desconto" id="item_desconto" class="form-control" step="0.01" value="0.00">
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
                                <th style="width: 25%;">Descrição</th>
                                <th style="width: 8%; text-align: right;">Qtd</th>
                                <th style="width: 10%; text-align: right;">Custo</th>
                                <th style="width: 10%; text-align: right;">Vlr Unit.</th>
                                <th style="width: 10%; text-align: right;">M. Obra</th>
                                <th style="width: 10%; text-align: right;">Desconto</th>
                                <th style="width: 10%; text-align: right;">Vlr Total</th>
                                <th style="width: 10%; text-align: center;">Compra</th>
                                <th style="width: 9%; text-align: center;">Ações</th>
                            </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($itens as $item): ?>
                            <?php $tipoItem = safe_val($item, 'tipo', 'produto'); ?>
                            <tr>
                                <form action="<?php echo BASE_URL; ?>ordens/atualizar-item" method="POST">
                                    <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>">
                                    <input type="hidden" name="ordem_servico_id" value="<?php echo $ordem['id']; ?>">
                                    <td>
                                        <span class="fw-bold" style="color: <?php echo ($tipoItem === 'servico') ? 'var(--info)' : 'var(--success)'; ?>;">
                                            <?php echo ($tipoItem === 'servico') ? '🛠️ Serv' : '📦 Prod'; ?>
                                        </span>
                                    </td>
                                    <td><?php echo safe_text($item, 'descricao', 'N/A'); ?></td>
                                    <td class="text-end">
                                        <input type="number" name="quantidade" value="<?php echo (float)safe_val($item, 'quantidade', 0); ?>" step="0.01" class="form-control form-control-sm text-end" style="width: 70px; display: inline-block;">
                                    </td>
                                    <td class="text-end">
                                        <input type="number" name="custo" value="<?php echo (float)safe_val($item, 'custo', 0); ?>" step="0.01" class="form-control form-control-sm text-end" style="width: 90px; display: inline-block;">
                                    </td>
                                    <td class="text-end">
                                        <input type="number" name="valor_unitario" value="<?php echo (float)safe_val($item, 'valor_unitario', 0); ?>" step="0.01" class="form-control form-control-sm text-end" style="width: 90px; display: inline-block;">
                                    </td>
                                    <td class="text-end">
                                        <input type="number" name="valor_mao_de_obra" value="<?php echo (float)safe_val($item, 'valor_mao_de_obra', 0); ?>" step="0.01" class="form-control form-control-sm text-end" style="width: 90px; display: inline-block;">
                                    </td>
                                    <td class="text-end">
                                        <input type="number" name="desconto" value="<?php echo (float)safe_val($item, 'desconto', 0); ?>" step="0.01" class="form-control form-control-sm text-end" style="width: 90px; display: inline-block;">
                                    </td>
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
                                        <div class="d-flex gap-1 justify-center">
                                            <button type="submit" class="btn btn-sm btn-success" title="Salvar alterações">💾</button>
                                </form>
                                            <form action="<?php echo BASE_URL; ?>ordens/remover-item" method="POST" onsubmit="return confirm('Remover este item?');" style="display: inline;">
                                                <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>">
                                                <input type="hidden" name="ordem_servico_id" value="<?php echo $ordem['id']; ?>">
                                                <button type="submit" class="btn btn-sm btn-danger" title="Remover item">🗑️</button>
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
    const form = document.getElementById('form-add-item');
    
    const comprarPecaCheckbox = document.getElementById('comprar_peca');
    const divLinkFornecedor = document.getElementById('div_link_fornecedor');

    comprarPecaCheckbox.addEventListener('change', function() {
        divLinkFornecedor.style.display = this.checked ? 'block' : 'none';
    });

    window.registrarPagamentoOS = function(e) {
        e.preventDefault();
        const form = document.getElementById('form-pagamento');
        const msgDiv = document.getElementById('pagamento-msg');
        const formData = new FormData(form);
        fetch('<?php echo BASE_URL; ?>pagamentos/registrar', {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            credentials: 'include',
            body: formData
        })
        .then(r => r.json())
        .then(res => {
            if (res && res.success) {
                msgDiv.innerHTML = '<div class="alert alert-success">Pagamento registrado com sucesso.</div>';
                setTimeout(() => { window.location.reload(); }, 800);
            } else {
                msgDiv.innerHTML = '<div class="alert alert-danger">Erro ao registrar pagamento.</div>';
            }
        })
        .catch(() => {
            msgDiv.innerHTML = '<div class="alert alert-danger">Erro de comunicação.</div>';
        });
        return false;
    }

    window.excluirTransacao = function(tipo, origemId, transacaoId) {
        const msgDiv = document.getElementById('pagamento-msg');
        const fd = new FormData();
        fd.append('transacao_id', transacaoId);
        fetch('<?php echo BASE_URL; ?>pagamentos/deletar', {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            credentials: 'include',
            body: fd
        })
        .then(r => r.json())
        .then(res => {
            if (res && res.success) {
                msgDiv.innerHTML = '<div class="alert alert-success">Transação excluída.</div>';
                setTimeout(() => { window.location.reload(); }, 600);
            } else {
                msgDiv.innerHTML = '<div class="alert alert-danger">Erro ao excluir transação.</div>';
            }
        })
        .catch(() => {
            msgDiv.innerHTML = '<div class="alert alert-danger">Erro de comunicação.</div>';
        });
        return false;
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
            fetch(`<?php echo BASE_URL; ?>ordens/search-items?termo=${encodeURIComponent(termo)}`)
                .then(response => response.json())
                .then(data => {
                    resultsDiv.innerHTML = '';
                    if (data.length > 0) {
                        data.forEach(item => {
                            const div = document.createElement('div');
                            div.className = 'autocomplete-item';
                            div.innerHTML = `
                                <div class="d-flex justify-between">
                                    <span>${item.nome}</span>
                                    <span class="badge ${item.tipo === 'servico' ? 'bg-info' : 'bg-success'}">${item.tipo}</span>
                                </div>
                            `;
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

    document.addEventListener('click', function(e) {
        if (e.target !== searchInput) {
            resultsDiv.style.display = 'none';
        }
    });
});
</script>

<style>
.autocomplete-results {
    position: absolute;
    background: var(--bg-secondary);
    border: 1px solid var(--border-color);
    border-radius: 4px;
    width: 100%;
    max-height: 200px;
    overflow-y: auto;
    z-index: 1000;
    display: none;
}
.autocomplete-item {
    padding: 8px 12px;
    cursor: pointer;
    border-bottom: 1px solid var(--border-color);
}
.autocomplete-item:hover {
    background: var(--bg-tertiary);
}
.form-group { position: relative; }
.cursor-pointer { cursor: pointer; }
.cursor-help { cursor: help; }
.fs-sm { font-size: 0.85rem; }

/* Ajustes para o Histórico de Status */
.table thead th {
    background-color: var(--bg-tertiary);
    color: var(--text-primary);
    border-bottom: 2px solid var(--border-color);
}
.table tbody td {
    color: var(--text-primary);
    border-bottom: 1px solid var(--border-color);
}
</style>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
