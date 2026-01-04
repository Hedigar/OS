<?php
$current_page = 'atendimentos_externos';
require_once __DIR__ . '/../layout/main.php';
?>

<div class="container">
    <div class="d-flex justify-between align-center mb-4">
        <h1><?php echo $title; ?></h1>
        <a href="<?php echo BASE_URL; ?>atendimentos-externos" class="btn btn-secondary">Voltar</a>
    </div>

    <div class="card mb-4">
        <form action="<?php echo BASE_URL; ?>atendimentos-externos/<?php echo isset($atendimento['id']) ? 'atualizar' : 'salvar'; ?>" method="POST">
            <?php if (isset($atendimento['id'])): ?>
                <input type="hidden" name="id" value="<?php echo $atendimento['id']; ?>">
            <?php endif; ?>

            <div class="form-grid mb-4">
                <div class="form-group">
                    <label for="cliente_search">Cliente</label>
                    <div class="d-flex gap-2 align-center">
                        <input
                            type="text"
                            id="cliente_search"
                            class="form-control flex-1"
                            placeholder="Nome, CPF ou CNPJ..."
                            value="<?php echo isset($cliente) ? htmlspecialchars($cliente['nome_completo']) : (isset($atendimento['cliente_nome']) ? htmlspecialchars($atendimento['cliente_nome']) : ''); ?>"
                            <?php echo isset($atendimento['id']) ? 'readonly' : ''; ?>
                            autocomplete="off"
                        >
                        <button type="button" id="btn_buscar_cliente" class="btn btn-primary" <?php echo isset($atendimento['id']) ? 'disabled' : ''; ?>>🔍 Buscar</button>
                    </div>
                    <input type="hidden" name="cliente_id" id="cliente_id" value="<?php echo isset($atendimento['cliente_id']) ? $atendimento['cliente_id'] : (isset($cliente) ? $cliente['id'] : ''); ?>" required>
                    
                    <div id="search_results" class="autocomplete-results mt-2" style="display:none;"></div>
                    
                    <div id="cliente_info" class="mt-3 p-3 border rounded" style="display: <?php echo isset($atendimento['cliente_id']) || isset($cliente) ? 'block' : 'none'; ?>;">
                        <div class="d-flex justify-between align-center mb-2">
                            <strong>Cliente Selecionado:</strong>
                            <button type="button" id="remove_client_btn" class="btn btn-danger btn-sm" onclick="removeClient()">✕ Alterar</button>
                        </div>
                        <p class="m-0"><strong>Nome:</strong> <span id="info_nome"><?php echo isset($atendimento['cliente_nome']) ? htmlspecialchars($atendimento['cliente_nome']) : (isset($cliente) ? htmlspecialchars($cliente['nome_completo']) : ''); ?></span></p>
                        <p class="m-0"><strong>Documento:</strong> <span id="info_documento"><?php echo isset($atendimento['cliente_documento']) ? htmlspecialchars($atendimento['cliente_documento']) : (isset($cliente) ? htmlspecialchars($cliente['documento']) : ''); ?></span></p>
                        <p class="m-0"><strong>Telefone:</strong> <span id="info_telefone"><?php echo isset($atendimento['cliente_telefone']) ? htmlspecialchars($atendimento['cliente_telefone']) : (isset($cliente) ? htmlspecialchars($cliente['telefone_principal'] ?? '') : ''); ?></span></p>
                    </div>
                </div>

                <div class="form-group">
                    <label for="usuario_id">Técnico Responsável</label>
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

            <div class="form-group mb-4">
                <div class="d-flex justify-between align-center mb-2">
                    <label for="endereco_visita" class="m-0">Endereço da Visita</label>
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

            <div class="form-grid mb-4">
                <div class="form-group">
                    <label for="data_agendada">Data/Hora Agendada</label>
                    <input type="datetime-local" name="data_agendada" id="data_agendada" class="form-control" value="<?php echo isset($atendimento['data_agendada']) ? date('Y-m-d\TH:i', strtotime($atendimento['data_agendada'])) : ''; ?>">
                </div>
                <div class="form-group">
                    <label for="status">Status</label>
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
                <div class="form-group">
                    <label for="valor_deslocamento">Valor Deslocamento (R$)</label>
                    <input type="number" step="0.01" name="valor_deslocamento" id="valor_deslocamento" class="form-control" value="<?php echo $atendimento['valor_deslocamento'] ?? '0.00'; ?>">
                </div>
            </div>

            <div class="form-group mb-4">
                <label for="descricao_problema">Descrição do Problema (Relato do Cliente)</label>
                <textarea name="descricao_problema" id="descricao_problema" class="form-control" rows="3" required><?php echo htmlspecialchars($atendimento['descricao_problema'] ?? ''); ?></textarea>
            </div>

            <div class="form-group mb-4">
                <label for="observacoes_tecnicas">Observações Técnicas (Pós-Visita)</label>
                <textarea name="observacoes_tecnicas" id="observacoes_tecnicas" class="form-control" rows="3"><?php echo htmlspecialchars($atendimento['observacoes_tecnicas'] ?? ''); ?></textarea>
            </div>

            <div class="d-flex justify-end">
                <button type="submit" class="btn btn-primary">Salvar Atendimento</button>
            </div>
        </form>
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
                    throw new Error('O servidor retornou um erro inesperado.');
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
                    resultsDiv.innerHTML = '<div class="autocomplete-item">Nenhum cliente encontrado.</div>';
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

        document.getElementById('info_nome').innerText = c.nome_completo;
        document.getElementById('info_documento').innerText = c.documento || 'N/A';
        document.getElementById('info_telefone').innerText = c.telefone_principal || 'N/A';
        clienteInfoDiv.style.display = 'block';
        enderecoOptions.style.display = 'block';

        clienteEnderecoOriginal = `${c.endereco_logradouro || ''} ${c.endereco_numero || ''} ${c.endereco_bairro || ''} ${c.endereco_cidade || ''}`.trim();
        if (radioPadrao.checked) {
            enderecoInput.value = clienteEnderecoOriginal;
        }
    }

    window.removeClient = function() {
        clienteIdInput.value = '';
        searchInput.value = '';
        searchInput.readOnly = false;
        btnBuscar.disabled = false;
        clienteInfoDiv.style.display = 'none';
        enderecoOptions.style.display = 'none';
        clienteEnderecoOriginal = '';
        if (radioPadrao.checked) {
            enderecoInput.value = '';
        }
    };

    radioPadrao.onchange = () => {
        if (radioPadrao.checked) {
            enderecoInput.value = clienteEnderecoOriginal;
            enderecoInput.readOnly = true;
        }
    };

    radioOutro.onchange = () => {
        if (radioOutro.checked) {
            enderecoInput.readOnly = false;
            enderecoInput.focus();
        }
    };

    if (radioPadrao.checked && clienteIdInput.value) {
        enderecoInput.readOnly = true;
    }
});
</script>

<style>
.autocomplete-results {
    position: absolute;
    z-index: 1000;
    background: var(--bg-secondary);
    border: 1px solid var(--border-color);
    border-radius: 8px;
    width: 100%;
    max-height: 200px;
    overflow-y: auto;
    box-shadow: var(--shadow-md);
}
.autocomplete-item {
    padding: 10px;
    cursor: pointer;
    border-bottom: 1px solid var(--border-color);
    display: flex;
    justify-content: space-between;
}
.autocomplete-item:hover {
    background: var(--bg-tertiary);
}
.justify-end { justify-content: flex-end !important; }
</style>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
