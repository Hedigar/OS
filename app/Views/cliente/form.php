<?php
$is_edit = isset($cliente) && $cliente;
$action_url = $is_edit ? BASE_URL . 'clientes/atualizar' : BASE_URL . 'clientes/salvar';
$title_text = $is_edit
    ? 'Editar Cliente: ' . htmlspecialchars($cliente['nome_completo'] ?? '')
    : 'Novo Cliente';
$current_page = 'clientes';
require_once __DIR__ . '/../layout/main.php';
?>

<div class="container">
    <div class="d-flex justify-between align-center mb-4">
        <h1><?php echo htmlspecialchars($title_text); ?></h1>
        <a href="<?php echo BASE_URL; ?>clientes" class="btn btn-secondary btn-sm">← Voltar</a>
    </div>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger">
            <span>⚠️</span>
            <span><?php echo htmlspecialchars($error); ?></span>
        </div>
    <?php endif; ?>

    <div class="card">
        <form action="<?php echo htmlspecialchars($action_url); ?>" method="POST">
            <?php if ($is_edit): ?>
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($cliente['id']); ?>">
            <?php endif; ?>

            <!-- SEÇÃO: INFORMAÇÕES PESSOAIS -->
            <div class="mb-4">
                <h3 class="card-title">📋 Informações Pessoais</h3>

                <div class="form-grid">
                    <div class="form-group">
                        <label for="nome_completo">Nome Completo *</label>
                        <input
                            type="text"
                            id="nome_completo"
                            name="nome_completo"
                            class="form-control"
                            placeholder="Digite o nome completo"
                            value="<?php echo $is_edit ? htmlspecialchars($cliente['nome_completo'] ?? '') : ''; ?>"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label for="tipo_pessoa">Tipo de Pessoa</label>
                        <select id="tipo_pessoa" name="tipo_pessoa" class="form-select">
                            <option value="fisica" <?php echo ($is_edit && $cliente['tipo_pessoa'] === 'fisica') ? 'selected' : ''; ?>>
                                Pessoa Física
                            </option>
                            <option value="juridica" <?php echo ($is_edit && $cliente['tipo_pessoa'] === 'juridica') ? 'selected' : ''; ?>>
                                Pessoa Jurídica
                            </option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="documento">Documento (CPF/CNPJ)</label>
                        <input
                            type="text"
                            id="documento"
                            name="documento"
                            class="form-control"
                            placeholder="000.000.000-00"
                            value="<?php echo $is_edit ? htmlspecialchars($cliente['documento'] ?? '') : ''; ?>"
                            autocomplete="off"
                        >
                        <div id="documento-feedback" class="invalid-feedback" style="display: none;">
                            Este documento já está cadastrado. Redirecionando...
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="data_nascimento">Data de Nascimento</label>
                        <input
                            type="date"
                            id="data_nascimento"
                            name="data_nascimento"
                            class="form-control"
                            value="<?php echo $is_edit ? htmlspecialchars($cliente['data_nascimento'] ?? '') : ''; ?>"
                        >
                    </div>
                </div>
            </div>

            <!-- SEÇÃO: CONTATO -->
            <div class="mb-4">
                <h3 class="card-title">📞 Contato</h3>

                <div class="form-grid">
                    <div class="form-group">
                        <label for="email">E-mail</label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            class="form-control"
                            placeholder="exemplo@email.com"
                            value="<?php echo $is_edit ? htmlspecialchars($cliente['email'] ?? '') : ''; ?>"
                        >
                    </div>

                    <div class="form-group">
                        <label for="telefone_principal">Telefone Principal</label>
                        <input
                            type="tel"
                            id="telefone_principal"
                            name="telefone_principal"
                            class="form-control"
                            placeholder="(11) 98765-4321"
                            value="<?php echo $is_edit ? htmlspecialchars($cliente['telefone_principal'] ?? '') : ''; ?>"
                        >
                    </div>

                    <div class="form-group">
                        <label for="telefone_secundario">Telefone Secundário</label>
                        <input
                            type="tel"
                            id="telefone_secundario"
                            name="telefone_secundario"
                            class="form-control"
                            placeholder="(11) 98765-4321"
                            value="<?php echo $is_edit ? htmlspecialchars($cliente['telefone_secundario'] ?? '') : ''; ?>"
                        >
                    </div>
                </div>
            </div>

            <!-- SEÇÃO: ENDEREÇO -->
            <div class="mb-4">
                <h3 class="card-title">🏠 Endereço</h3>

                <div class="form-grid">
                    <div class="form-group full-width">
                        <label for="endereco_logradouro">Logradouro</label>
                        <input
                            type="text"
                            id="endereco_logradouro"
                            name="endereco_logradouro"
                            class="form-control"
                            placeholder="Rua, Avenida, etc."
                            value="<?php echo $is_edit ? htmlspecialchars($cliente['endereco_logradouro'] ?? '') : ''; ?>"
                        >
                    </div>

                    <div class="form-group">
                        <label for="endereco_numero">Número</label>
                        <input
                            type="text"
                            id="endereco_numero"
                            name="endereco_numero"
                            class="form-control"
                            placeholder="123"
                            value="<?php echo $is_edit ? htmlspecialchars($cliente['endereco_numero'] ?? '') : ''; ?>"
                        >
                    </div>

                    <div class="form-group">
                        <label for="endereco_bairro">Bairro</label>
                        <input
                            type="text"
                            id="endereco_bairro"
                            name="endereco_bairro"
                            class="form-control"
                            placeholder="Bairro"
                            value="<?php echo $is_edit ? htmlspecialchars($cliente['endereco_bairro'] ?? '') : ''; ?>"
                        >
                    </div>

                    <div class="form-group">
                        <label for="endereco_cidade">Cidade</label>
                        <input
                            type="text"
                            id="endereco_cidade"
                            name="endereco_cidade"
                            class="form-control"
                            placeholder="Cidade"
                            value="<?php echo $is_edit ? htmlspecialchars($cliente['endereco_cidade'] ?? '') : ''; ?>"
                        >
                    </div>
                </div>
            </div>

            <!-- SEÇÃO: OBSERVAÇÕES -->
            <div class="mb-4">
                <h3 class="card-title">📝 Observações</h3>

                    <div class="form-group">
                    <label for="observacoes">Notas Adicionais</label>
                    <textarea
                        id="observacoes"
                        name="observacoes"
                        class="form-control"
                        placeholder="Digite aqui qualquer informação adicional sobre o cliente..."
                        class="textarea-large"
                    ><?php echo $is_edit ? htmlspecialchars($cliente['observacoes'] ?? '') : ''; ?></textarea>
                </div>
            </div>

            <!-- AÇÕES -->
            <div class="d-flex gap-2 mt-4 pt-4 border-top">
                <button type="submit" class="btn btn-primary">
                    <?php echo $is_edit ? '✓ Atualizar Cliente' : '✓ Cadastrar Cliente'; ?>
                </button>
                <a href="<?php echo BASE_URL; ?>clientes" class="btn btn-secondary">
                    ✕ Cancelar
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Scripts para Máscara e Verificação -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>

