<?php
$current_page = 'crm';
require_once __DIR__ . '/../layout/main.php';
?>

<div class="container">
    <div class="d-flex justify-between align-center mb-4">
        <h1>🚀 CRM - Gestão e Campanhas</h1>
        <div class="d-flex gap-2">
            <?php if ($campanhaAtiva): ?>
                <span class="badge bg-success d-flex align-center px-3">Campanha Ativa: <?php echo htmlspecialchars($campanhaAtiva['nome']); ?></span>
                <a href="<?php echo BASE_URL; ?>crm" class="btn btn-outline-secondary btn-sm">Sair da Campanha</a>
            <?php endif; ?>
            <a href="<?php echo BASE_URL; ?>pos-venda" class="btn btn-secondary btn-sm">Voltar ao Pós-Venda Simples</a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-7">
            <!-- FILTROS AVANÇADOS -->
            <div class="card mb-4">
                <h3 class="card-title">🔍 Filtros de Segmentação</h3>
                <form action="<?php echo BASE_URL; ?>crm" method="GET" class="row align-end">
                    <div class="col-md-4">
                        <label>Clientes sem vir há mais de (dias):</label>
                        <input type="number" name="dias_min" class="form-control" value="<?php echo $filtros['dias_min'] ?? ''; ?>" placeholder="Ex: 90" <?php echo $campanhaAtiva ? 'readonly' : ''; ?>>
                    </div>
                    <div class="col-md-4">
                        <label>Que já fizeram o serviço/produto:</label>
                        <input type="text" name="termo_servico" class="form-control" value="<?php echo htmlspecialchars($filtros['termo_servico'] ?? ''); ?>" placeholder="Digite ou selecione..." list="listaServicos" <?php echo $campanhaAtiva ? 'readonly' : ''; ?>>
                        <datalist id="listaServicos">
                            <?php if (!empty($servicosExistentes)): ?>
                                <?php foreach ($servicosExistentes as $servico): ?>
                                    <option value="<?php echo htmlspecialchars($servico); ?>">
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </datalist>
                    </div>
                    <div class="col-md-4 d-flex gap-2">
                        <?php if (!$campanhaAtiva): ?>
                            <button type="submit" class="btn btn-primary flex-1">Filtrar</button>
                            <button type="button" class="btn btn-success flex-1" onclick="abrirModalSalvarCampanha()">💾 Salvar</button>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-md-5">
            <!-- CONFIGURAÇÃO MENSAGEM PADRÃO -->
            <div class="card mb-4">
                <h3 class="card-title">⚙️ Mensagens Padrão</h3>
                <form action="<?php echo BASE_URL; ?>crm/salvar-configuracao" method="POST">
                    <div class="mb-3">
                        <label class="small text-muted">Mensagem CRM (Segmentação)</label>
                        <div class="d-flex gap-2 align-end">
                            <input type="text" name="crm_mensagem_padrao" class="form-control" value="<?php echo htmlspecialchars($mensagemPadrao); ?>" placeholder="Mensagem CRM...">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="small text-muted">Mensagem Pós-Venda (WhatsApp)</label>
                        <div class="d-flex gap-2 align-end">
                            <input type="text" name="pos_venda_mensagem_padrao" class="form-control" value="<?php echo htmlspecialchars($posVendaMensagemPadrao); ?>" placeholder="Mensagem Pós-Venda...">
                        </div>
                    </div>
                    <div class="d-flex justify-between align-center">
                        <small class="text-muted">Use <strong>{nome}</strong> e <strong>{os_id}</strong>.</small>
                        <button type="submit" class="btn btn-secondary btn-sm">Atualizar Todas</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <!-- CAMPANHAS ATIVAS -->
            <div class="card mb-4">
                <h3 class="card-title">📁 Campanhas em Aberto</h3>
                <div style="max-height: 180px; overflow-y: auto;">
                    <?php if (empty($campanhasAbertas)): ?>
                        <p class="text-muted small">Nenhuma campanha ativa no momento.</p>
                    <?php else: ?>
                        <div class="row">
                            <?php foreach ($campanhasAbertas as $camp): ?>
                                <div class="col-md-4 mb-2">
                                    <div class="list-group-item d-flex justify-between align-center p-2 border rounded">
                                        <div class="small">
                                            <strong><?php echo htmlspecialchars($camp['nome']); ?></strong><br>
                                            <span class="text-muted">Enviados: <?php echo $camp['total_enviados']; ?></span>
                                        </div>
                                        <div class="d-flex gap-1">
                                            <a href="<?php echo BASE_URL; ?>crm?campanha_id=<?php echo $camp['id']; ?>" class="btn btn-primary btn-xs" title="Retomar">▶️</a>
                                            <form action="<?php echo BASE_URL; ?>crm/finalizar-campanha" method="POST" onsubmit="return confirm('Deseja finalizar esta campanha?')">
                                                <input type="hidden" name="id" value="<?php echo $camp['id']; ?>">
                                                <button type="submit" class="btn btn-danger btn-xs" title="Finalizar">🏁</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- RESULTADOS -->
    <!-- CLIENTES JÁ CONTACTADOS (AGORA NO TOPO!) -->
    <?php if ($campanhaAtiva): ?>
        <div class="card mb-4">
            <div class="d-flex justify-between align-center mb-3">
                <h3 class="card-title mb-0">✅ Clientes Já Contactados (<span id="totalContactados"><?php echo $totalContactados; ?></span>)</h3>
                <button type="button" class="btn btn-sm btn-outline-primary" onclick="toggleContactados()">
                    <span id="toggleLabel">Ocultar Lista</span>
                </button>
            </div>
            
            <div id="tabelaContactadosContainer">
                <div class="table-responsive">
                    <table class="table" id="tabelaContactados">
                        <thead>
                            <tr>
                                <th>Cliente</th>
                                <th>Telefone</th>
                                <th>Data do Envio</th>
                                <th>Resposta</th>
                                <th>Lista Negra</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody id="tbodyContactados">
                            <?php foreach ($clientesContactados as $c): ?>
                                <tr data-interacao-id="<?php echo $c['interacao_id']; ?>" data-cliente-id="<?php echo $c['id']; ?>">
                                    <td><strong><?php echo htmlspecialchars($c['nome_completo']); ?></strong></td>
                                    <td>
                                        <?php 
                                            $tel = preg_replace('/\D+/', '', $c['telefone_principal'] ?? '');
                                            if ($tel):
                                                $wa = "https://wa.me/55{$tel}";
                                                echo '<a href="' . $wa . '" target="_blank">' . htmlspecialchars($c['telefone_principal']) . '</a>';
                                            else:
                                                echo 'N/A';
                                            endif;
                                        ?>
                                    </td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($c['data_envio'])); ?></td>
                                    <td class="resposta-cliente"><?php echo htmlspecialchars($c['resposta_cliente'] ?? '-'); ?></td>
                                    <td class="lista-negra">
                                        <?php if ($c['lista_negra']): ?>
                                            <span class="badge bg-danger">Sim</span>
                                        <?php else: ?>
                                            <span class="badge bg-success">Não</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <button class="btn btn-warning btn-xs btn-editar-interacao"
                                                data-interacao-id="<?php echo $c['interacao_id']; ?>"
                                                data-cliente-id="<?php echo $c['id']; ?>"
                                                data-resposta-cliente='<?php echo json_encode($c['resposta_cliente'] ?? '', JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_HEX_TAG); ?>'
                                                data-lista-negra="<?php echo $c['lista_negra']; ?>"
                                                data-campanha-id="<?php echo $campanhaAtiva['id']; ?>">
                                                ✏️ Editar
                                            </button>
                                            <a href="<?php echo BASE_URL; ?>clientes/view?id=<?php echo $c['id']; ?>" class="btn btn-primary btn-xs">Ver Jornada</a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Paginação Contactados -->
                <div class="d-flex justify-between align-center mt-3" id="paginationContactados">
                    <button class="btn btn-sm btn-outline-secondary" onclick="carregarContactados(crmState.pageContactados - 1)" id="btnPrevContactados" disabled>
                        ← Anterior
                    </button>
                    <span class="text-muted small">
                        Página <span id="pageContactados">1</span> de <span id="totalPagesContactados"><?php echo ceil($totalContactados / 10); ?></span>
                    </span>
                    <button class="btn btn-sm btn-outline-secondary" onclick="carregarContactados(crmState.pageContactados + 1)" id="btnNextContactados" <?php echo $totalContactados <= 10 ? 'disabled' : '' ?>>
                        Próximo →
                    </button>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- CLIENTES SEGMENTADOS (AGORA ABAIXO!) -->
    <div class="card">
        <div class="d-flex justify-between align-center mb-3">
            <h3 class="card-title mb-0">👥 Clientes Segmentados (<span id="totalSegmentados"><?php echo $totalSegmentados; ?></span>)</h3>
            <button class="btn btn-success btn-sm" onclick="abrirModalCampanha()" id="btnCampanha" style="display: <?php echo empty($clientes) ? 'none' : 'block'; ?>">📢 Criar Campanha para esta Lista</button>
        </div>

        <div class="table-responsive">
            <table class="table" id="tabelaCRM">
                <thead>
                    <tr>
                        <th>Cliente</th>
                        <th>Telefone</th>
                        <th>Última Visita</th>
                        <th>Dias sem vir</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody id="tbodySegmentados">
                    <?php if (empty($clientes)): ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted">Nenhum cliente encontrado com estes filtros.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($clientes as $c): ?>
                            <tr class="cliente-row" 
                                data-id="<?php echo $c['id']; ?>" 
                                data-nome="<?php echo htmlspecialchars($c['nome_completo']); ?>" 
                                data-tel="<?php echo preg_replace('/\D+/', '', $c['telefone_principal'] ?? ''); ?>">
                                <td><strong><?php echo htmlspecialchars($c['nome_completo']); ?></strong></td>
                                <td>
                                    <?php 
                                        $tel = preg_replace('/\D+/', '', $c['telefone_principal'] ?? '');
                                        if ($tel):
                                            $wa = "https://wa.me/55{$tel}";
                                            echo '<a href="' . $wa . '" target="_blank">' . htmlspecialchars($c['telefone_principal']) . '</a>';
                                        else:
                                            echo 'N/A';
                                        endif;
                                    ?>
                                </td>
                                <td><?php echo $c['ultima_visita'] ? date('d/m/Y', strtotime($c['ultima_visita'])) : 'Nunca'; ?></td>
                                <td>
                                    <?php if ($c['dias_sem_vir'] !== null): ?>
                                        <span class="badge <?php echo $c['dias_sem_vir'] > 180 ? 'bg-danger' : ($c['dias_sem_vir'] > 90 ? 'bg-warning' : 'bg-info'); ?>">
                                            <?php echo (int)$c['dias_sem_vir']; ?> dias
                                        </span>
                                    <?php else: ?>
                                        --
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <a href="<?php echo BASE_URL; ?>clientes/view?id=<?php echo $c['id']; ?>" class="btn btn-primary btn-xs">Ver Jornada</a>
                                        <?php if ($tel): ?>
                                            <button class="btn btn-success btn-xs" onclick="abrirMensagemCRM(<?php echo $c['id']; ?>, <?php echo json_encode($c['nome_completo']); ?>, '<?php echo $tel; ?>')">WhatsApp</button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Paginação Segmentados -->
        <div class="d-flex justify-between align-center mt-3" id="paginationSegmentados">
            <button class="btn btn-sm btn-outline-secondary" onclick="carregarSegmentados(crmState.pageSegmentados - 1)" id="btnPrevSegmentados" disabled>
                ← Anterior
            </button>
            <span class="text-muted small">
                Página <span id="pageSegmentados">1</span> de <span id="totalPagesSegmentados"><?php echo ceil($totalSegmentados / 10); ?></span>
            </span>
            <button class="btn btn-sm btn-outline-secondary" onclick="carregarSegmentados(crmState.pageSegmentados + 1)" id="btnNextSegmentados" <?php echo $totalSegmentados <= 10 ? 'disabled' : '' ?>>
                Próximo →
            </button>
        </div>
    </div>
