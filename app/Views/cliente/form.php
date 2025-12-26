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
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
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
            <div style="margin-bottom: 2rem;">
                <h3 style="color: var(--primary-red); margin-bottom: 1.5rem; padding-bottom: 0.5rem; border-bottom: 2px solid var(--primary-red);">
                    📋 Informações Pessoais
                </h3>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem;">
                    <div class="form-group">
                        <label for="nome_completo">Nome Completo *</label>
                        <input
                            type="text"
                            id="nome_completo"
                            name="nome_completo"
                            placeholder="Digite o nome completo"
                            value="<?php echo $is_edit ? htmlspecialchars($cliente['nome_completo'] ?? '') : ''; ?>"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label for="tipo_pessoa">Tipo de Pessoa</label>
                        <select id="tipo_pessoa" name="tipo_pessoa">
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
                            placeholder="000.000.000-00"
                            value="<?php echo $is_edit ? htmlspecialchars($cliente['documento'] ?? '') : ''; ?>"
                        >
                    </div>

                    <div class="form-group">
                        <label for="data_nascimento">Data de Nascimento</label>
                        <input
                            type="date"
                            id="data_nascimento"
                            name="data_nascimento"
                            value="<?php echo $is_edit ? htmlspecialchars($cliente['data_nascimento'] ?? '') : ''; ?>"
                        >
                    </div>
                </div>
            </div>

            <!-- SEÇÃO: CONTATO -->
            <div style="margin-bottom: 2rem;">
                <h3 style="color: var(--primary-red); margin-bottom: 1.5rem; padding-bottom: 0.5rem; border-bottom: 2px solid var(--primary-red);">
                    📞 Contato
                </h3>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem;">
                    <div class="form-group">
                        <label for="email">E-mail</label>
                        <input
                            type="email"
                            id="email"
                            name="email"
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
                            placeholder="(11) 98765-4321"
                            value="<?php echo $is_edit ? htmlspecialchars($cliente['telefone_secundario'] ?? '') : ''; ?>"
                        >
                    </div>
                </div>
            </div>

            <!-- SEÇÃO: ENDEREÇO -->
            <div style="margin-bottom: 2rem;">
                <h3 style="color: var(--primary-red); margin-bottom: 1.5rem; padding-bottom: 0.5rem; border-bottom: 2px solid var(--primary-red);">
                    🏠 Endereço
                </h3>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem;">
                    <div class="form-group" style="grid-column: 1 / -1;">
                        <label for="endereco_logradouro">Logradouro</label>
                        <input
                            type="text"
                            id="endereco_logradouro"
                            name="endereco_logradouro"
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
                            placeholder="Cidade"
                            value="<?php echo $is_edit ? htmlspecialchars($cliente['endereco_cidade'] ?? '') : ''; ?>"
                        >
                    </div>
                </div>
            </div>

            <!-- SEÇÃO: OBSERVAÇÕES -->
            <div style="margin-bottom: 2rem;">
                <h3 style="color: var(--primary-red); margin-bottom: 1.5rem; padding-bottom: 0.5rem; border-bottom: 2px solid var(--primary-red);">
                    📝 Observações
                </h3>

                <div class="form-group">
                    <label for="observacoes">Notas Adicionais</label>
                    <textarea
                        id="observacoes"
                        name="observacoes"
                        placeholder="Digite aqui qualquer informação adicional sobre o cliente..."
                        style="min-height: 120px;"
                    ><?php echo $is_edit ? htmlspecialchars($cliente['observacoes'] ?? '') : ''; ?></textarea>
                </div>
            </div>

            <!-- AÇÕES -->
            <div style="display: flex; gap: 1rem; margin-top: 2rem; padding-top: 2rem; border-top: 1px solid var(--dark-tertiary);">
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

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