<script>
$(document).ready(function() {
    const isEdit = <?php echo $is_edit ? 'true' : 'false'; ?>;
    const clienteId = <?php echo $is_edit ? $cliente['id'] : 'null'; ?>;

    // Função para aplicar máscara baseada no tamanho
    function aplicarMascara() {
        let val = $('#documento').val().replace(/\D/g, '');
        if (val.length <= 11) {
            $('#documento').mask('000.000.000-009', {reverse: true});
        } else {
            $('#documento').mask('00.000.000/0000-00', {reverse: true});
        }
    }

    $('#documento').on('input', function() {
        aplicarMascara();
    });

    // Aplicar máscara inicial
    aplicarMascara();

    // Verificação de duplicidade (apenas no cadastro)
    if (!isEdit) {
        $('#documento').on('blur', function() {
            const documento = $(this).val().replace(/\D/g, '');
            if (documento.length >= 11) {
                $.get('<?php echo BASE_URL; ?>clientes/verificar-documento', { documento: documento }, function(data) {
                    if (data.exists) {
                        $('#documento').addClass('is-invalid');
                        $('#documento-feedback').show();
                        
                        // Pequeno delay para o usuário ver a mensagem antes de redirecionar
                        setTimeout(function() {
                            window.location.href = '<?php echo BASE_URL; ?>clientes/view?id=' + data.id;
                        }, 1500);
                    } else {
                        $('#documento').removeClass('is-invalid');
                        $('#documento-feedback').hide();
                    }
                });
            }
        });
    }

    // Máscaras para telefones
    const maskBehavior = function (val) {
        return val.replace(/\D/g, '').length === 11 ? '(00) 00000-0000' : '(00) 0000-00009';
    };
    const options = {
        onKeyPress: function(val, e, field, options) {
            field.mask(maskBehavior.apply({}, arguments), options);
        }
    };
    $('#telefone_principal, #telefone_secundario').mask(maskBehavior, options);
});
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