</div>

<!-- MODAL CAMPANHA EM LOTE (ASSISTENTE) -->
<div id="modalCampanhaLote" class="modal" style="display:none; position:fixed; z-index:1000; left:0; top:0; width:100%; height:100%; background:rgba(0,0,0,0.5);">
    <div class="modal-content card" style="background:#fff; margin: 5% auto; padding: 20px; width: 600px; border-radius: 8px;">
        <div class="d-flex justify-between mb-3">
            <h3>📢 Assistente de Campanha (<?php echo count($clientes); ?> clientes)</h3>
            <span style="cursor:pointer; font-size:24px;" onclick="document.getElementById('modalCampanhaLote').style.display='none'">&times;</span>
        </div>
        
        <div id="step1">
            <div class="form-group mb-3">
                <label>Assunto da Campanha</label>
                <input type="text" id="lote_assunto" class="form-control" placeholder="Ex: Promoção de Primavera" value="<?php echo $campanhaAtiva ? htmlspecialchars($campanhaAtiva['nome']) : ''; ?>">
            </div>
            <div class="form-group mb-3">
                <label>Mensagem Padrão (Use {nome} para o primeiro nome)</label>
                <textarea id="lote_mensagem" class="form-control" rows="5"><?php echo $campanhaAtiva ? htmlspecialchars($campanhaAtiva['mensagem_padrao']) : htmlspecialchars($mensagemPadrao); ?></textarea>
            </div>
            <div class="alert alert-info small">
                O assistente abrirá o WhatsApp de cada cliente um por um. 
                Após enviar, clique em "Próximo" para registrar e avançar.
            </div>
            <button class="btn btn-primary btn-block" onclick="iniciarEnvioLote()">Iniciar Envio Sequencial</button>
        </div>

        <div id="step2" style="display:none;">
            <div class="text-center mb-4">
                <h4 id="lote_status_progresso">Enviando 1 de <?php echo count($clientes); ?></h4>
                <div class="progress mb-2" style="height: 10px; background: #eee; border-radius: 5px;">
                    <div id="lote_barra_progresso" style="width: 0%; height: 100%; background: var(--primary); border-radius: 5px;"></div>
                </div>
            </div>

            <div class="card p-3 mb-3" style="background: #f9f9f9;">
                <p><strong>Cliente Atual:</strong> <span id="lote_cliente_nome">---</span></p>
                <p><strong>Telefone:</strong> <span id="lote_cliente_tel">---</span></p>
                <hr>
                <p class="small text-muted">Mensagem a ser enviada:</p>
                <div id="lote_preview_msg" style="white-space: pre-wrap; padding: 10px; background: #fff; border: 1px solid #ddd; border-radius: 4px;"></div>
            </div>

            <div class="d-flex gap-2">
                <button class="btn btn-secondary flex-1" onclick="pularCliente()">Pular</button>
                <button class="btn btn-success flex-2" onclick="enviarWAAtual()">Abrir WhatsApp e Próximo</button>
            </div>
            
            <button class="btn btn-link btn-block mt-3 text-danger small" onclick="document.getElementById('modalCampanhaLote').style.display='none'">Cancelar Campanha</button>
        </div>

        <div id="step3" style="display:none;" class="text-center">
            <h2 class="mb-3">🎉 Campanha Concluída!</h2>
            <p>Todas as interações foram registradas no histórico dos clientes.</p>
            <button class="btn btn-primary" onclick="location.reload()">Fechar e Atualizar</button>
        </div>
    </div>
