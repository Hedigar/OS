<?php
$current_page = 'clientes';
require_once __DIR__ . '/../layout/main.php';

$cliente = $cliente ?? [];
$historicoOS = $historicoOS ?? [];
$equipamentos = $equipamentos ?? [];

$cliente_id = $cliente['id'] ?? 0;
$cliente_nome = urlencode($cliente['nome_completo'] ?? '');
$cliente_documento = urlencode($cliente['documento'] ?? '');
?>

<div class="container">
    <div class="d-flex justify-between align-center mb-4">
        <h1>👤 Cliente: <?php echo htmlspecialchars($cliente['nome_completo'] ?? 'Cliente Não Encontrado'); ?></h1>
        <div class="d-flex gap-2">
            <a href="<?php echo BASE_URL; ?>clientes" class="btn btn-secondary btn-sm">← Voltar</a>
            <a href="<?php echo BASE_URL; ?>clientes/editar?id=<?php echo $cliente_id; ?>" class="btn btn-primary btn-sm">✏️ Editar Cliente</a>
            <a href="<?php echo BASE_URL; ?>atendimentos-externos/form?cliente_id=<?php echo $cliente_id; ?>&cliente_nome=<?php echo $cliente_nome; ?>&cliente_documento=<?php echo $cliente_documento; ?>&cliente_telefone=<?php echo urlencode($cliente['telefone_principal'] ?? ''); ?>&cliente_logradouro=<?php echo urlencode($cliente['endereco_logradouro'] ?? ''); ?>&cliente_numero=<?php echo urlencode($cliente['endereco_numero'] ?? ''); ?>&cliente_bairro=<?php echo urlencode($cliente['endereco_bairro'] ?? ''); ?>&cliente_cidade=<?php echo urlencode($cliente['endereco_cidade'] ?? ''); ?>" class="btn btn-warning btn-sm">🏠 Novo Atendimento Externo</a>
            <a href="<?php echo BASE_URL; ?>ordens/form?cliente_id=<?php echo $cliente_id; ?>&cliente_nome=<?php echo $cliente_nome; ?>&cliente_documento=<?php echo $cliente_documento; ?>" class="btn btn-success btn-sm">➕ Nova OS</a>
            <a href="<?php echo BASE_URL; ?>clientes/print-debitos?id=<?php echo $cliente_id; ?>" target="_blank" class="btn btn-secondary btn-sm">🖨️ Imprimir Débitos</a>
        </div>
    </div>

    <div class="client-detail-grid">
        <!-- COLUNA ESQUERDA: DADOS DO CLIENTE -->
        <div>
            <div class="card mb-4">
                <h3 class="card-title">Informações Pessoais</h3>
                <div class="card-content">
                    <p><strong>Documento:</strong> <?php echo htmlspecialchars($cliente['documento'] ?? 'N/A'); ?></p>
                    <p><strong>E-mail:</strong> <?php echo htmlspecialchars($cliente['email'] ?? 'N/A'); ?></p>
                    <p><strong>Telefone:</strong> 
                        <?php 
                            $telRaw = $cliente['telefone_principal'] ?? '';
                            $tel = preg_replace('/\D+/', '', (string)$telRaw);
                            if ($tel) {
                                $nomeCli = trim((string)($cliente['nome_completo'] ?? ''));
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
                    <p><strong>Cidade:</strong> <?php echo htmlspecialchars($cliente['endereco_cidade'] ?? 'N/A'); ?></p>
                </div>
            </div>

            <div class="card">
                <h3 class="card-title">💻 Equipamentos</h3>
                <?php if (!empty($equipamentos)): ?>
                    <div class="equipment-list">
                        <button class="btn btn-outline-primary btn-block filter-equip" data-id="all">Todos os Equipamentos</button>
                        <?php foreach ($equipamentos as $e): ?>
                            <button class="btn btn-outline-secondary btn-block filter-equip" data-id="<?php echo $e['id']; ?>">
                                <?php echo htmlspecialchars($e['tipo'] . ' ' . $e['marca'] . ' ' . $e['modelo']); ?>
                                <br><small>Serial: <?php echo htmlspecialchars($e['serial'] ?: 'S/N'); ?></small>
                            </button>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p>Nenhum equipamento cadastrado.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- COLUNA DIREITA: HISTÓRICO DE OS -->
        <div>
            <div class="card">
                <h3 class="card-title">📜 Histórico de Ordens de Serviço</h3>
                    📜 Histórico de Ordens de Serviço
                </h3>
                <div class="table-responsive">
                    <table class="table" id="os-table">
                        <thead>
                            <tr>
                                <th># OS</th>
                                <th>Data</th>
                                <th>Status</th>
                                <th>Defeito</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($historicoOS as $os): ?>
                                <tr class="os-row" data-equip-id="<?php echo $os['equipamento_id']; ?>">
                                    <td>#<?php echo $os['id']; ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($os['created_at'])); ?></td>
                                    <td><span class="badge" style="background-color: <?php echo $os['status_cor']; ?>; color: var(--on-primary);"><?php echo $os['status_nome']; ?></span></td>
                                    <td><?php echo htmlspecialchars(substr($os['defeito_relatado'], 0, 50)) . '...'; ?></td>
                                    <td>
                                        <a href="<?php echo BASE_URL; ?>ordens/view?id=<?php echo $os['id']; ?>" class="btn btn-info btn-xs">Ver</a>
                                        <a href="<?php echo BASE_URL; ?>ordens/print-receipt?id=<?php echo $os['id']; ?>" target="_blank" class="btn btn-secondary btn-xs">🖨️</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- HISTÓRICO DE ATENDIMENTOS EXTERNOS -->
            <div class="card mt-4">
                <h3 class="card-title">🏠 Histórico de Atendimentos Externos</h3>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Data Agendada</th>
                                <th>Status</th>
                                <th>Problema</th>
                                <th>Técnico</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($historicoExterno)): ?>
                                <tr>
                                    <td colspan="6" class="text-center">Nenhum atendimento externo registrado.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($historicoExterno as $ae): ?>
                                    <tr>
                                        <td>#<?php echo $ae['id']; ?></td>
                                        <td><?php echo $ae['data_agendada'] ? date('d/m/Y H:i', strtotime($ae['data_agendada'])) : 'N/A'; ?></td>
                                        <td><span class="badge bg-info"><?php echo ucfirst($ae['status']); ?></span></td>
                                        <td><?php echo htmlspecialchars(substr($ae['descricao_problema'], 0, 50)) . '...'; ?></td>
                                        <td><?php echo htmlspecialchars($ae['tecnico_nome'] ?? 'Não atribuído'); ?></td>
                                        <td>
                                            <a href="<?php echo BASE_URL; ?>atendimentos-externos/view?id=<?php echo $ae['id']; ?>" class="btn btn-info btn-xs">Ver/Editar</a>
                                            <a href="<?php echo BASE_URL; ?>atendimentos-externos/print-receipt?id=<?php echo $ae['id']; ?>" target="_blank" class="btn btn-secondary btn-xs">🧾</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- CRM TIMELINE -->
            <div class="card mt-4">
                <div class="d-flex justify-between align-center mb-3">
                    <h3 class="card-title mb-0">🕒 Linha do Tempo CRM (Jornada do Cliente)</h3>
                    <button type="button" class="btn btn-primary btn-sm" onclick="document.getElementById('modalCRM').style.display='block'">+ Registrar Interação</button>
                </div>
                
                <div class="timeline-container" style="max-height: 600px; overflow-y: auto; padding-right: 10px;">
                    <?php if (empty($timeline)): ?>
                        <p class="text-muted text-center">Nenhuma interação registrada na jornada deste cliente.</p>
                    <?php else: ?>
                        <?php foreach ($timeline as $item): ?>
                            <?php 
                                $bg_color = '#3498db';
                                $icon = '💬';
                                $border_color = '#3498db';

                                switch($item['tipo_registro']) {
                                    case 'os':
                                        $bg_color = '#2ecc71';
                                        $icon = '🛠️';
                                        $border_color = '#2ecc71';
                                        break;
                                    case 'atendimento_externo':
                                        $bg_color = '#f1c40f';
                                        $icon = '🏠';
                                        $border_color = '#f1c40f';
                                        break;
                                    case 'log':
                                        $bg_color = '#95a5a6';
                                        $icon = '⚙️';
                                        $border_color = '#95a5a6';
                                        break;
                                    case 'crm':
                                        if($item['tipo'] === 'pos_venda') {
                                            $bg_color = '#e67e22';
                                            $icon = '⭐';
                                            $border_color = '#e67e22';
                                        } elseif($item['tipo'] === 'campanha') {
                                            $bg_color = '#9b59b6';
                                            $icon = '📢';
                                            $border_color = '#9b59b6';
                                        }
                                        break;
                                }
                            ?>
                            <div class="timeline-item mb-4" style="border-left: 3px solid <?php echo $border_color; ?>; padding-left: 20px; position: relative;">
                                <div style="position: absolute; left: -11px; top: 0; width: 20px; height: 20px; border-radius: 50%; background: <?php echo $bg_color; ?>; color: #fff; display: flex; align-items: center; justify-content: center; font-size: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                    <?php echo $icon; ?>
                                </div>
                                <div class="d-flex justify-between align-center mb-1">
                                    <strong style="color: <?php echo $border_color; ?>; font-size: 0.95rem;">
                                        <?php echo htmlspecialchars($item['tipo']); ?>: <?php echo htmlspecialchars($item['titulo']); ?>
                                    </strong>
                                    <span class="badge bg-light text-muted small" style="font-weight: normal;"><?php echo date('d/m/Y H:i', strtotime($item['created_at'])); ?></span>
                                </div>
                                
                                <div style="background: #fdfdfd; padding: 10px; border-radius: 6px; border: 1px solid #f0f0f0;">
                                    <p class="mb-1" style="font-size: 0.9rem;"><strong>Ocorrência:</strong> <?php echo htmlspecialchars($item['descricao']); ?></p>
                                    
                                    <?php if ($item['detalhe']): ?>
                                        <div class="mt-2 pt-2" style="border-top: 1px dashed #eee;">
                                            <p class="mb-0 text-muted small" style="line-height: 1.4;">
                                                <i class="fas fa-info-circle"></i> <em><?php echo nl2br(htmlspecialchars($item['detalhe'])); ?></em>
                                            </p>
                                        </div>
                                    <?php endif; ?>

                                    <div class="d-flex gap-3 mt-2 small text-muted">
                                        <span><i class="fas fa-user"></i> <?php echo htmlspecialchars($item['usuario_nome']); ?></span>
                                        <?php if ($item['nota']): ?>
                                            <span class="text-warning"><i class="fas fa-star"></i> Nota: <?php echo (int)$item['nota']; ?>/5</span>
                                        <?php endif; ?>
                                        <?php if ($item['canal'] && $item['canal'] !== 'log'): ?>
                                            <span class="text-uppercase"><i class="fas fa-broadcast-tower"></i> <?php echo htmlspecialchars($item['canal']); ?></span>
                                        <?php endif; ?>
                                        <?php if ($item['os_id'] && $item['tipo_registro'] !== 'os'): ?>
                                            <a href="<?php echo BASE_URL; ?>ordens/view?id=<?php echo $item['os_id']; ?>" class="text-primary text-decoration-none">
                                                <i class="fas fa-link"></i> Ver OS #<?php echo $item['os_id']; ?>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL CRM -->
