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
        <div class="col-md-8">
            <!-- FILTROS AVANÇADOS -->
            <div class="card mb-4">
                <h3 class="card-title">🔍 Filtros de Segmentação</h3>
                <form action="<?php echo BASE_URL; ?>crm" method="GET" class="row align-end">
                    <div class="col-md-3">
                        <label>Clientes sem vir há mais de (dias):</label>
                        <input type="number" name="dias_min" class="form-control" value="<?php echo $filtros['dias_min'] ?? ''; ?>" placeholder="Ex: 90" <?php echo $campanhaAtiva ? 'readonly' : ''; ?>>
                    </div>
                    <div class="col-md-5">
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
                            <button type="button" class="btn btn-success flex-1" onclick="abrirModalSalvarCampanha()">💾 Salvar como Campanha</button>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-md-4">
            <!-- CAMPANHAS ATIVAS -->
            <div class="card mb-4">
                <h3 class="card-title">📁 Campanhas em Aberto</h3>
                <div style="max-height: 180px; overflow-y: auto;">
                    <?php if (empty($campanhasAbertas)): ?>
                        <p class="text-muted small">Nenhuma campanha ativa no momento.</p>
                    <?php else: ?>
                        <div class="list-group">
                            <?php foreach ($campanhasAbertas as $camp): ?>
                                <div class="list-group-item d-flex justify-between align-center p-2">
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
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- RESULTADOS -->
    <div class="card">
        <div class="d-flex justify-between align-center mb-3">
            <h3 class="card-title mb-0">👥 Clientes Segmentados (<?php echo count($clientes); ?>)</h3>
            <?php if (!empty($clientes)): ?>
                <button class="btn btn-success btn-sm" onclick="abrirModalCampanha()">📢 Criar Campanha para esta Lista</button>
            <?php endif; ?>
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
                <tbody>
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
                                            <button class="btn btn-success btn-xs" onclick="abrirMensagemCRM(<?php echo $c['id']; ?>, '<?php echo addslashes($c['nome_completo']); ?>', '<?php echo $tel; ?>')">WhatsApp</button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
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
                <textarea id="lote_mensagem" class="form-control" rows="5"><?php echo $campanhaAtiva ? htmlspecialchars($campanhaAtiva['mensagem_padrao']) : 'Olá {nome}! Notamos que faz um tempo que não nos visita. Temos uma oferta especial para você hoje!'; ?></textarea>
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
                <textarea name="mensagem_padrao" class="form-control" rows="4">Olá {nome}! Notamos que você fez um serviço conosco e gostaríamos de oferecer...</textarea>
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
    <div class="modal-content card" style="background:#fff; margin: 10% auto; padding: 20px; width: 500px; border-radius: 8px;">
        <div class="d-flex justify-between mb-3">
            <h3>Enviar Mensagem / Promoção</h3>
            <span style="cursor:pointer; font-size:24px;" onclick="document.getElementById('modalMsgCRM').style.display='none'">&times;</span>
        </div>
        <form action="<?php echo BASE_URL; ?>crm/registrar-interacao" method="POST" id="formMsgCRM">
            <input type="hidden" name="cliente_id" id="crm_cliente_id">
            <input type="hidden" name="tipo" value="campanha">
            <input type="hidden" name="canal" value="whatsapp">
            
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

<script>
let listaClientesLote = [];
let indexAtualLote = 0;

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

function enviarWAAtual() {
    const cliente = listaClientesLote[indexAtualLote];
    const msg = encodeURIComponent(document.getElementById('lote_preview_msg').innerText);
    const assunto = document.getElementById('lote_assunto').value;
    const campanhaId = '<?php echo $campanhaAtiva['id'] ?? ''; ?>';

    // Registrar no banco via AJAX
    const formData = new FormData();
    formData.append('cliente_id', cliente.id);
    formData.append('tipo', 'campanha');
    formData.append('canal', 'whatsapp');
    formData.append('assunto', assunto);
    formData.append('descricao', document.getElementById('lote_preview_msg').innerText);
    if (campanhaId) formData.append('campanha_id', campanhaId);
    formData.append('ajax', '1');

    fetch('<?php echo BASE_URL; ?>crm/registrar-interacao', {
        method: 'POST',
        body: formData
    }).then(() => {
        // Abre WhatsApp
        window.open(`https://wa.me/55${cliente.tel}?text=${msg}`, '_blank');
        avancarLote();
    });
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
    const msg = `Olá ${primeiroNome}! Notamos que faz um tempo que não revisamos seu equipamento. Temos uma promoção especial de limpeza preventiva esta semana. Gostaria de agendar?`;
    document.getElementById('crm_mensagem').value = msg;
    document.getElementById('crm_assunto').value = 'Promoção Reativação';
    document.getElementById('modalMsgCRM').style.display = 'block';
    window.currentTel = tel;
}

function enviarWAeSalvar() {
    const msg = encodeURIComponent(document.getElementById('crm_mensagem').value);
    const tel = window.currentTel;
    
    // Abre o WhatsApp
    window.open(`https://wa.me/55${tel}?text=${msg}`, '_blank');
    
    // Submete o formulário para registrar no banco
    document.getElementById('formMsgCRM').submit();
}

document.addEventListener('DOMContentLoaded', function() {
    // Abertura automática do assistente se estiver retomando uma campanha
    <?php if ($campanhaAtiva): ?>
        abrirModalCampanha();
    <?php endif; ?>
});
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
