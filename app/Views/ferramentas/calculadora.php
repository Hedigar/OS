<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Calculadora de Preços</h1>

    <div class="row">
        <!-- Input Panel -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Parâmetros</h6>
                </div>
                <div class="card-body">
                    <form id="calcForm" onsubmit="return false;">
                        <div class="form-group">
                            <label for="custo">Custo do Produto/Serviço (R$)</label>
                            <input type="number" class="form-control form-control-lg" id="custo" step="0.01" placeholder="0,00" required autofocus>
                        </div>
                        
                        <div class="form-group">
                            <label for="margem">Margem Líquida (%)</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="margem" step="0.1" value="<?= $margem_padrao ?>" <?php echo \App\Core\Auth::isAdmin() ? '' : 'readonly'; ?>>
                                <div class="input-group-append">
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                            <small class="form-text text-muted">Quanto você quer lucrar livre.</small>
                        </div>

                        <div class="form-group">
                            <label for="imposto">Imposto / Nota Fiscal (%)</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="imposto" step="0.1" value="<?= $imposto_padrao ?>" <?php echo \App\Core\Auth::isAdmin() ? '' : 'readonly'; ?>>
                                <div class="input-group-append">
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                            <small class="form-text text-muted">Custo de nota fiscal e outros impostos.</small>
                        </div>
                        
                        <?php if (count($maquinas) > 1): ?>
                        <div class="form-group">
                            <label for="maquina">Máquina de Cartão</label>
                            <select class="form-control" id="maquina">
                                <?php foreach ($maquinas as $index => $mq): ?>
                                    <option value="<?= $index ?>"><?= htmlspecialchars($mq['nome']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <?php else: ?>
                            <input type="hidden" id="maquina" value="0">
                        <?php endif; ?>
                    </form>
                    
                    <?php if (\App\Core\Auth::isAdmin()): ?>
                    <hr>
                    <form action="<?= BASE_URL ?>calculadora/salvar-config" method="POST">
                        <input type="hidden" name="margem_padrao" id="save_margem">
                        <input type="hidden" name="imposto_padrao" id="save_imposto">
                        <button type="submit" class="btn btn-sm btn-outline-secondary w-100" onclick="updateSaveInputs()">
                            <i class="fas fa-save"></i> Salvar Margem/Imposto como Padrão
                        </button>
                    </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Results Panel -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">Tabela de Preços Sugeridos</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="resultsTable">
                            <thead class="thead-light">
                                <tr>
                                    <th>Forma de Pagamento</th>
                                    <th>Taxa Maq. (%)</th>
                                    <th>Valor Venda (R$)</th>
                                    <th>Lucro Líquido (R$)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="4" class="text-center text-muted">Digite o custo para calcular...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3 text-muted small">
                        <p class="mb-1"><strong>Fórmula utilizada:</strong> Venda = Custo / (1 - (Margem + Taxa + Imposto)/100)</p>
                        <p class="mb-0">* O lucro líquido exibido é o valor que sobra após descontar taxas da máquina, imposto e custo do produto.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const maquinas = <?= json_encode($maquinas) ?>;

function formatMoney(value) {
    return value.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
}

function calculate() {
    const custo = parseFloat(document.getElementById('custo').value) || 0;
    const margem = parseFloat(document.getElementById('margem').value) || 0;
    const imposto = parseFloat(document.getElementById('imposto').value) || 0;
    const maquinaIdx = document.getElementById('maquina').value || 0;
    
    if (custo <= 0) {
        document.querySelector('#resultsTable tbody').innerHTML = '<tr><td colspan="4" class="text-center text-muted">Digite o custo para calcular...</td></tr>';
        return;
    }

    const maquina = maquinas[maquinaIdx];
    // Se não tiver máquina configurada, ainda calcula para dinheiro
    const hasMaquina = !!maquina;

    let html = '';
    
    // Função auxiliar para calcular venda
    // Venda = Custo / (1 - (Margem + Taxa + Imposto)/100)
    function calcRow(label, taxa) {
        const taxaTotal = taxa + margem + imposto;
        
        // Evitar divisão por zero ou negativo
        if (taxaTotal >= 100) {
            return `<tr class="table-danger">
                <td>${label}</td>
                <td>${taxa.toFixed(2)}%</td>
                <td colspan="2">Impossível calcular (Margem+Taxas >= 100%)</td>
            </tr>`;
        }

        const divisor = 1 - (taxaTotal / 100);
        const venda = custo / divisor;
        const lucro = venda * (margem / 100);

        return `<tr>
            <td>${label}</td>
            <td>${taxa.toFixed(2)}%</td>
            <td class="font-weight-bold text-primary" style="font-size: 1.1em;">${formatMoney(venda)}</td>
            <td class="text-success">${formatMoney(lucro)}</td>
        </tr>`;
    }

    // Dinheiro (Taxa 0)
    html += calcRow('Dinheiro / Pix (Sem Taxa)', 0);

    if (hasMaquina) {
        // Débito
        if (maquina.debito_grupos) {
            if (maquina.debito_grupos.visa_master) {
                 html += calcRow('Débito (Visa/Master)', parseFloat(maquina.debito_grupos.visa_master));
            }
        }

        // Crédito
        if (maquina.credito_taxas) {
            // Visa/Master
            if (maquina.credito_taxas.visa_master) {
                 for (let i = 1; i <= 12; i++) {
                     if (maquina.credito_taxas.visa_master[i]) {
                         html += calcRow(`Crédito ${i}x (Visa/Master)`, parseFloat(maquina.credito_taxas.visa_master[i]));
                     }
                 }
            }
        }
    } else {
        html += '<tr><td colspan="4" class="text-center text-warning">Nenhuma máquina de cartão configurada/habilitada.</td></tr>';
    }

    document.querySelector('#resultsTable tbody').innerHTML = html;
}

function updateSaveInputs() {
    document.getElementById('save_margem').value = document.getElementById('margem').value;
    document.getElementById('save_imposto').value = document.getElementById('imposto').value;
}

// Event Listeners
document.getElementById('custo').addEventListener('input', calculate);
document.getElementById('margem').addEventListener('input', calculate);
document.getElementById('imposto').addEventListener('input', calculate);
if (document.getElementById('maquina')) {
    document.getElementById('maquina').addEventListener('change', calculate);
}

// Trigger initial calc if cost is present (e.g. browser autofill)
if (document.getElementById('custo').value) {
    calculate();
}
</script>