</div>

<!-- MODAL SALVAR CAMPANHA -->
<div id="modalSalvarCampanha" class="modal" style="display:none; position:fixed; z-index:1000; left:0; top:0; width:100%; height:100%; background:rgba(0,0,0,0.5);">
    <div class="modal-content card" style="background:#fff; margin: 10% auto; padding: 20px; width: 500px; border-radius: 8px;">
        <div class="d-flex justify-between mb-3">
            <h3>💾 Salvar Filtro como Campanha</h3>
            <span style="cursor:pointer; font-size:24px;" onclick="document.getElementById('modalSalvarCampanha').style.display='none'">&times;</span>
        </div>
        <form action="<?php echo BASE_URL; ?>crm/salvar-campanha" method="POST">
            <input type="hidden" name="dias_min" value="<?php echo $filtros['dias_min'] ?? ''; ?>">
            <input type="hidden" name="termo_servico" value="<?php echo htmlspecialchars($filtros['termo_servico'] ?? ''); ?>">
            
            <div class="form-group mb-3">
                <label>Nome da Campanha</label>
                <input type="text" name="nome" class="form-control" placeholder="Ex: Campanha de SSD - Outubro" required>
            </div>
            <div class="form-group mb-3">
                <label>Mensagem Padrão Sugerida</label>
                <textarea name="mensagem_padrao" class="form-control" rows="4"><?php echo htmlspecialchars($mensagemPadrao); ?></textarea>
            </div>
            <div class="d-flex justify-end gap-2">
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('modalSalvarCampanha').style.display='none'">Cancelar</button>
                <button type="submit" class="btn btn-success">Salvar e Iniciar</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL MENSAGEM CRM RÁPIDA -->
