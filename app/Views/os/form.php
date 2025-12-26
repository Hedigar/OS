<?php
$current_page = 'ordens';
require_once __DIR__ . '/../layout/main.php';

$is_edit = isset($ordem) && $ordem !== null;
$action_url = BASE_URL . ($is_edit ? 'ordens/update' : 'ordens/store');
$title_text = $is_edit ? 'Editar Ordem de Serviço #' . $ordem['id'] : 'Nova Ordem de Serviço';

// Dados passados pelo controller
$clientes = $clientes ?? [];
$statuses = $statuses ?? [];
$itens = $itens ?? [];

// Dados do cliente selecionado (se estiver em modo de edição)
$cliente_selecionado = null;
if ($is_edit) {
    // No modo de edição, o controller deve ter buscado o cliente completo
    // Assumindo que o nome do cliente está em $ordem['cliente_nome']
    $cliente_selecionado = [
        'id' => $ordem['cliente_id'],
        'nome_completo' => $ordem['cliente_nome'] ?? 'Cliente Não Encontrado',
        'documento' => $ordem['cliente_documento'] ?? '', // Estes campos não estão no findWithDetails, mas serão preenchidos via JS ou ajustados no controller
        'telefone_principal' => $ordem['cliente_telefone'] ?? ''
    ];
}
?>

