<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>🏠 Atendimentos Externos</h1>
        <a href="<?php echo BASE_URL; ?>atendimentos-externos/form" class="btn btn-primary">
            <i class="fas fa-plus"></i> Novo Atendimento
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="<?php echo BASE_URL; ?>atendimentos-externos" method="GET" class="mb-3">
                <div class="input-group">
                    <input type="text" name="busca" class="form-control" placeholder="Buscar por cliente ou problema..." value="<?php echo htmlspecialchars($busca ?? ''); ?>">
                    <button class="btn btn-outline-secondary" type="submit">Buscar</button>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Cliente</th>
                            <th>Data Agendada</th>
                            <th>Status</th>
                            <th>Valor</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($atendimentos)): ?>
                            <tr>
                                <td colspan="6" class="text-center">Nenhum atendimento encontrado.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($atendimentos as $atendimento): ?>
                                <tr>
                                    <td>#<?php echo $atendimento['id']; ?></td>
                                    <td><?php echo htmlspecialchars($atendimento['cliente_nome']); ?></td>
                                    <td><?php echo $atendimento['data_agendada'] ? date('d/m/Y H:i', strtotime($atendimento['data_agendada'])) : 'Não agendado'; ?></td>
                                    <td>
                                        <span class="badge bg-<?php 
                                            echo match($atendimento['status']) {
                                                'pendente' => 'warning',
                                                'agendado' => 'info',
                                                'em_deslocamento' => 'primary',
                                                'concluido' => 'success',
                                                'cancelado' => 'danger',
                                                default => 'secondary'
                                            };
                                        ?>">
                                            <?php echo ucfirst($atendimento['status']); ?>
                                        </span>
                                    </td>
                                    <td>R$ <?php echo number_format($atendimento['valor_deslocamento'], 2, ',', '.'); ?></td>
                                    <td>
                                        <a href="<?php echo BASE_URL; ?>atendimentos-externos/form?id=<?php echo $atendimento['id']; ?>" class="btn btn-sm btn-info">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="<?php echo BASE_URL; ?>atendimentos-externos/deletar" method="POST" class="d-inline" onsubmit="return confirm('Tem certeza que deseja excluir este atendimento?')">
                                            <input type="hidden" name="id" value="<?php echo $atendimento['id']; ?>">
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Paginação -->
            <?php if ($totalPaginas > 1): ?>
                <nav>
                    <ul class="pagination justify-content-center">
                        <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                            <li class="page-item <?php echo ($paginaAtual == $i) ? 'active' : ''; ?>">
                                <a class="page-link" href="<?php echo BASE_URL; ?>atendimentos-externos?pagina=<?php echo $i; ?>&busca=<?php echo urlencode($busca ?? ''); ?>">
                                    <?php echo $i; ?>
                                </a>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </nav>
            <?php endif; ?>
        </div>
    </div>
</div>