<div id="modalMsgCRM" class="modal" style="display:none; position:fixed; z-index:1000; left:0; top:0; width:100%; height:100%; background:rgba(0,0,0,0.5);">
    <div class="modal-content card" style="background:#fff; margin:10% auto; padding:20px; width:500px; border-radius:8px;">
        <div class="d-flex justify-between mb-3">
            <h3>Enviar Mensagem / Promoção</h3>
            <span style="cursor:pointer; font-size:24px;" onclick="document.getElementById('modalMsgCRM').style.display='none'">&times;</span>
        </div>
        <form action="<?php echo BASE_URL; ?>crm/registrar-interacao" method="POST" id="formMsgCRM">
            <input type="hidden" name="cliente_id" id="crm_cliente_id">
            <input type="hidden" name="tipo" value="campanha">
            <input type="hidden" name="canal" value="whatsapp">
            <input type="hidden" name="campanha_id" id="crm_campanha_id" value="<?php echo $campanhaAtiva['id'] ?? ''; ?>">
            
            <div class="form-group mb-3">
                <label>Assunto / Campanha</label>
                <input type="text" name="assunto" class="form-control" placeholder="Ex: Promoção de Limpeza de Notebook" required id="crm_assunto">
            </div>

            <div class="form-group mb-3">
                <label>Mensagem para o WhatsApp</label>
                <textarea name="descricao" id="crm_mensagem" class="form-control" rows="4"></textarea>
                <small class="text-muted">Dica: A mensagem será aberta no WhatsApp para você enviar.</small>
            </div>

            <div class="d-flex justify-end gap-2">
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('modalMsgCRM').style.display='none'">Cancelar</button>
                <button type="button" class="btn btn-success" onclick="enviarWAeSalvar()">Enviar e Registrar</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL EDITAR INTERAÇÃO E CLIENTE -->