<div id="modalCRM" class="modal" style="display:none; position:fixed; z-index:1000; left:0; top:0; width:100%; height:100%; background:rgba(0,0,0,0.5);">
    <div class="modal-content card" style="background:#fff; margin: 10% auto; padding: 20px; width: 500px; border-radius: 8px;">
        <div class="d-flex justify-between mb-3">
            <h3>Registrar Interação CRM</h3>
            <span style="cursor:pointer; font-size:24px;" onclick="document.getElementById('modalCRM').style.display='none'">&times;</span>
        </div>
        <form action="<?php echo BASE_URL; ?>crm/registrar-interacao" method="POST">
            <input type="hidden" name="cliente_id" value="<?php echo $cliente_id; ?>">
            
            <div class="form-group mb-3">
                <label>Tipo de Interação</label>
                <select name="tipo" class="form-control" required onchange="toggleOSSelect(this.value)">
                    <option value="anotacao_manual">Anotação Manual</option>
                    <option value="pos_venda">Pós-Venda (Follow-up)</option>
                    <option value="campanha">Campanha de Marketing</option>
                    <option value="promocao">Envio de Promoção</option>
                    <option value="retorno">Retorno de Cliente</option>
                </select>
            </div>

            <div id="os-select-container" class="form-group mb-3" style="display:none;">
                <label>Relacionar com OS</label>
                <select name="ordem_servico_id" class="form-control">
                    <option value="">-- Nenhuma --</option>
                    <?php foreach ($historicoOS as $os): ?>
                        <option value="<?php echo $os['id']; ?>">OS #<?php echo $os['id']; ?> (<?php echo date('d/m/Y', strtotime($os['created_at'])); ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group mb-3">
                <label>Canal</label>
                <select name="canal" class="form-control">
                    <option value="whatsapp">WhatsApp</option>
                    <option value="telefone">Telefone</option>
                    <option value="presencial">Presencial</option>
                    <option value="email">E-mail</option>
                </select>
            </div>

            <div class="form-group mb-3">
                <label>Assunto</label>
                <input type="text" name="assunto" class="form-control" placeholder="Ex: Promoção de Limpeza, Follow-up OS #123" required>
            </div>

            <div class="form-group mb-3">
                <label>O que foi falado/feito</label>
                <textarea name="descricao" class="form-control" rows="3" placeholder="Detalhes do que você enviou ou conversou"></textarea>
            </div>

            <div class="form-group mb-3">
                <label>Resposta do Cliente</label>
                <textarea name="resposta_cliente" class="form-control" rows="2" placeholder="O que o cliente respondeu (se houver)"></textarea>
            </div>

            <div class="form-group mb-3" id="nota-container" style="display:none;">
                <label>Nota de Satisfação (1-5)</label>
                <select name="nota_satisfacao" class="form-control">
                    <option value="">Sem nota</option>
                    <option value="5">5 ⭐⭐⭐⭐⭐</option>
                    <option value="4">4 ⭐⭐⭐⭐</option>
                    <option value="3">3 ⭐⭐⭐</option>
                    <option value="2">2 ⭐⭐</option>
                    <option value="1">1 ⭐</option>
                </select>
            </div>

            <div class="d-flex justify-end gap-2">
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('modalCRM').style.display='none'">Cancelar</button>
                <button type="submit" class="btn btn-primary">Salvar Interação</button>
            </div>
        </form>
    </div>
