<?php
$current_page = 'clientes';
require_once __DIR__ . '/../layout/main.php';
?>

<div class="container">
    <!-- CABEÇALHO COM TÍTULO E BOTÃO -->
    <div class="d-flex justify-between align-center mb-4 flex-wrap">
        <h1><?php echo htmlspecialchars($title ?? 'Clientes'); ?></h1>
        <a href="<?php echo BASE_URL; ?>clientes/criar" class="btn btn-primary">
            ➕ Novo Cliente
        </a>
    </div>

    <!-- CARD DE BUSCA -->
    <div class="card mb-4">
        <form action="<?php echo BASE_URL; ?>clientes" method="GET">
            <div class="grid-search">
                <div class="form-group mb-0">
                    <label for="busca">🔍 Buscar Cliente</label>
                    <input
                        type="text"
                        id="busca"
                        name="busca"
                        class="form-control"
                        placeholder="Digite o nome ou CPF/CNPJ..."
                        value="<?php echo htmlspecialchars($busca ?? ''); ?>"
                    >
                </div>
                <button type="submit" class="btn btn-primary">Buscar</button>
                <?php if (!empty($busca)): ?>
                    <a href="<?php echo BASE_URL; ?>clientes" class="btn btn-secondary">Limpar</a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- LISTAGEM DE CLIENTES -->
    <?php if (empty($clientes)): ?>
        <div class="card">
            <div class="alert alert-info m-0">
                <span>ℹ️ Nenhum cliente encontrado. <a href="<?php echo BASE_URL; ?>clientes/criar" class="text-red">Criar novo cliente</a></span>
            </div>
        </div>
    <?php else: ?>
        <!-- TABELA RESPONSIVA -->
        <div class="overflow-x-auto">
            <table class="table">
                <thead>
                    <tr>
                        <th>👤 Nome</th>
                        <th>📄 Documento</th>
                        <th>📞 Telefone</th>
                        <th>📧 E-mail</th>
                        <th>⚙️ Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($clientes as $cliente): ?>
                        <tr>
                            <td>
                                <strong><?php echo htmlspecialchars($cliente['nome_completo']); ?></strong>
                            </td>
                            <td>
                                <?php echo htmlspecialchars($cliente['documento'] ?? 'N/A'); ?>
                            </td>
                            <td>
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
                            </td>
                            <td>
                                <span class="text-muted fs-sm">
                                    <?php echo htmlspecialchars($cliente['email'] ?? 'N/A'); ?>
                                </span>
                            </td>
                            <td>
                                <div class="d-flex gap-1 flex-wrap">
                                    <a href="<?php echo BASE_URL; ?>clientes/editar?id=<?php echo htmlspecialchars($cliente['id']); ?>" class="btn btn-info btn-sm" title="Editar Cliente">
                                        ✏️ Editar
                                    </a>
                                    <a href="<?php echo BASE_URL; ?>clientes/view?id=<?php echo htmlspecialchars($cliente['id']); ?>" class="btn btn-success btn-sm" title="Ver Ordens de Serviço">
                                        📋 Ordens
                                    </a>
                                    <form action="<?php echo BASE_URL; ?>clientes/deletar" method="POST" class="d-inline" onsubmit="return confirm('⚠️ Tem certeza que deseja deletar este cliente? Esta ação não pode ser desfeita.');">
                                        <input type="hidden" name="id" value="<?php echo htmlspecialchars($cliente['id']); ?>">
                                        <button type="submit" class="btn btn-danger btn-sm" title="Deletar Cliente">
                                            🗑️ Deletar
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php if (($totalPaginas ?? 0) > 1): ?>
            <div class="mt-4">
                <div class="pagination">
                    <?php 
                        $p_atual = $paginaAtual ?? 1;
                        $raio = 2; // Quantos números mostrar antes e depois da atual
                    ?>

                    <?php if ($p_atual > 1): ?>
                        <a href="<?php echo BASE_URL . 'clientes?pagina=' . ($p_atual - 1) . (!empty($busca) ? '&busca=' . urlencode($busca) : ''); ?>">«</a>
                    <?php endif; ?>

                    <?php if ($p_atual > ($raio + 1)): ?>
                        <a href="<?php echo BASE_URL . 'clientes?pagina=1' . (!empty($busca) ? '&busca=' . urlencode($busca) : ''); ?>">1</a>
                        <?php if ($p_atual > ($raio + 2)): ?>
                            <span class="gap" style="padding: 8px 14px;">...</span>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php 
                    $inicio = max(1, $p_atual - $raio);
                    $fim = min($totalPaginas, $p_atual + $raio);

                    for ($i = $inicio; $i <= $fim; $i++): 
                        $url_num = BASE_URL . 'clientes?pagina=' . $i . (!empty($busca) ? '&busca=' . urlencode($busca) : '');
                    ?>
                        <a href="<?php echo $url_num; ?>" class="<?php echo ($i == $p_atual) ? 'active' : ''; ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>

                    <?php if ($p_atual < ($totalPaginas - $raio)): ?>
                        <?php if ($p_atual < ($totalPaginas - $raio - 1)): ?>
                            <span class="gap" style="padding: 8px 14px;">...</span>
                        <?php endif; ?>
                        <a href="<?php echo BASE_URL . 'clientes?pagina=' . $totalPaginas . (!empty($busca) ? '&busca=' . urlencode($busca) : ''); ?>"><?php echo $totalPaginas; ?></a>
                    <?php endif; ?>

                    <?php if ($p_atual < $totalPaginas): ?>
                        <a href="<?php echo BASE_URL . 'clientes?pagina=' . ($p_atual + 1) . (!empty($busca) ? '&busca=' . urlencode($busca) : ''); ?>">»</a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

    <?php endif; // Fecha o if do empty($clientes) ?>
</div> <style>
.pagination { display: flex; justify-content: center; gap: 5px; flex-wrap: wrap; margin-bottom: 20px; }
.pagination a { padding: 8px 14px; border: 1px solid #ddd; border-radius: 4px; text-decoration: none; color: #007bff; }
.pagination a.active { background-color: #007bff; color: white; border-color: #007bff; font-weight: bold; }
.pagination a:hover:not(.active) { background-color: #f8f9fa; }
</style>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