<div id="modalEditar" class="modal" style="display:none; position:fixed; z-index:1000; left:0; top:0; width:100%; height:100%; background:rgba(0,0,0,0.5);">
    <div class="modal-content card" style="background:#fff; margin:10% auto; padding:20px; width:500px; border-radius:8px;">
        <div class="d-flex justify-between mb-3">
            <h3>Editar Interação e Cliente</h3>
            <span style="cursor:pointer; font-size:24px;" onclick="document.getElementById('modalEditar').style.display='none'">&times;</span>
        </div>
        <form action="<?php echo BASE_URL; ?>crm/atualizar-interacao-cliente" method="POST">
            <input type="hidden" name="interacao_id" id="editar_interacao_id">
            <input type="hidden" name="cliente_id" id="editar_cliente_id">
            <input type="hidden" name="campanha_id" id="editar_campanha_id">
            
            <div class="form-group mb-3">
                <label>Resposta do Cliente</label>
                <textarea name="resposta_cliente" id="editar_resposta_cliente" class="form-control" rows="4" placeholder="O que o cliente respondeu..."></textarea>
            </div>

            <div class="form-group mb-3">
                <label class="checkbox-label">
                    <input type="checkbox" id="editar_lista_negra" name="lista_negra" value="1">
                    🚫 Colocar na Lista Negra (não receberá mais campanhas)
                </label>
            </div>

            <div class="d-flex justify-end gap-2">
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('modalEditar').style.display='none'">Cancelar</button>
                <button type="submit" class="btn btn-primary">Salvar Alterações</button>
            </div>
        </form>
    </div>
