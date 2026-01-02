<?php
$current_page = 'ordens';
require_once __DIR__ . '/../layout/main.php';

$is_edit = isset($ordem) && $ordem !== null;
$action_url = BASE_URL . ($is_edit ? 'ordens/atualizar' : 'ordens/salvar');
$title_text = $is_edit ? 'Editar Ordem de Serviço #' . $ordem['id'] : 'Nova Ordem de Serviço';

$statuses = $statuses ?? [];
?>

<div class="container">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h1>📝 <?php echo htmlspecialchars($title_text); ?></h1>
        <a href="<?php echo BASE_URL; ?>ordens" class="btn btn-secondary btn-sm">← Voltar</a>
    </div>

    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger">
            <span>⚠️</span>
            <span><?php echo htmlspecialchars($_GET['error']); ?></span>
        </div>
    <?php endif; ?>

    <div class="card">
        <form action="<?php echo htmlspecialchars($action_url); ?>" method="POST" id="os-form">
            <?php if ($is_edit): ?>
                <input type="hidden" name="id" value="<?php echo $ordem['id']; ?>">
            <?php endif; ?>

            <input type="hidden" id="cliente_id" name="cliente_id" value="<?php echo $is_edit ? $ordem['cliente_id'] : ''; ?>" required>
            <input type="hidden" id="equipamento_id" name="equipamento_id" value="<?php echo $is_edit ? $ordem['equipamento_id'] : ''; ?>">

            <!-- SEÇÃO: DADOS DO CLIENTE -->
            <div style="margin-bottom: 2rem;">
                <h3 style="color: var(--primary-red); margin-bottom: 1.5rem; padding-bottom: 0.5rem; border-bottom: 2px solid var(--primary-red);">
                    👥 1. Seleção do Cliente (Obrigatório)
                </h3>

                <div class="form-group">
                    <label for="cliente_search">Buscar Cliente (Nome, CPF/CNPJ)</label>
                    <div style="display: flex; gap: 10px;">
                        <input
                            type="text"
                            id="cliente_search"
                            placeholder="Digite o nome, CPF ou CNPJ e aperte Enter ou clique em Buscar..."
                            value="<?php echo $is_edit ? htmlspecialchars($ordem['cliente_nome']) : ''; ?>"
                            <?php echo $is_edit ? 'readonly' : ''; ?>
                            autocomplete="off"
                            style="flex: 1;"
                        >
                        <button type="button" id="btn_buscar_cliente" class="btn btn-primary" <?php echo $is_edit ? 'disabled' : ''; ?>>🔍 Buscar</button>
                    </div>
                    <div id="search_results" class="autocomplete-results"></div>
                </div>

                <div id="cliente_info" style="margin-top: 1rem; padding: 1rem; border: 1px solid var(--dark-tertiary); border-radius: 8px; display: <?php echo $is_edit ? 'block' : 'none'; ?>;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                        <p style="margin: 0;"><strong>Cliente Selecionado:</strong></p>
                        <button type="button" id="remove_client_btn" class="btn btn-danger btn-sm" onclick="removeClient()">✕ Alterar Cliente</button>
                    </div>
                    <p><strong>Nome:</strong> <span id="info_nome"><?php echo $is_edit ? htmlspecialchars($ordem['cliente_nome']) : ''; ?></span></p>
                    <p><strong>Documento:</strong> <span id="info_documento"><?php echo $is_edit ? htmlspecialchars($ordem['cliente_documento']) : ''; ?></span></p>
                    <p><strong>Celular:</strong> <span id="info_telefone"><?php echo $is_edit ? htmlspecialchars($ordem['cliente_telefone']) : ''; ?></span></p>
                </div>
            </div>

            <!-- SEÇÃO: DADOS DO EQUIPAMENTO -->
            <div id="equipamento_section" style="margin-bottom: 2rem; opacity: <?php echo $is_edit ? '1' : '0.5'; ?>; pointer-events: <?php echo $is_edit ? 'auto' : 'none'; ?>;">
                <h3 style="color: var(--primary-red); margin-bottom: 1.5rem; padding-bottom: 0.5rem; border-bottom: 2px solid var(--primary-red);">
                    💻 2. Dados do Equipamento
                </h3>

                <div id="select_equipamento_div" class="form-group" style="display: none; margin-bottom: 1.5rem;">
                    <label for="equipamento_select">Selecionar Equipamento Existente (Opcional)</label>
                    <select id="equipamento_select" class="form-control">
                        <option value="">-- Novo Equipamento --</option>
                    </select>
                </div>

                <div id="equipamento_fields_container">
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem;">
                        <div class="form-group">
                            <label for="tipo_equipamento">Tipo *</label>
                            <select id="tipo_equipamento" name="tipo_equipamento" required>
                                <option value="">Selecione</option>
                                <?php
                                $tipos = ['Notebook', 'Desktop', 'Monitor', 'Impressora', 'All in One', 'Smartphone', 'Tablet', 'Outro'];
                                foreach ($tipos as $tipo):
                                    $selected = ($is_edit && $ordem['equipamento_tipo'] == $tipo) ? 'selected' : '';
                                    echo "<option value=\"{$tipo}\" {$selected}>{$tipo}</option>";
                                endforeach;
                                ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="marca_equipamento">Marca</label>
                            <input type="text" id="marca_equipamento" name="marca_equipamento" value="<?php echo $is_edit ? htmlspecialchars($ordem['equipamento_marca']) : ''; ?>">
                        </div>

                        <div class="form-group">
                            <label for="modelo_equipamento">Modelo</label>
                            <input type="text" id="modelo_equipamento" name="modelo_equipamento" value="<?php echo $is_edit ? htmlspecialchars($ordem['equipamento_modelo']) : ''; ?>">
                        </div>

                        <div class="form-group">
                            <label for="serial_equipamento">Serial / IMEI</label>
                            <input type="text" id="serial_equipamento" name="serial_equipamento" value="<?php echo $is_edit ? htmlspecialchars($ordem['equipamento_serial']) : ''; ?>">
                        </div>

                        <div class="form-group">
                            <label for="senha_equipamento">Senha de Acesso</label>
                            <input type="text" id="senha_equipamento" name="senha_equipamento" value="<?php echo $is_edit ? htmlspecialchars($ordem['equipamento_senha']) : ''; ?>">
                        </div>

                        <div class="form-group">
                            <label for="fonte_equipamento">Fonte de Alimentação</label>
                            <select id="fonte_equipamento" name="fonte_equipamento">
                                <option value="nao" <?php echo ($is_edit && $ordem['equipamento_fonte'] == 0) ? 'selected' : ''; ?>>Não Deixou</option>
                                <option value="sim" <?php echo ($is_edit && $ordem['equipamento_fonte'] == 1) ? 'selected' : ''; ?>>Deixou</option>
                            </select>
                        </div>

                        <div class="form-group" style="grid-column: 1 / -1;">
                            <label for="acessorios_equipamento">Acessórios Deixados</label>
                            <textarea id="acessorios_equipamento" name="acessorios_equipamento" style="min-height: 60px;"><?php echo $is_edit ? htmlspecialchars($ordem['equipamento_acessorios']) : ''; ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SEÇÃO: DADOS DA OS -->
            <div id="os_section" style="margin-bottom: 2rem; opacity: <?php echo $is_edit ? '1' : '0.5'; ?>; pointer-events: <?php echo $is_edit ? 'auto' : 'none'; ?>;">
                <h3 style="color: var(--primary-red); margin-bottom: 1.5rem; padding-bottom: 0.5rem; border-bottom: 2px solid var(--primary-red);">
                    📝 3. Informações da Ordem de Serviço
                </h3>

                <div class="form-group">
                    <label for="defeito">Defeito Relatado *</label>
                    <textarea id="defeito" name="defeito" placeholder="Descreva o problema relatado pelo cliente..." style="min-height: 100px; <?php echo $is_edit ? 'background-color: var(--dark-tertiary); cursor: not-allowed;' : ''; ?>" required <?php echo $is_edit ? 'readonly' : ''; ?>><?php echo $is_edit ? htmlspecialchars($ordem['defeito_relatado']) : ''; ?></textarea>
                    <?php if ($is_edit): ?>
                        <small style="color: #888;">* Edição bloqueada para padronização.</small>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="laudo_tecnico">Laudo Técnico / Observações Internas</label>
                    <textarea id="laudo_tecnico" name="laudo_tecnico" placeholder="Descreva o diagnóstico técnico, estado do equipamento, etc..." style="min-height: 100px;"><?php echo $is_edit ? htmlspecialchars($ordem['laudo_tecnico'] ?? '') : ''; ?></textarea>
                </div>

                <div class="form-group">
                    <label for="status_id">Status da OS</label>
                    <select id="status_id" name="status_id">
                        <?php foreach ($statuses as $status): ?>
                            <option value="<?php echo $status['id']; ?>" <?php echo ($is_edit && $ordem['status_atual_id'] == $status['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($status['nome']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div style="margin-top: 2rem; display: flex; gap: 1rem;">
                <button type="submit" class="btn btn-primary btn-lg">💾 Salvar Ordem de Serviço</button>
                <a href="<?php echo BASE_URL; ?>ordens" class="btn btn-secondary btn-lg">Cancelar</a>
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
    const equipSection = document.getElementById('equipamento_section');
    const osSection = document.getElementById('os_section');
    const equipSelectDiv = document.getElementById('select_equipamento_div');
    const equipSelect = document.getElementById('equipamento_select');
    const removeClientBtn = document.getElementById('remove_client_btn');

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

        equipSection.style.opacity = '1';
        equipSection.style.pointerEvents = 'auto';
        osSection.style.opacity = '1';
        osSection.style.pointerEvents = 'auto';

        loadEquipamentos(c.id);
    }

    window.removeClient = function() {
        clienteIdInput.value = '';
        searchInput.value = '';
        searchInput.readOnly = false;
        btnBuscar.disabled = false;
        clienteInfoDiv.style.display = 'none';
        equipSection.style.opacity = '0.5';
        equipSection.style.pointerEvents = 'none';
        osSection.style.opacity = '0.5';
        osSection.style.pointerEvents = 'none';
        equipSelectDiv.style.display = 'none';
        resetEquipFields();
        searchInput.focus();
    }

    function loadEquipamentos(clienteId) {
        fetch(`<?php echo BASE_URL; ?>ordens/search-equipamentos?cliente_id=${clienteId}`)
            .then(r => r.json())
            .then(equips => {
                equipSelect.innerHTML = '<option value="">-- Novo Equipamento --</option>';
                if (Array.isArray(equips) && equips.length > 0) {
                    equips.forEach(e => {
                        const opt = document.createElement('option');
                        opt.value = e.id;
                        opt.textContent = `${e.tipo} - ${e.marca} ${e.modelo} (${e.serial || 'S/N'})`;
                        opt.dataset.info = JSON.stringify(e);
                        equipSelect.appendChild(opt);
                    });
                    equipSelectDiv.style.display = 'block';
                } else {
                    equipSelectDiv.style.display = 'none';
                }
            });
    }

    equipSelect.onchange = function() {
        const equipId = this.value;
        document.getElementById('equipamento_id').value = equipId;
        if (equipId) {
            const data = JSON.parse(this.options[this.selectedIndex].dataset.info);
            fillEquipFields(data);
            lockEquipFields(true);
        } else {
            resetEquipFields();
            lockEquipFields(false);
        }
    };

    function fillEquipFields(data) {
        document.getElementById('tipo_equipamento').value = data.tipo;
        document.getElementById('marca_equipamento').value = data.marca;
        document.getElementById('modelo_equipamento').value = data.modelo;
        document.getElementById('serial_equipamento').value = data.serial;
        document.getElementById('senha_equipamento').value = data.senha;
        document.getElementById('fonte_equipamento').value = data.possui_fonte == 1 ? 'sim' : 'nao';
        document.getElementById('acessorios_equipamento').value = data.acessorios;
    }

    function resetEquipFields() {
        document.getElementById('equipamento_id').value = '';
        document.getElementById('tipo_equipamento').value = '';
        document.getElementById('marca_equipamento').value = '';
        document.getElementById('modelo_equipamento').value = '';
        document.getElementById('serial_equipamento').value = '';
        document.getElementById('senha_equipamento').value = '';
        document.getElementById('fonte_equipamento').value = 'nao';
        document.getElementById('acessorios_equipamento').value = '';
    }

    function lockEquipFields(lock) {
        const fields = ['tipo_equipamento', 'marca_equipamento', 'modelo_equipamento', 'serial_equipamento', 'senha_equipamento', 'fonte_equipamento', 'acessorios_equipamento'];
        fields.forEach(id => {
            const el = document.getElementById(id);
            if (lock) {
                el.setAttribute('readonly', 'readonly');
                if (el.tagName === 'SELECT') el.style.pointerEvents = 'none';
            } else {
                el.removeAttribute('readonly');
                if (el.tagName === 'SELECT') el.style.pointerEvents = 'auto';
            }
        });
    }

    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('cliente_id') && !<?php echo $is_edit ? 'true' : 'false'; ?>) {
        selectClient({
            id: urlParams.get('cliente_id'),
            nome_completo: decodeURIComponent(urlParams.get('cliente_nome')),
            documento: decodeURIComponent(urlParams.get('cliente_documento')),
            telefone_principal: ''
        });
    }
});
</script>

<style>
.autocomplete-results {
    position: absolute;
    z-index: 1000;
    width: 100%;
    max-height: 250px;
    overflow-y: auto;
    background: var(--dark-secondary);
    border: 1px solid var(--dark-tertiary);
    border-radius: 0 0 8px 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.3);
}
.autocomplete-item {
    padding: 12px;
    cursor: pointer;
    border-bottom: 1px solid var(--dark-tertiary);
    transition: background 0.2s;
}
.autocomplete-item:hover { background: var(--dark-tertiary); }
.autocomplete-item strong { display: block; color: var(--text-primary); font-size: 1.1em; }
.autocomplete-item span { font-size: 0.9em; color: var(--text-secondary); }
.autocomplete-item a { color: var(--primary-red); text-decoration: underline; }
</style>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