<div class="container">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h1>📝 <?php echo htmlspecialchars($title_text); ?></h1>
        <a href="<?php echo BASE_URL; ?>ordens" class="btn btn-secondary btn-sm">← Voltar</a>
    </div>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger">
            <span>⚠️</span>
            <span><?php echo htmlspecialchars($error); ?></span>
        </div>
    <?php endif; ?>

    <div class="card">
        <form action="<?php echo htmlspecialchars($action_url); ?>" method="POST" id="os-form">
            <?php if ($is_edit): ?>
                <input type="hidden" name="id" value="<?php echo $ordem['id']; ?>">
            <?php endif; ?>

            <!-- Campo oculto para o status (sempre 'Aberto' na recepção) -->
            <input type="hidden" name="status_id" value="<?php echo $is_edit ? $ordem['status_id'] : 1; ?>">
            <input type="hidden" id="cliente_id" name="cliente_id" value="<?php echo $is_edit ? $ordem['cliente_id'] : ''; ?>" required>

            <!-- SEÇÃO: DADOS DO CLIENTE -->
            <div style="margin-bottom: 2rem;">
                <h3 style="color: var(--primary-red); margin-bottom: 1.5rem; padding-bottom: 0.5rem; border-bottom: 2px solid var(--primary-red);">
                    👥 Dados do Cliente
                </h3>

                <div class="form-group">
                    <label for="cliente_search">Buscar Cliente (Nome, CPF/CNPJ)</label>
                    <input
                        type="text"
                        id="cliente_search"
                        placeholder="Digite o nome, CPF ou CNPJ do cliente..."
                        value="<?php echo $is_edit ? htmlspecialchars($cliente_selecionado['nome_completo']) : ''; ?>"
                        <?php echo $is_edit ? 'readonly' : ''; ?>
                        autocomplete="off"
                        required
                    >
                    <div id="search_results" class="autocomplete-results"></div>
                </div>

                <div id="cliente_info" style="margin-top: 1rem; padding: 1rem; border: 1px solid var(--dark-tertiary); border-radius: 8px; display: <?php echo $is_edit ? 'block' : 'none'; ?>;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                        <p style="margin: 0;"><strong>Cliente Selecionado:</strong></p>
                        <div>
                            <a href="#" id="edit_client_link" class="btn btn-secondary btn-sm" style="margin-right: 0.5rem; display: <?php echo $is_edit ? 'inline-block' : 'none'; ?>;">✏️ Editar</a>
                            <button type="button" id="remove_client_btn" class="btn btn-danger btn-sm" onclick="removeClient()" style="display: <?php echo $is_edit ? 'inline-block' : 'none'; ?>;">✕ Remover</button>
                        </div>
                    </div>
                    <p><strong>Nome:</strong> <span id="info_nome"><?php echo $is_edit ? htmlspecialchars($cliente_selecionado['nome_completo']) : ''; ?></span></p>
                    <p><strong>Documento:</strong> <span id="info_documento"><?php echo $is_edit ? htmlspecialchars($cliente_selecionado['documento']) : ''; ?></span></p>
                    <p><strong>Celular:</strong> <span id="info_telefone"><?php echo $is_edit ? htmlspecialchars($cliente_selecionado['telefone_principal']) : ''; ?></span></p>
                </div>

                <!-- SEÇÃO: EQUIPAMENTOS ANTERIORES -->
                <div id="previous_equipments_section" style="margin-top: 1rem; display: none;">
                    <h4 style="color: var(--text-secondary); margin-bottom: 0.5rem;">Equipamentos Anteriores:</h4>
                    <div id="previous_equipments_list" style="display: flex; flex-wrap: wrap; gap: 10px;">
                        <!-- Lista de equipamentos anteriores será injetada aqui pelo JS -->
                    </div>
                    <p id="no_previous_equipments" style="display: none; color: var(--text-secondary);">Nenhum equipamento anterior encontrado para este cliente.</p>
                </div>
            </div>

            <!-- SEÇÃO: DADOS DO EQUIPAMENTO -->
            <div style="margin-bottom: 2rem;">
                <h3 style="color: var(--primary-red); margin-bottom: 1.5rem; padding-bottom: 0.5rem; border-bottom: 2px solid var(--primary-red);">
                    💻 Dados do Equipamento
                </h3>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem;">
                    <div class="form-group" style="grid-column: 1 / -1;">
                        <label for="equipamento">Nome do Equipamento *</label>
                        <input
                            type="text"
                            id="equipamento"
                            name="equipamento"
                            placeholder="Ex: Notebook Dell Inspiron 15"
                            value="<?php echo $is_edit ? htmlspecialchars($ordem['equipamento']) : ''; ?>"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label for="tipo_equipamento">Tipo</label>
                        <select id="tipo_equipamento" name="tipo_equipamento">
                            <option value="">Selecione</option>
                            <?php
                            $tipos = ['Notebook', 'Desktop', 'Monitor', 'Impressora', 'All in One', 'Outro'];
                            foreach ($tipos as $tipo):
                                $selected = ($is_edit && $ordem['tipo_equipamento'] == $tipo) ? 'selected' : '';
                                echo "<option value=\"{$tipo}\" {$selected}>{$tipo}</option>";
                            endforeach;
                            ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="marca_equipamento">Marca</label>
                        <input type="text" id="marca_equipamento" name="marca_equipamento" value="<?php echo $is_edit ? htmlspecialchars($ordem['marca_equipamento']) : ''; ?>">
                    </div>

                    <div class="form-group">
                        <label for="modelo_equipamento">Modelo</label>
                        <input type="text" id="modelo_equipamento" name="modelo_equipamento" value="<?php echo $is_edit ? htmlspecialchars($ordem['modelo_equipamento']) : ''; ?>">
                    </div>

                    <div class="form-group">
                        <label for="serial_equipamento">Serial</label>
                        <input type="text" id="serial_equipamento" name="serial_equipamento" value="<?php echo $is_edit ? htmlspecialchars($ordem['serial_equipamento']) : ''; ?>">
                    </div>

                    <div class="form-group">
                        <label for="senha_equipamento">Senha de Acesso</label>
                        <input type="text" id="senha_equipamento" name="senha_equipamento" value="<?php echo $is_edit ? htmlspecialchars($ordem['senha_equipamento']) : ''; ?>">
                    </div>

                    <div class="form-group">
                        <label for="fonte_equipamento">Fonte de Alimentação</label>
                        <select id="fonte_equipamento" name="fonte_equipamento">
                            <option value="nao" <?php echo ($is_edit && $ordem['fonte_equipamento'] == 'nao') ? 'selected' : ''; ?>>Não Deixou</option>
                            <option value="sim" <?php echo ($is_edit && $ordem['fonte_equipamento'] == 'sim') ? 'selected' : ''; ?>>Deixou</option>
                        </select>
                    </div>

                    <div class="form-group" style="grid-column: 1 / -1;">
                        <label for="acessorios_equipamento">Acessórios Deixados (Ex: Bolsa, Mouse, CD)</label>
                        <textarea
                            id="acessorios_equipamento"
                            name="acessorios_equipamento"
                            style="min-height: 80px;"
                        ><?php echo $is_edit ? htmlspecialchars($ordem['acessorios_equipamento']) : ''; ?></textarea>
                    </div>

                    <div class="form-group" style="grid-column: 1 / -1;">
                        <label for="defeito">Defeito Relatado *</label>
                        <textarea
                            id="defeito"
                            name="defeito"
                            placeholder="Descreva o problema relatado pelo cliente..."
                            style="min-height: 100px;"
                            required
                        ><?php echo $is_edit ? htmlspecialchars($ordem['defeito']) : ''; ?></textarea>
                    </div>
                </div>
            </div>

            <!-- SEÇÃO: CAMPOS DO TÉCNICO (OCULTOS NA RECEPÇÃO) -->
            <div id="tecnico_fields" style="display: none;">
                <!-- O campo 'servico' e a tabela de itens devem ser preenchidos pelo técnico -->
                <div class="form-group" style="grid-column: 1 / -1;">
                    <label for="servico">Serviço Realizado / Solução (Técnico)</label>
                    <textarea
                        id="servico"
                        name="servico"
                        placeholder="Descreva o serviço realizado ou a solução aplicada..."
                        style="min-height: 100px;"
                    ><?php echo $is_edit ? htmlspecialchars($ordem['servico']) : ''; ?></textarea>
                </div>

                <div class="form-group" style="grid-column: 1 / -1;">
                    <label for="observacoes_tecnicas">Observações Técnicas (Memória, Processador, SSD, etc.)</label>
                    <textarea
                        id="observacoes_tecnicas"
                        name="observacoes_tecnicas"
                        placeholder="Detalhes técnicos internos (RAM, CPU, SSD, etc.)..."
                        style="min-height: 100px;"
                    ><?php echo $is_edit ? htmlspecialchars($ordem['observacoes_tecnicas'] ?? '') : ''; ?></textarea>
                </div>

                <!-- SEÇÃO: ITENS (PRODUTOS E SERVIÇOS) - MANTIDA OCULTA -->
                <div style="margin-bottom: 2rem;">
                    <h3 style="color: var(--primary-red); margin-bottom: 1.5rem; padding-bottom: 0.5rem; border-bottom: 2px solid var(--primary-red);">
                        🛒 Produtos e Serviços (Técnico)
                    </h3>
                    <!-- O conteúdo da tabela de itens foi removido daqui para simplificar o fluxo de recepção.
                         O JS para itens foi mantido abaixo, mas a tabela em si está oculta. -->
                    <table id="itens-table" style="width: 100%;">
                        <thead>
                            <tr>
                                <th style="width: 10%;">Tipo</th>
                                <th style="width: 40%;">Descrição</th>
                                <th style="width: 15%;">Qtd</th>
                                <th style="width: 15%;">Vlr Unit.</th>
                                <th style="width: 15%;">Vlr Total</th>
                                <th style="width: 5%;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($is_edit && !empty($itens)): ?>
                                <?php foreach ($itens as $index => $item): ?>
                                    <tr class="item-row">
                                        <td>
                                            <select name="itens[<?php echo $index; ?>][tipo]" onchange="calculateTotal(this.parentNode.parentNode)">
                                                <option value="servico" <?php echo ($item['tipo'] == 'servico') ? 'selected' : ''; ?>>Serviço</option>
                                                <option value="produto" <?php echo ($item['tipo'] == 'produto') ? 'selected' : ''; ?>>Produto</option>
                                            </select>
                                        </td>
                                        <td><input type="text" name="itens[<?php echo $index; ?>][descricao]" value="<?php echo htmlspecialchars($item['descricao']); ?>" required></td>
                                        <td><input type="number" step="0.01" name="itens[<?php echo $index; ?>][quantidade]" value="<?php echo $item['quantidade']; ?>" oninput="calculateTotal(this.parentNode.parentNode)" required></td>
                                        <td><input type="number" step="0.01" name="itens[<?php echo $index; ?>][valor_unitario]" value="<?php echo $item['valor_unitario']; ?>" oninput="calculateTotal(this.parentNode.parentNode)" required></td>
                                        <td class="total-cell"><?php echo number_format($item['valor_total'], 2, ',', '.'); ?></td>
                                        <td><button type="button" class="btn btn-danger btn-sm" onclick="removeItem(this)">✕</button></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="4" style="text-align: right; font-weight: 600; color: var(--primary-red);">Total Produtos:</td>
                                <td id="total-produtos" style="font-weight: 600; color: var(--primary-red);">R$ 0,00</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td colspan="4" style="text-align: right; font-weight: 600; color: var(--primary-red);">Total Serviços:</td>
                                <td id="total-servicos" style="font-weight: 600; color: var(--primary-red);">R$ 0,00</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td colspan="4" style="text-align: right; font-weight: 700; font-size: 1.1rem; color: var(--text-primary);">TOTAL GERAL:</td>
                                <td id="total-geral" style="font-weight: 700; font-size: 1.1rem; color: var(--text-primary);">R$ 0,00</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>

                    <button type="button" class="btn btn-secondary btn-sm mt-3" onclick="addItem()">
                        ➕ Adicionar Item
                    </button>
                </div>
            </div>

            <!-- AÇÕES -->
            <div style="display: flex; gap: 1rem; margin-top: 2rem; padding-top: 2rem; border-top: 1px solid var(--dark-tertiary);">
                <button type="submit" class="btn btn-primary">
                    <?php echo $is_edit ? '✓ Atualizar OS' : '✓ Abrir OS'; ?>
                </button>
                <a href="<?php echo BASE_URL; ?>ordens" class="btn btn-secondary">
                    ✕ Cancelar
                </a>
            </div>
        </form>
    </div>