</div>

<script>
function toggleOSSelect(tipo) {
    const osContainer = document.getElementById('os-select-container');
    const notaContainer = document.getElementById('nota-container');
    
    if (tipo === 'pos_venda') {
        osContainer.style.display = 'block';
        notaContainer.style.display = 'block';
    } else {
        osContainer.style.display = 'none';
        notaContainer.style.display = 'none';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // Lógica de filtro por equipamento
    const filterButtons = document.querySelectorAll('.filter-equip');
    const osRows = document.querySelectorAll('.os-row');

    filterButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const equipId = this.dataset.id;
            
            // Atualiza estilo dos botões
            filterButtons.forEach(b => b.classList.replace('btn-primary', 'btn-outline-primary'));
            filterButtons.forEach(b => b.classList.replace('btn-secondary', 'btn-outline-secondary'));
            this.classList.replace('btn-outline-primary', 'btn-primary');
            this.classList.replace('btn-outline-secondary', 'btn-secondary');

            osRows.forEach(row => {
                if (equipId === 'all' || row.dataset.equipId === equipId) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    });

    // Lógica de abertura automática da impressão se for uma nova OS
    const urlParams = new URLSearchParams(window.location.search);
    const newOsId = urlParams.get('new_os_id');
    if (newOsId) {
        window.open(`<?php echo BASE_URL; ?>ordens/print-receipt?id=${newOsId}`, '_blank');
        // Limpa a URL
        urlParams.delete('new_os_id');
        const newUrl = window.location.pathname + (urlParams.toString() ? '?' + urlParams.toString() : '');
        history.replaceState(null, '', newUrl);
    }
});
</script>
<?php require_once __DIR__ . '/../layout/footer.php'; ?>