</div>

<script>
// Estado global do CRM
const crmState = {
    pageSegmentados: 1,
    pageContactados: 1,
    perPage: 10,
    campanhaId: <?php echo $campanhaAtiva['id'] ?? 'null'; ?>,
    filtros: {
        dias_min: <?php echo $filtros['dias_min'] ?? 'null'; ?>,
        termo_servico: <?php echo isset($filtros['termo_servico']) ? json_encode($filtros['termo_servico']) : 'null'; ?>
    }
};

let listaClientesLote = [];
let indexAtualLote = 0;

// Função para carregar clientes segmentados via AJAX
async function carregarSegmentados(page) {
    if (page < 1) return;
    
    crmState.pageSegmentados = page;
    
    const params = new URLSearchParams({
        page: page,
        per_page: crmState.perPage
    });
    
    if (crmState.campanhaId) params.append('campanha_id', crmState.campanhaId);
    if (crmState.filtros.dias_min) params.append('dias_min', crmState.filtros.dias_min);
    if (crmState.filtros.termo_servico) params.append('termo_servico', crmState.filtros.termo_servico);
    
    try {
        const response = await fetch('<?php echo BASE_URL; ?>crm/listar-segmentados-ajax?' + params);
        const data = await response.json();
        
        if (data.success) {
            // Atualiza o tbody
            document.getElementById('tbodySegmentados').innerHTML = data.html;
            
            // Atualiza os controles de paginação
            document.getElementById('pageSegmentados').textContent = data.page;
            document.getElementById('totalPagesSegmentados').textContent = data.total_pages;
            document.getElementById('totalSegmentados').textContent = data.total;
            
            // Habilita/desabilita botões
            document.getElementById('btnPrevSegmentados').disabled = page <= 1;
            document.getElementById('btnNextSegmentados').disabled = page >= data.total_pages;
            
            // Mostra/oculta botão de campanha
            document.getElementById('btnCampanha').style.display = data.total > 0 ? 'block' : 'none';
        }
    } catch (error) {
        console.error('Erro ao carregar clientes segmentados:', error);
    }
}

// Função para carregar clientes contactados via AJAX
async function carregarContactados(page) {
    if (page < 1 || !crmState.campanhaId) return;
    
    crmState.pageContactados = page;
    
    const params = new URLSearchParams({
        page: page,
        per_page: crmState.perPage,
        campanha_id: crmState.campanhaId
    });
    
    try {
        const response = await fetch('<?php echo BASE_URL; ?>crm/listar-contactados-ajax?' + params);
        const data = await response.json();
        
        if (data.success) {
            // Atualiza o tbody
            document.getElementById('tbodyContactados').innerHTML = data.html;
            
            // Atualiza os controles de paginação
            document.getElementById('pageContactados').textContent = data.page;
            document.getElementById('totalPagesContactados').textContent = data.total_pages;
            document.getElementById('totalContactados').textContent = data.total;
            
            // Habilita/desabilita botões
            document.getElementById('btnPrevContactados').disabled = page <= 1;
            document.getElementById('btnNextContactados').disabled = page >= data.total_pages;
        }
    } catch (error) {
        console.error('Erro ao carregar clientes contactados:', error);
    }
}

