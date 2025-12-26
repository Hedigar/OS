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
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h1>👤 Cliente: <?php echo htmlspecialchars($cliente['nome_completo'] ?? 'Cliente Não Encontrado'); ?></h1>
        <div>
            <a href="<?php echo BASE_URL; ?>clientes" class="btn btn-secondary btn-sm" style="margin-right: 10px;">← Voltar</a>
            <a href="<?php echo BASE_URL; ?>clientes/editar?id=<?php echo $cliente_id; ?>" class="btn btn-primary btn-sm" style="margin-right: 10px;">✏️ Editar Cliente</a>
            <a href="<?php echo BASE_URL; ?>ordens/form?cliente_id=<?php echo $cliente_id; ?>&cliente_nome=<?php echo $cliente_nome; ?>&cliente_documento=<?php echo $cliente_documento; ?>" class="btn btn-success btn-sm">➕ Nova OS</a>
        </div>
    </div>

    <!-- SEÇÃO: DADOS DO CLIENTE -->
    <div class="card" style="margin-bottom: 2rem;">
        <h3 style="color: var(--primary-red); margin-bottom: 1.5rem; padding-bottom: 0.5rem; border-bottom: 2px solid var(--primary-red);">
            Informações Pessoais
        </h3>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem;">
            <p><strong>Tipo de Pessoa:</strong> <?php echo htmlspecialchars($cliente['tipo_pessoa'] ?? 'N/A'); ?></p>
            <p><strong>Documento (CPF/CNPJ):</strong> <?php echo htmlspecialchars($cliente['documento'] ?? 'N/A'); ?></p>
            <p><strong>E-mail:</strong> <?php echo htmlspecialchars($cliente['email'] ?? 'N/A'); ?></p>
            <p><strong>Telefone Principal:</strong> <?php echo htmlspecialchars($cliente['telefone_principal'] ?? 'N/A'); ?></p>
            <p><strong>Telefone Secundário:</strong> <?php echo htmlspecialchars($cliente['telefone_secundario'] ?? 'N/A'); ?></p>
            <p><strong>Data de Nascimento:</strong> <?php echo htmlspecialchars($cliente['data_nascimento'] ?? 'N/A'); ?></p>
        </div>
        <h4 style="margin-top: 1.5rem; margin-bottom: 1rem; border-bottom: 1px solid var(--dark-tertiary); padding-bottom: 5px;">Endereço</h4>
        <p><strong>Logradouro:</strong> <?php echo htmlspecialchars($cliente['endereco_logradouro'] ?? 'N/A'); ?>, Nº <?php echo htmlspecialchars($cliente['endereco_numero'] ?? 'S/N'); ?></p>
        <p><strong>Bairro:</strong> <?php echo htmlspecialchars($cliente['endereco_bairro'] ?? 'N/A'); ?></p>
        <p><strong>Cidade:</strong> <?php echo htmlspecialchars($cliente['endereco_cidade'] ?? 'N/A'); ?></p>
        <h4 style="margin-top: 1.5rem; margin-bottom: 1rem; border-bottom: 1px solid var(--dark-tertiary); padding-bottom: 5px;">Observações</h4>
        <p><?php echo nl2br(htmlspecialchars($cliente['observacoes'] ?? 'Nenhuma observação.')); ?></p>
    </div>

    <!-- SEÇÃO: EQUIPAMENTOS CADASTRADOS (Baseado no histórico de OS) -->
    <div class="card" style="margin-bottom: 2rem;">
        <h3 style="color: var(--primary-red); margin-bottom: 1.5rem; padding-bottom: 0.5rem; border-bottom: 2px solid var(--primary-red);">
            💻 Equipamentos Registrados (Histórico)
        </h3>
        <?php if (!empty($equipamentos)): ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>OS #</th>
                        <th>Tipo</th>
                        <th>Marca/Modelo</th>
                        <th>Serial</th>
                        <th>Defeito Relatado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($equipamentos as $equipamento): ?>
                        <tr>
                            <td><a href="<?php echo BASE_URL; ?>ordens/view?id=<?php echo $equipamento['os_id']; ?>">#<?php echo htmlspecialchars($equipamento['os_id']); ?></a></td>
                            <td><?php echo htmlspecialchars($equipamento['tipo_equipamento'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($equipamento['marca_equipamento'] ?? 'N/A'); ?> / <?php echo htmlspecialchars($equipamento['modelo_equipamento'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($equipamento['serial_equipamento'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars(substr($equipamento['defeito_relatado'] ?? 'N/A', 0, 50)) . '...'; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Nenhum equipamento registrado no histórico de Ordens de Serviço para este cliente.</p>
        <?php endif; ?>
    </div>

    <!-- SEÇÃO: HISTÓRICO DE ORDENS DE SERVIÇO -->
    <div class="card">
        <h3 style="color: var(--primary-red); margin-bottom: 1.5rem; padding-bottom: 0.5rem; border-bottom: 2px solid var(--primary-red);">
            📜 Histórico de Ordens de Serviço
        </h3>
        <?php if (!empty($historicoOS)): ?>
            <table class="table">
                <thead>
                    <tr>
                        <th># OS</th>
                        <th>Data Abertura</th>
                        <th>Status</th>
                        <th>Defeito Relatado</th>
                        <th>Valor Total</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($historicoOS as $os): ?>
                        <tr>
                            <td>#<?php echo htmlspecialchars($os['id']); ?></td>
                            <td><?php echo date('d/m/Y', strtotime($os['created_at'])); ?></td>
                            <td><span class="badge" style="background-color: <?php echo htmlspecialchars($os['status_cor'] ?? '#000'); ?>;"><?php echo htmlspecialchars($os['status_nome'] ?? 'N/A'); ?></span></td>
                            <td><?php echo htmlspecialchars(substr($os['defeito_relatado'] ?? 'N/A', 0, 80)) . '...'; ?></td>
                            <td>R$ <?php echo number_format($os['valor_total_os'] ?? 0, 2, ',', '.'); ?></td>
                            <td>
                                <a href="<?php echo BASE_URL; ?>ordens/view?id=<?php echo $os['id']; ?>" class="btn btn-info btn-xs">Ver</a>
                                <a href="<?php echo BASE_URL; ?>ordens/print?id=<?php echo $os['id']; ?>" target="_blank" class="btn btn-secondary btn-xs">Imprimir</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Nenhuma Ordem de Serviço encontrada para este cliente.</p>
        <?php endif; ?>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        const newOsId = urlParams.get('new_os_id');

        if (newOsId) {
            // Remove o parâmetro 'new_os_id' da URL para evitar que abra novamente ao recarregar
            urlParams.delete('new_os_id');
            const newUrl = window.location.pathname + (urlParams.toString() ? '?' + urlParams.toString() : '');
            history.replaceState(null, '', newUrl);

            // Abre a versão para impressão em uma nova aba
            const printUrl = `<?php echo BASE_URL; ?>ordens/print?id=${newOsId}`;
            window.open(printUrl, '_blank');
        }
    });
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
