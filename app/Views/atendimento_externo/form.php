<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><?php echo $title; ?></h1>
        <a href="<?php echo BASE_URL; ?>atendimentos-externos" class="btn btn-secondary">Voltar</a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="<?php echo BASE_URL; ?>atendimentos-externos/<?php echo isset($atendimento['id']) ? 'atualizar' : 'salvar'; ?>" method="POST">
                <?php if (isset($atendimento['id'])): ?>
                    <input type="hidden" name="id" value="<?php echo $atendimento['id']; ?>">
                <?php endif; ?>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="cliente_search" class="form-label">Cliente</label>
                        <div style="display:flex; gap:8px; align-items:center;">
                            <input
                                type="text"
                                id="cliente_search"
                                placeholder="Digite o nome, CPF ou CNPJ e aperte Enter ou clique em Buscar..."
                                value="<?php echo isset($cliente) ? htmlspecialchars($cliente['nome_completo']) : (isset($atendimento['cliente_nome']) ? htmlspecialchars($atendimento['cliente_nome']) : ''); ?>"
                                <?php echo isset($atendimento['id']) ? 'readonly' : ''; ?>
                                autocomplete="off"
                                class="form-control"
                                style="flex:1"
                            >
                            <button type="button" id="btn_buscar_cliente" class="btn btn-primary" <?php echo isset($atendimento['id']) ? 'disabled' : ''; ?>>🔍 Buscar</button>
                        </div>
                        <input type="hidden" name="cliente_id" id="cliente_id" value="<?php echo isset($atendimento['cliente_id']) ? $atendimento['cliente_id'] : (isset($cliente) ? $cliente['id'] : ''); ?>" required>
                        <div id="search_results" class="autocomplete-results" style="display:none;margin-top:6px;"></div>
                        <div id="cliente_info" style="display: <?php echo isset($atendimento['cliente_id']) || isset($cliente) ? 'block' : 'none'; ?>; margin-top:8px; padding:8px; border:1px solid #ddd; border-radius:6px;">
                            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:4px;">
                                <strong>Cliente Selecionado:</strong>
                                <button type="button" id="remove_client_btn" class="btn btn-danger btn-sm" onclick="removeClient()">✕ Alterar</button>
                            </div>
                            <p style="margin:0"><strong>Nome:</strong> <span id="info_nome"><?php echo isset($atendimento['cliente_nome']) ? htmlspecialchars($atendimento['cliente_nome']) : (isset($cliente) ? htmlspecialchars($cliente['nome_completo']) : ''); ?></span></p>
                            <p style="margin:0"><strong>Documento:</strong> <span id="info_documento"><?php echo isset($atendimento['cliente_documento']) ? htmlspecialchars($atendimento['cliente_documento']) : (isset($cliente) ? htmlspecialchars($cliente['documento']) : ''); ?></span></p>
                            <p style="margin:0"><strong>Telefone:</strong> <span id="info_telefone"><?php echo isset($atendimento['cliente_telefone']) ? htmlspecialchars($atendimento['cliente_telefone']) : (isset($cliente) ? htmlspecialchars($cliente['telefone_principal'] ?? '') : ''); ?></span></p>
                        </div>
                        <small class="text-muted">Você pode buscar por nome, CPF ou CNPJ.</small>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="usuario_id" class="form-label">Técnico Responsável</label>
                        <select name="usuario_id" id="usuario_id" class="form-select">
                            <option value="">Selecione um técnico</option>
                            <?php foreach ($tecnicos as $tecnico): ?>
                                <option value="<?php echo $tecnico['id']; ?>" <?php echo (isset($atendimento['usuario_id']) && $atendimento['usuario_id'] == $tecnico['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($tecnico['nome']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label for="endereco_visita" class="form-label mb-0">Endereço da Visita</label>
                            <div id="endereco_options" style="display: <?php echo (isset($atendimento['cliente_id']) || isset($cliente)) ? 'block' : 'none'; ?>;">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="tipo_endereco" id="endereco_padrao" value="padrao" checked>
                                    <label class="form-check-label" for="endereco_padrao">Endereço do Cliente</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="tipo_endereco" id="endereco_outro" value="outro">
                                    <label class="form-check-label" for="endereco_outro">Outro Endereço</label>
                                </div>
                            </div>
                        </div>
                        <input type="text" name="endereco_visita" id="endereco_visita" class="form-control" value="<?php 
                            if (isset($atendimento['endereco_visita'])) {
                                echo htmlspecialchars($atendimento['endereco_visita']);
                            } elseif (isset($cliente)) {
                                $end = trim(($cliente['endereco_logradouro'] ?? '') . ' ' . ($cliente['endereco_numero'] ?? '') . ' ' . ($cliente['endereco_bairro'] ?? '') . ' ' . ($cliente['endereco_cidade'] ?? ''));
                                echo htmlspecialchars($end);
                            } else {
                                echo '';
                            }
                        ?>" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="data_agendada" class="form-label">Data/Hora Agendada</label>
                        <input type="datetime-local" name="data_agendada" id="data_agendada" class="form-control" value="<?php echo isset($atendimento['data_agendada']) ? date('Y-m-d\TH:i', strtotime($atendimento['data_agendada'])) : ''; ?>">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select name="status" id="status" class="form-select">
                            <?php 
                            $statuses = ['pendente', 'agendado', 'em_deslocamento', 'concluido', 'cancelado'];
                            foreach ($statuses as $s): ?>
                                <option value="<?php echo $s; ?>" <?php echo (isset($atendimento['status']) && $atendimento['status'] == $s) ? 'selected' : ''; ?>>
                                    <?php echo ucfirst($s); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="valor_deslocamento" class="form-label">Valor Deslocamento (R$)</label>
                        <input type="number" step="0.01" name="valor_deslocamento" id="valor_deslocamento" class="form-control" value="<?php echo $atendimento['valor_deslocamento'] ?? '0.00'; ?>">
                    </div>
                </div>

                <div class="mb-3">
                    <label for="descricao_problema" class="form-label">Descrição do Problema (Relato do Cliente)</label>
                    <textarea name="descricao_problema" id="descricao_problema" class="form-control" rows="3" required><?php echo htmlspecialchars($atendimento['descricao_problema'] ?? ''); ?></textarea>
                </div>

                <div class="mb-3">
                    <label for="observacoes_tecnicas" class="form-label">Observações Técnicas (Pós-Visita)</label>
                    <textarea name="observacoes_tecnicas" id="observacoes_tecnicas" class="form-control" rows="3"><?php echo htmlspecialchars($atendimento['observacoes_tecnicas'] ?? ''); ?></textarea>
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <button type="submit" class="btn btn-primary">Salvar Atendimento</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('cliente_search');
    const btnBuscar = document.getElementById('btn_buscar_cliente');
    const resultsDiv = document.getElementById('search_results');
    const clienteIdInput = document.getElementById('cliente_id');
    const clienteInfoDiv = document.getElementById('cliente_info');
    const enderecoInput = document.getElementById('endereco_visita');
    const enderecoOptions = document.getElementById('endereco_options');
    const radioPadrao = document.getElementById('endereco_padrao');
    const radioOutro = document.getElementById('endereco_outro');

    let clienteEnderecoOriginal = '';

    function buscarCliente() {
        const termo = searchInput.value.trim();
        if (termo.length < 2) {
            alert('Digite pelo menos 2 caracteres para buscar.');
            return;
        }

        btnBuscar.innerHTML = '⏳ Buscando...';
        btnBuscar.disabled = true;
        resultsDiv.style.display = 'none';

        fetch(`<?php echo BASE_URL; ?>ordens/search-client?termo=${encodeURIComponent(termo)}`)
            .then(async r => {
                const text = await r.text();
                try {
                    return JSON.parse(text);
                } catch (e) {
                    console.error('Resposta inválida do servidor:', text);
                    throw new Error('O servidor retornou um erro inesperado. Verifique o console.');
                }
            })
            .then(clientes => {
                resultsDiv.innerHTML = '';
                if (Array.isArray(clientes) && clientes.length > 0) {
                    clientes.forEach(c => {
                        const div = document.createElement('div');
                        div.className = 'autocomplete-item';
                        div.innerHTML = `<strong>${c.nome_completo}</strong><span>${c.documento || 'Sem documento'}</span>`;
                        div.onclick = () => selectClient(c);
                        resultsDiv.appendChild(div);
                    });
                    resultsDiv.style.display = 'block';
                } else {
                    resultsDiv.innerHTML = '<div class="autocomplete-item">Nenhum cliente encontrado. <a href="<?php echo BASE_URL; ?>clientes/criar" target="_blank">Cadastrar Novo?</a></div>';
                    resultsDiv.style.display = 'block';
                }
            })
            .catch(err => {
                console.error(err);
                alert(err.message);
            })
            .finally(() => {
                btnBuscar.innerHTML = '🔍 Buscar';
                btnBuscar.disabled = false;
            });
    }

    btnBuscar.onclick = buscarCliente;
    searchInput.onkeypress = (e) => {
        if (e.key === 'Enter') {
            e.preventDefault();
            buscarCliente();
        }
    };

    function selectClient(c) {
        clienteIdInput.value = c.id;
        searchInput.value = c.nome_completo;
        searchInput.readOnly = true;
        btnBuscar.disabled = true;
        resultsDiv.style.display = 'none';
        document.getElementById('info_nome').textContent = c.nome_completo;
        document.getElementById('info_documento').textContent = c.documento || 'N/A';
        document.getElementById('info_telefone').textContent = c.telefone_principal || 'N/A';
        clienteInfoDiv.style.display = 'block';
        
        // Lógica de endereço
        const logradouro = c.endereco_logradouro || '';
        const numero = c.endereco_numero || '';
        const bairro = c.endereco_bairro || '';
        const cidade = c.endereco_cidade || '';
        
        clienteEnderecoOriginal = `${logradouro} ${numero} ${bairro} ${cidade}`.trim();
        
        enderecoInput.value = clienteEnderecoOriginal;
        enderecoOptions.style.display = 'block';
        radioPadrao.checked = true;
    }

    window.removeClient = function() {
        clienteIdInput.value = '';
        searchInput.value = '';
        searchInput.readOnly = false;
        btnBuscar.disabled = false;
        clienteInfoDiv.style.display = 'none';
        resultsDiv.style.display = 'none';
        
        // Reset endereço
        enderecoInput.value = '';
        enderecoOptions.style.display = 'none';
        clienteEnderecoOriginal = '';
        
        searchInput.focus();
    }

    // Eventos para troca de endereço
    radioPadrao.addEventListener('change', () => {
        if (radioPadrao.checked) {
            enderecoInput.value = clienteEnderecoOriginal;
        }
    });

    radioOutro.addEventListener('change', () => {
        if (radioOutro.checked) {
            enderecoInput.value = '';
            enderecoInput.focus();
        }
    });

    // If page was opened with cliente_id in URL (e.g., linked from clientes), prefill
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('cliente_id') && !<?php echo isset($atendimento['id']) ? 'true' : 'false'; ?>) {
        // Para preenchimento via URL, precisamos dos dados de endereço também
        // Como o selectClient espera o objeto completo, vamos tentar pegar o que tem na URL
        // ou deixar o selectClient lidar com o que vier.
        selectClient({
            id: urlParams.get('cliente_id'),
            nome_completo: decodeURIComponent(urlParams.get('cliente_nome') || ''),
            documento: decodeURIComponent(urlParams.get('cliente_documento') || ''),
            telefone_principal: decodeURIComponent(urlParams.get('cliente_telefone') || ''),
            endereco_logradouro: decodeURIComponent(urlParams.get('cliente_logradouro') || ''),
            endereco_numero: decodeURIComponent(urlParams.get('cliente_numero') || ''),
            endereco_bairro: decodeURIComponent(urlParams.get('cliente_bairro') || ''),
            endereco_cidade: decodeURIComponent(urlParams.get('cliente_cidade') || '')
        });
    }
    
    // Se for edição, inicializar o endereço original
    <?php if (isset($cliente) && !isset($atendimento['id'])): ?>
        clienteEnderecoOriginal = enderecoInput.value;
    <?php elseif (isset($atendimento['id'])): ?>
        // Em edição, se o endereço for diferente do endereço do cliente, marcamos "outro"
        // Mas para simplificar, vamos apenas carregar o que está no banco.
        // Se o usuário quiser mudar, ele usa as opções.
    <?php endif; ?>
});
</script>

<style>
.autocomplete-results {
    position: relative;
    z-index: 1000;
    width: 100%;
    max-height: 220px;
    overflow-y: auto;
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 0 0 6px 6px;
}
.autocomplete-item { padding:10px; cursor:pointer; border-bottom:1px solid #eee; }
.autocomplete-item:hover { background:#f5f5f5; }
.autocomplete-item strong { display:block; }
.autocomplete-item span { color:#666; font-size:0.9em; }
</style>