function abrirModalCampanha() {
    listaClientesLote = [];
    document.querySelectorAll('.cliente-row').forEach(row => {
        if (row.dataset.tel) {
            listaClientesLote.push({
                id: row.dataset.id,
                nome: row.dataset.nome,
                tel: row.dataset.tel
            });
        }
    });

    if (listaClientesLote.length === 0) {
        alert('Nenhum cliente com telefone nesta lista.');
        return;
    }

    indexAtualLote = 0;
    document.getElementById('step1').style.display = 'block';
    document.getElementById('step2').style.display = 'none';
    document.getElementById('step3').style.display = 'none';
    document.getElementById('modalCampanhaLote').style.display = 'block';
}

function abrirModalSalvarCampanha() {
    document.getElementById('modalSalvarCampanha').style.display = 'block';
}

function iniciarEnvioLote() {
    const assunto = document.getElementById('lote_assunto').value;
    if (!assunto) {
        alert('Por favor, informe o assunto da campanha.');
        return;
    }
    document.getElementById('step1').style.display = 'none';
    document.getElementById('step2').style.display = 'block';
    atualizarUIEnvioLote();
}

function atualizarUIEnvioLote() {
    const total = listaClientesLote.length;
    const atual = indexAtualLote + 1;
    const cliente = listaClientesLote[indexAtualLote];
    const porcentagem = (indexAtualLote / total) * 100;

    document.getElementById('lote_status_progresso').innerText = `Enviando ${atual} de ${total}`;
    document.getElementById('lote_barra_progresso').style.width = `${porcentagem}%`;
    document.getElementById('lote_cliente_nome').innerText = cliente.nome;
    document.getElementById('lote_cliente_tel').innerText = cliente.tel;

    const template = document.getElementById('lote_mensagem').value;
    const primeiroNome = cliente.nome.split(' ')[0];
    const msgFinal = template.replace('{nome}', primeiroNome);
    document.getElementById('lote_preview_msg').innerText = msgFinal;
}

function pularCliente() {
    avancarLote();
}

async function enviarWAAtual() {
    const cliente = listaClientesLote[indexAtualLote];
    const msg = encodeURIComponent(document.getElementById('lote_preview_msg').innerText);
    const assunto = document.getElementById('lote_assunto').value;
    const campanhaId = crmState.campanhaId;

    // Registrar no banco via AJAX
    const formData = new FormData();
    formData.append('cliente_id', cliente.id);
    formData.append('tipo', 'campanha');
    formData.append('canal', 'whatsapp');
    formData.append('assunto', assunto);
    formData.append('descricao', document.getElementById('lote_preview_msg').innerText);
    if (campanhaId) formData.append('campanha_id', campanhaId);
    formData.append('ajax', '1');

    try {
        await fetch('<?php echo BASE_URL; ?>crm/registrar-interacao', {
            method: 'POST',
            body: formData
        });
        
        // Abre WhatsApp
        window.open(`https://wa.me/55${cliente.tel}?text=${msg}`, '_blank');
        
        // Atualiza as listas após enviar
        if (crmState.campanhaId) {
            await carregarSegmentados(1);
            await carregarContactados(1);
        }
        
        avancarLote();
    } catch (error) {
        console.error('Erro ao registrar interação:', error);
        alert('Erro ao registrar interação. Tente novamente.');
    }
}

function avancarLote() {
    indexAtualLote++;
    if (indexAtualLote >= listaClientesLote.length) {
        document.getElementById('step2').style.display = 'none';
        document.getElementById('step3').style.display = 'block';
    } else {
        atualizarUIEnvioLote();
    }
}

function abrirMensagemCRM(id, nome, tel) {
    document.getElementById('crm_cliente_id').value = id;
    const primeiroNome = nome.split(' ')[0];
    const template = <?php echo json_encode($mensagemPadrao); ?>;
    const msg = template.replace('{nome}', primeiroNome);
    document.getElementById('crm_mensagem').value = msg;
    document.getElementById('crm_assunto').value = 'Promoção Reativação';
    document.getElementById('modalMsgCRM').style.display = 'block';
    window.currentTel = tel;
}