</div>

<script>
    // --- Lógica de Itens (Mantida para o modo de edição, mas oculta na recepção) ---
    let itemIndex = <?php echo $is_edit ? count($itens) : 0; ?>;

    function createItemRow(item = {}) {
        // ... (função createItemRow mantida)
        const index = itemIndex++;
        const tipo = item.tipo || 'servico';
        const descricao = item.descricao || '';
        const quantidade = item.quantidade || 1;
        const valor_unitario = item.valor_unitario || 0.00;
        const valor_total = item.valor_total || 0.00;

        const row = document.createElement('tr');
        row.className = 'item-row';
        row.innerHTML = `
            <td>
                <select name="itens[${index}][tipo]" onchange="calculateTotal(this.parentNode.parentNode)">
                    <option value="servico" ${tipo === 'servico' ? 'selected' : ''}>Serviço</option>
                    <option value="produto" ${tipo === 'produto' ? 'selected' : ''}>Produto</option>
                </select>
            </td>
            <td><input type="text" name="itens[${index}][descricao]" value="${descricao}" required></td>
            <td><input type="number" step="0.01" name="itens[${index}][quantidade]" value="${quantidade}" oninput="calculateTotal(this.parentNode.parentNode)" required></td>
            <td><input type="number" step="0.01" name="itens[${index}][valor_unitario]" value="${valor_unitario}" oninput="calculateTotal(this.parentNode.parentNode)" required></td>
            <td class="total-cell">${valor_total.toFixed(2).replace('.', ',')}</td>
            <td><button type="button" class="btn btn-danger btn-sm" onclick="removeItem(this)">✕</button></td>
        `;
        return row;
    }

    function addItem() {
        const tbody = document.querySelector('#itens-table tbody');
        tbody.appendChild(createItemRow());
        calculateTotals();
    }

    function removeItem(button) {
        button.closest('.item-row').remove();
        calculateTotals();
    }

    function calculateTotal(row) {
        const quantidadeInput = row.querySelector('input[name$="[quantidade]"]');
        const valorUnitarioInput = row.querySelector('input[name$="[valor_unitario]"]');
        const totalCell = row.querySelector('.total-cell');

        const quantidade = parseFloat(quantidadeInput.value) || 0;
        const valorUnitario = parseFloat(valorUnitarioInput.value) || 0;
        const total = quantidade * valorUnitario;

        totalCell.textContent = total.toFixed(2).replace('.', ',');
        calculateTotals();
    }

    function calculateTotals() {
        let totalProdutos = 0;
        let totalServicos = 0;

        document.querySelectorAll('.item-row').forEach(row => {
            const tipo = row.querySelector('select[name$="[tipo]"]').value;
            const totalText = row.querySelector('.total-cell').textContent.replace(',', '.');
            const total = parseFloat(totalText) || 0;

            if (tipo === 'produto') {
                totalProdutos += total;
            } else if (tipo === 'servico') {
                totalServicos += total;
            }
        });

        const totalGeral = totalProdutos + totalServicos;

        document.getElementById('total-produtos').textContent = 'R$ ' + totalProdutos.toFixed(2).replace('.', ',');
        document.getElementById('total-servicos').textContent = 'R$ ' + totalServicos.toFixed(2).replace('.', ',');
        document.getElementById('total-geral').textContent = 'R$ ' + totalGeral.toFixed(2).replace('.', ',');
    }

    // --- Lógica de Busca de Cliente (Autocomplete) ---
    const searchInput = document.getElementById('cliente_search');
    const resultsDiv = document.getElementById('search_results');
    const clienteIdInput = document.getElementById('cliente_id');
        const clienteInfoDiv = document.getElementById('cliente_info');
        const editClientLink = document.getElementById('edit_client_link');
        const removeClientBtn = document.getElementById('remove_client_btn');

    let searchTimeout;

    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        const term = this.value.trim();

        if (term.length < 3) {
            resultsDiv.style.display = 'none';
            return;
        }

        searchTimeout = setTimeout(() => {
            fetch('<?php echo BASE_URL; ?>clientes/search-ajax?term=' + encodeURIComponent(term))
                .then(response => response.json())
                .then(data => {
                    resultsDiv.innerHTML = '';
                    if (data.length > 0) {
                        data.forEach(cliente => {
                            const item = document.createElement('div');
                            item.className = 'autocomplete-item';
                            item.innerHTML = `
                                <strong>${cliente.nome_completo}</strong>
                                <span>${cliente.documento} - ${cliente.telefone_principal}</span>
                            `;
                            item.addEventListener('click', () => selectClient(cliente));
                            resultsDiv.appendChild(item);
                        });
                        resultsDiv.style.display = 'block';
                    } else {
                        resultsDiv.innerHTML = '<div class="autocomplete-item no-results">Nenhum cliente encontrado.</div>';
                        resultsDiv.style.display = 'block';
                    }
                })
                .catch(error => console.error('Erro na busca AJAX:', error));
        }, 300);
    });

	    function selectClient(cliente) {
        searchInput.value = cliente.nome_completo;
        clienteIdInput.value = cliente.id;
        resultsDiv.style.display = 'none';

        // Exibir dados básicos
        document.getElementById('info_nome').textContent = cliente.nome_completo;
        document.getElementById('info_documento').textContent = cliente.documento;
        document.getElementById('info_telefone').textContent = cliente.telefone_principal;
        clienteInfoDiv.style.display = 'block';

        // Bloquear a busca e mostrar botões de ação
        searchInput.setAttribute('readonly', 'readonly');
        searchInput.removeAttribute('required');
        editClientLink.style.display = 'inline-block';
        removeClientBtn.style.display = 'inline-block';
        editClientLink.href = `<?php echo BASE_URL; ?>clientes/edit/${cliente.id}`;

        // Carregar equipamentos anteriores
        loadPreviousEquipments(cliente.id);
    }

    // Esconder resultados ao clicar fora
    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !resultsDiv.contains(e.target)) {
            resultsDiv.style.display = 'none';
        }
    });

	    function removeClient() {
		        searchInput.value = '';
		        clienteIdInput.value = '';
		        clienteInfoDiv.style.display = 'none';

		        // Desbloquear a busca e esconder botões de ação
		        searchInput.removeAttribute('readonly');
		        searchInput.setAttribute('required', 'required');
		        editClientLink.style.display = 'none';
		        removeClientBtn.style.display = 'none';
		        editClientLink.href = '#';

                // Esconder seção de equipamentos anteriores
                document.getElementById('previous_equipments_section').style.display = 'none';
                document.getElementById('previous_equipments_list').innerHTML = '';
	    }

	    // Inicialização
	    // --- Lógica de Equipamentos Anteriores ---
        const previousEquipmentsSection = document.getElementById('previous_equipments_section');
        const previousEquipmentsList = document.getElementById('previous_equipments_list');
        const noPreviousEquipments = document.getElementById('no_previous_equipments');

        function loadPreviousEquipments(clienteId) {
            previousEquipmentsList.innerHTML = 'Carregando...';
            previousEquipmentsSection.style.display = 'block';
            noPreviousEquipments.style.display = 'none';

            fetch(`<?php echo BASE_URL; ?>ordens/search-equipamentos?cliente_id=${clienteId}`)
                .then(response => response.json())
                .then(equipamentos => {
                    previousEquipmentsList.innerHTML = '';
                    if (equipamentos.length > 0) {
                        equipamentos.forEach(equipamento => {
                            const item = document.createElement('div');
                            item.className = 'previous-equipment-item';
                            item.innerHTML = `
                                <strong>${equipamento.tipo_equipamento} ${equipamento.marca_equipamento} ${equipamento.modelo_equipamento}</strong>
                                <span>Serial: ${equipamento.serial_equipamento}</span>
                                <span style="font-size: 0.8em; color: var(--text-secondary);">Última OS: ${equipamento.ultima_os_id} (${new Date(equipamento.ultima_os_data).toLocaleDateString()})</span>
                            `;
                            item.addEventListener('click', () => populateEquipmentFields(equipamento));
                            previousEquipmentsList.appendChild(item);
                        });
                    } else {
                        noPreviousEquipments.style.display = 'block';
                    }
                })
                .catch(error => {
                    console.error('Erro ao buscar equipamentos anteriores:', error);
                    previousEquipmentsList.innerHTML = 'Erro ao carregar histórico.';
                });
        }

        function populateEquipmentFields(equipamento) {
            // Preenche os campos do formulário com os dados do equipamento anterior
            document.getElementById('equipamento').value = `${equipamento.tipo_equipamento} ${equipamento.marca_equipamento} ${equipamento.modelo_equipamento}`;
            document.getElementById('tipo_equipamento').value = equipamento.tipo_equipamento;
            document.getElementById('marca_equipamento').value = equipamento.marca_equipamento;
            document.getElementById('modelo_equipamento').value = equipamento.modelo_equipamento;
            document.getElementById('serial_equipamento').value = equipamento.serial_equipamento;
            document.getElementById('senha_equipamento').value = equipamento.senha_equipamento;
            document.getElementById('acessorios_equipamento').value = equipamento.acessorios_equipamento;
            document.getElementById('defeito').value = ''; // O defeito deve ser novo
            
            // Seleciona a fonte
            const fonteSelect = document.getElementById('fonte_equipamento');
            for (let i = 0; i < fonteSelect.options.length; i++) {
                if (fonteSelect.options[i].value === equipamento.fonte_equipamento) {
                    fonteSelect.selectedIndex = i;
                    break;
                }
            }

            // Opcional: Rolar para a seção de equipamento
            document.getElementById('equipamento').scrollIntoView({ behavior: 'smooth	       // Inicialização
    document.addEventListener('DOMContentLoaded', () => {
        // Se estiver em modo de edição, inicializa o link de edição
        if (<?php echo $is_edit ? 'true' : 'false'; ?>) {
            const currentClientId = clienteIdInput.value;
            if (currentClientId) {
                editClientLink.href = `<?php echo BASE_URL; ?>clientes/edit/${currentClientId}`;
            }
            // No modo de edição, carrega os equipamentos anteriores
            if (clienteIdInput.value) {
                loadPreviousEquipments(clienteIdInput.value);
            }
        }    // Se não estiver em modo de edição, esconde os campos do técnico
        if (!<?php echo $is_edit ? 'true' : 'false'; ?>) {
            document.getElementById('tecnico_fields').style.display = 'none';
        } else {
            // Se estiver em modo de edição, mostra os campos do técnico e inicializa itens
            document.getElementById('tecnico_fields').style.display = 'block';
            if (document.querySelectorAll('.item-row').length === 0) {
                addItem();
            }
            calculateTotals();
        }

        // Lógica de Autocomplete para Cliente
        const searchInput = document.getElementById('cliente_search');
        const resultsDiv = document.getElementById('search_results');
        const clienteIdInput = document.getElementById('cliente_id');
        const clienteInfoDiv = document.getElementById('cliente_info');
        const infoNome = document.getElementById('info_nome');
        const infoDocumento = document.getElementById('info_documento');
        const infoTelefone = document.getElementById('info_telefone');
        const removeClientBtn = document.getElementById('remove_client_btn');
        const editClientLink = document.getElementById('edit_client_link');

        let searchTimeout;

        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            const termo = this.value.trim();

            if (termo.length < 3) {
                resultsDiv.style.display = 'none';
                resultsDiv.innerHTML = '';
                return;
            }

            searchTimeout = setTimeout(() => {
                fetch(`<?php echo BASE_URL; ?>ordens/search-client?termo=${encodeURIComponent(termo)}`)
                    .then(response => response.json())
                    .then(clientes => {
                        resultsDiv.innerHTML = '';
                        if (clientes.length > 0) {
                            clientes.forEach(cliente => {
                                const item = document.createElement('div');
                                item.classList.add('autocomplete-item');
                                item.innerHTML = `<strong>${cliente.nome_completo}</strong><span>${cliente.documento || 'CPF/CNPJ não informado'}</span>`;
                                item.addEventListener('click', () => selectClient(cliente));
                                resultsDiv.appendChild(item);
                            });
                            resultsDiv.style.display = 'block';
                        } else {
                            resultsDiv.innerHTML = '<div class="autocomplete-item no-results">Nenhum cliente encontrado.</div>';
                            resultsDiv.style.display = 'block';
                        }
                    })
                    .catch(error => console.error('Erro na busca de clientes:', error));
            }, 300);
        });

        // Esconder resultados ao clicar fora
        document.addEventListener('click', function(event) {
            if (!searchInput.contains(event.target) && !resultsDiv.contains(event.target)) {
                resultsDiv.style.display = 'none';
            }
        });

        function selectClient(cliente) {
            // 1. Preenche o campo oculto e o campo de busca
            clienteIdInput.value = cliente.id;
            searchInput.value = cliente.nome_completo;
            searchInput.setAttribute('readonly', 'readonly');
            resultsDiv.style.display = 'none';

            // 2. Exibe as informações do cliente
            clienteInfoDiv.style.display = 'block';
            infoNome.textContent = cliente.nome_completo;
            infoDocumento.textContent = cliente.documento || 'Não Informado';
            // O telefone não está no retorno do buscarPorTermo, mas vamos assumir que o controller
            // ou uma nova requisição traria o dado completo. Por enquanto, deixamos vazio.
            infoTelefone.textContent = 'Buscando...'; // Será atualizado por loadClientDetails

            // 3. Atualiza o link de edição
            editClientLink.href = `<?php echo BASE_URL; ?>clientes/editar?id=${cliente.id}`;
            editClientLink.style.display = 'inline-block';
            removeClientBtn.style.display = 'inline-block';

            // 4. Carrega os detalhes completos do cliente (incluindo telefone) e equipamentos anteriores
            loadClientDetails(cliente.id);
            loadPreviousEquipments(cliente.id);
        }

        // Função para remover o cliente selecionado
        window.removeClient = function() {
            clienteIdInput.value = '';
            searchInput.value = '';
            searchInput.removeAttribute('readonly');
            clienteInfoDiv.style.display = 'none';
            editClientLink.style.display = 'none';
            removeClientBtn.style.display = 'none';
            document.getElementById('previous_equipments_section').style.display = 'none';
            document.getElementById('previous_equipments_list').innerHTML = '';
            searchInput.focus();
        }

        // Função para carregar detalhes completos do cliente (necessário para o telefone)
        function loadClientDetails(clienteId) {
            // Precisamos de um endpoint no ClienteController para buscar um cliente por ID
            // Por enquanto, vamos simular, mas o ideal é criar o endpoint.
            // Para o escopo atual, vamos focar na pesquisa e na visualização.
            // O telefone não está no retorno do buscarPorTermo, mas vamos assumir que o controller
            // ou uma nova requisição traria o dado completo. Por enquanto, deixamos vazio.
            infoTelefone.textContent = 'Não disponível na busca rápida';
        }

        // Função para carregar equipamentos anteriores
        function loadPreviousEquipments(clienteId) {
            const listDiv = document.getElementById('previous_equipments_list');
            const sectionDiv = document.getElementById('previous_equipments_section');
            const noEquipmentsP = document.getElementById('no_previous_equipments');
            listDiv.innerHTML = '';
            sectionDiv.style.display = 'block';
            noEquipmentsP.style.display = 'none';

            fetch(`<?php echo BASE_URL; ?>ordens/search-equipamentos?cliente_id=${clienteId}`)
                .then(response => response.json())
                .then(equipamentos => {
                    if (equipamentos.length > 0) {
                        equipamentos.forEach(equipamento => {
                            const item = document.createElement('div');
                            item.classList.add('previous-equipment-item');
                            item.innerHTML = `
                                <strong>${equipamento.tipo} - ${equipamento.marca} ${equipamento.modelo}</strong>
                                <span>Serial: ${equipamento.serial || 'N/A'}</span>
                                <span>Defeito OS #${equipamento.ordem_servico_id}: ${equipamento.defeito_relatado.substring(0, 50)}...</span>
                            `;
                            item.addEventListener('click', () => usePreviousEquipment(equipamento));
                            listDiv.appendChild(item);
                        });
                    } else {
                        noEquipmentsP.style.display = 'block';
                    }
                })
                .catch(error => console.error('Erro ao carregar equipamentos anteriores:', error));
        }

        // Se o cliente foi passado via GET (Nova OS a partir da página do cliente)
        const urlParams = new URLSearchParams(window.location.search);
        const preSelectedClientId = urlParams.get('cliente_id');
        const preSelectedClientName = urlParams.get('cliente_nome');
        const preSelectedClientDoc = urlParams.get('cliente_documento');

        if (preSelectedClientId && preSelectedClientName && !<?php echo $is_edit ? 'true' : 'false'; ?>) {
            selectClient({
                id: preSelectedClientId,
                nome_completo: preSelectedClientName,
                documento: preSelectedClientDoc
            });
        }

    });  });
