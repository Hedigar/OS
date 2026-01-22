<div class="container mt-4">
    <h2><?= $title ?></h2>
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="<?= BASE_URL ?>auditoria">
                <div class="form-grid">
                    <div class="form-group">
                        <label>Usuário</label>
                        <select name="usuario_id" class="form-control">
                            <option value="">Todos</option>
                            <?php foreach (($usuarios ?? []) as $u): ?>
                                <?php $sel = ((int)($filters['usuario_id'] ?? 0) === (int)$u['id']) ? 'selected' : ''; ?>
                                <option value="<?= (int)$u['id'] ?>" <?= $sel ?>><?= htmlspecialchars($u['nome'] ?? ('Usuário #' . $u['id'])) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>De</label>
                        <input type="date" name="data_inicio" class="form-control" value="<?= htmlspecialchars($filters['data_inicio'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label>Até</label>
                        <input type="date" name="data_fim" class="form-control" value="<?= htmlspecialchars($filters['data_fim'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label>Ação contém</label>
                        <input type="text" name="acao" class="form-control" value="<?= htmlspecialchars($filters['acao'] ?? '') ?>" placeholder="ex: login, excluir, atualizar...">
                    </div>
                    <div class="form-group align-end">
                        <button type="submit" class="btn btn-primary">Filtrar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <?php if (empty($logs)): ?>
                <div class="alert alert-info m-0">Nenhum registro encontrado para os filtros selecionados.</div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Data</th>
                                <th>Usuário</th>
                                <th>Ação</th>
                                <th>Referência</th>
                                <th>IP</th>
                                <th>Agente</th>
                                <th>Detalhes</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($logs as $l): ?>
                                <tr>
                                    <td><?= htmlspecialchars(date('d/m/Y H:i', strtotime($l['created_at']))) ?></td>
                                    <td><?= htmlspecialchars($l['usuario_nome'] ?? '—') ?></td>
                                    <td><?= htmlspecialchars($l['acao'] ?? '—') ?></td>
                                    <td><?= htmlspecialchars($l['referencia'] ?? '—') ?></td>
                                    <td><?= htmlspecialchars($l['ip_address'] ?? '—') ?></td>
                                    <td class="fs-sm"><?= htmlspecialchars($l['user_agent'] ?? '—') ?></td>
                                    <td>
                                        <?php
                                            $antes = $l['dados_anteriores'] ?? null;
                                            $novos = $l['dados_novos'] ?? null;
                                            $antesArr = $antes ? json_decode($antes, true) : null;
                                            $novosArr = $novos ? json_decode($novos, true) : null;
                                        ?>
                                        <?php if ($antesArr || $novosArr): ?>
                                            <details>
                                                <summary>Ver</summary>
                                                <div class="mt-2">
                                                    <?php if ($antesArr): ?>
                                                        <strong>Antes:</strong>
                                                        <pre class="bg-tertiary p-2 rounded"><?= htmlspecialchars(json_encode($antesArr, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) ?></pre>
                                                    <?php endif; ?>
                                                    <?php if ($novosArr): ?>
                                                        <strong>Depois:</strong>
                                                        <pre class="bg-tertiary p-2 rounded"><?= htmlspecialchars(json_encode($novosArr, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) ?></pre>
                                                    <?php endif; ?>
                                                </div>
                                            </details>
                                        <?php else: ?>
                                            —
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php
                    $page = (int)($filters['page'] ?? 1);
                    $totalPages = (int)($filters['total_pages'] ?? 1);
                    $usuarioId = $filters['usuario_id'] ?? '';
                    $dataInicio = $filters['data_inicio'] ?? '';
                    $dataFim = $filters['data_fim'] ?? '';
                    $acao = $filters['acao'] ?? '';
                ?>
                <div class="d-flex justify-between align-center mt-3">
                    <div>Total: <strong><?= (int)($filters['total'] ?? 0) ?></strong></div>
                    <div class="d-flex gap-2">
                        <?php if ($page > 1): ?>
                            <a class="btn btn-outline-secondary" href="<?= BASE_URL ?>auditoria?usuario_id=<?= urlencode((string)$usuarioId) ?>&data_inicio=<?= urlencode((string)$dataInicio) ?>&data_fim=<?= urlencode((string)$dataFim) ?>&acao=<?= urlencode((string)$acao) ?>&page=<?= $page - 1 ?>">Anterior</a>
                        <?php endif; ?>
                        <span>Página <?= $page ?> de <?= $totalPages ?></span>
                        <?php if ($page < $totalPages): ?>
                            <a class="btn btn-outline-secondary" href="<?= BASE_URL ?>auditoria?usuario_id=<?= urlencode((string)$usuarioId) ?>&data_inicio=<?= urlencode((string)$dataInicio) ?>&data_fim=<?= urlencode((string)$dataFim) ?>&acao=<?= urlencode((string)$acao) ?>&page=<?= $page + 1 ?>">Próxima</a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