async function enviarWAeSalvar() {
    const msg = encodeURIComponent(document.getElementById('crm_mensagem').value);
    const tel = window.currentTel;
    const form = document.getElementById('formMsgCRM');
    
    // Registrar via AJAX primeiro
    const formData = new FormData(form);
    formData.append('ajax', '1');
    
    try {
        await fetch('<?php echo BASE_URL; ?>crm/registrar-interacao', {
            method: 'POST',
            body: formData
        });
        
        // Abre o WhatsApp
        window.open(`https://wa.me/55${tel}?text=${msg}`, '_blank');
        
        // Fecha o modal
        document.getElementById('modalMsgCRM').style.display = 'none';
        
        // Atualiza a lista se houver campanha
        if (crmState.campanhaId) {
            await carregarSegmentados(1);
            await carregarContactados(1);
        }
    } catch (error) {
        console.error('Erro ao registrar interação:', error);
        // Fallback para submit normal
        form.submit();
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // Abertura automática do assistente se estiver retomando uma campanha
    <?php if ($campanhaAtiva): ?>
        abrirModalCampanha();
    <?php endif; ?>
    
    // Event listener para botões de editar
    document.addEventListener('click', function(e) {
        if (e.target.closest('.btn-editar-interacao')) {
            const btn = e.target.closest('.btn-editar-interacao');
            const interacaoId = btn.dataset.interacaoId;
            const clienteId = btn.dataset.clienteId;
            const respostaCliente = JSON.parse(btn.dataset.respostaCliente || '""');
            const listaNegra = btn.dataset.listaNegra;
            const campanhaId = btn.dataset.campanhaId;
            abrirModalEditar(interacaoId, clienteId, respostaCliente, listaNegra, campanhaId);
        }
    });
    
    // Modifica o formulário de edição para usar AJAX
    const formEditar = document.querySelector('#modalEditar form');
    if (formEditar) {
        formEditar.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            formData.append('ajax', '1');
            const interacaoId = formData.get('interacao_id');
            
            try {
                const response = await fetch('<?php echo BASE_URL; ?>crm/atualizar-interacao-cliente', {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();
                
                if (data.success) {
                    // Atualiza a linha na tabela in-place
                    const row = document.querySelector(`tr[data-interacao-id="${interacaoId}"]`);
                    if (row) {
                        // Atualiza resposta do cliente
                        const respostaCell = row.querySelector('.resposta-cliente');
                        if (respostaCell) {
                            respostaCell.textContent = data.resposta_cliente || '-';
                        }
                        
                        // Atualiza lista negra
                        const listaNegraCell = row.querySelector('.lista-negra');
                        if (listaNegraCell) {
                            if (data.lista_negra) {
                                listaNegraCell.innerHTML = '<span class="badge bg-danger">Sim</span>';
                            } else {
                                listaNegraCell.innerHTML = '<span class="badge bg-success">Não</span>';
                            }
                        }
                        
                        // Atualiza o botão editar
                        const btnEditar = row.querySelector('.btn-editar-interacao');
                        if (btnEditar) {
                            btnEditar.dataset.respostaCliente = JSON.stringify(data.resposta_cliente || '');
                            btnEditar.dataset.listaNegra = data.lista_negra;
                        }
                    }
                    
                    // Fecha o modal
                    document.getElementById('modalEditar').style.display = 'none';
                }
            } catch (error) {
                console.error('Erro ao atualizar interação:', error);
                alert('Erro ao salvar alterações. Tente novamente.');
            }
        });
    }
});

function abrirModalEditar(interacaoId, clienteId, respostaCliente, listaNegra, campanhaId) {
    document.getElementById('editar_interacao_id').value = interacaoId;
    document.getElementById('editar_cliente_id').value = clienteId;
    document.getElementById('editar_resposta_cliente').value = respostaCliente;
    document.getElementById('editar_campanha_id').value = campanhaId;
    document.getElementById('editar_lista_negra').checked = listaNegra == 1;
    document.getElementById('modalEditar').style.display = 'block';
}

function toggleContactados() {
    const container = document.getElementById('tabelaContactadosContainer');
    const label = document.getElementById('toggleLabel');
    if (container.style.display === 'none') {
        container.style.display = 'block';
        label.textContent = 'Ocultar Lista';
    } else {
        container.style.display = 'none';
        label.textContent = 'Exibir Lista';
    }
}
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