</script>

<style>
    /* Estilos para o Autocomplete */
    .autocomplete-results {
        position: absolute;
        z-index: 1000;
        width: calc(100% - 2rem); /* Ajuste conforme o padding do container */
        max-height: 200px;
        overflow-y: auto;
        border: 1px solid var(--dark-tertiary);
        border-top: none;
        background-color: var(--dark-secondary);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        display: none;
    }
    .autocomplete-item {
        padding: 10px;
        cursor: pointer;
        border-bottom: 1px solid var(--dark-tertiary);
        display: flex;
        flex-direction: column;
        transition: background-color 0.2s;
    }
    .autocomplete-item:hover {
        background-color: var(--dark-tertiary);
    }
    .autocomplete-item strong {
        color: var(--text-primary);
    }
    .autocomplete-item span {
        font-size: 0.8em;
        color: var(--text-secondary);
    }
	    .autocomplete-item.no-results {
	        cursor: default;
	        color: var(--text-secondary);
	    }

        .previous-equipment-item {
            padding: 10px;
            border: 1px solid var(--dark-tertiary);
            border-radius: 6px;
            cursor: pointer;
            transition: background-color 0.2s;
            display: flex;
            flex-direction: column;
            width: 100%;
            max-width: 300px;
        }

        .previous-equipment-item:hover {
            background-color: var(--dark-tertiary);
        }

        .previous-equipment-item strong {
            color: var(--text-primary);
            margin-bottom: 5px;
        }
	</style>
	
	<?php require_once __DIR__ . '/../layout/footer.php'; ?>
