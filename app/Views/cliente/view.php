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
                    <p><strong>Telefone:</strong> <?php echo htmlspecialchars($cliente['telefone_principal'] ?? 'N/A'); ?></p>
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
                    🏠 Histórico de Atendimentos Externos
                </h3>
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
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
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
