<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="mb-0"><?php echo htmlspecialchars($title ?? 'Despesas'); ?></h1>
        <a href="<?php echo BASE_URL; ?>despesas/form" class="btn btn-primary">Nova Despesa</a>
    </div>
    <form class="row g-2 mb-3" method="get" action="<?php echo BASE_URL; ?>despesas">
        <div class="col-md-3">
            <input type="text" name="busca" class="form-control" placeholder="Buscar" value="<?php echo htmlspecialchars($busca ?? ''); ?>">
        </div>
        <div class="col-md-3">
            <select name="categoria_id" class="form-control">
                <option value="">Categoria</option>
                <?php foreach (($categorias ?? []) as $cat): ?>
                    <option value="<?php echo (int)$cat['id']; ?>" <?php echo isset($categoriaId) && (int)$categoriaId === (int)$cat['id'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($cat['nome']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2">
            <select name="status" class="form-control">
                <option value="">Status</option>
                <?php
                    $statuses = ['pendente' => 'Pendente','pago' => 'Pago','parcial' => 'Parcial'];
                    $selStatus = $status ?? '';
                    foreach ($statuses as $k => $v):
                ?>
                <option value="<?php echo $k; ?>" <?php echo $selStatus === $k ? 'selected' : ''; ?>><?php echo $v; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2">
            <select name="metodo" class="form-control">
                <option value="">Método</option>
                <?php
                    $metodos = ['dinheiro'=>'Dinheiro','cartao'=>'Cartão','pix'=>'Pix','transferencia'=>'Transferência','boleto'=>'Boleto','outro'=>'Outro'];
                    $selMetodo = $metodo ?? '';
                    foreach ($metodos as $k => $v):
                ?>
                <option value="<?php echo $k; ?>" <?php echo $selMetodo === $k ? 'selected' : ''; ?>><?php echo $v; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2">
            <button class="btn btn-secondary w-100" type="submit">Filtrar</button>
        </div>
    </form>
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Data</th>
                    <th>Categoria</th>
                    <th>Descrição</th>
                    <th>Método</th>
                    <th>Status</th>
                    <th class="text-end">Valor</th>
                    <th class="text-end">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($despesas)): ?>
                    <?php foreach ($despesas as $d): ?>
                        <tr>
                            <td><?php echo (int)$d['id']; ?></td>
                            <td><?php echo htmlspecialchars($d['data_despesa']); ?></td>
                            <td><?php echo htmlspecialchars($d['categoria_nome'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($d['descricao']); ?></td>
                            <td><?php echo htmlspecialchars($d['metodo_pagamento']); ?></td>
                            <td><?php echo htmlspecialchars($d['status_pagamento']); ?></td>
                            <td class="text-end"><?php echo number_format((float)$d['valor'], 2, ',', '.'); ?></td>
                            <td class="text-end">
                                <a href="<?php echo BASE_URL; ?>despesas/form?id=<?php echo (int)$d['id']; ?>" class="btn btn-sm btn-outline-primary">Editar</a>
                                <form method="post" action="<?php echo BASE_URL; ?>despesas/deletar" class="d-inline">
                                    <input type="hidden" name="id" value="<?php echo (int)$d['id']; ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Remover</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="8">Nenhuma despesa encontrada</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <nav>
        <ul class="pagination">
            <?php
                $pa = (int)($paginaAtual ?? 1);
                $tp = (int)($totalPaginas ?? 1);
                for ($p = 1; $p <= $tp; $p++):
                    $qs = $_GET;
                    $qs['pagina'] = $p;
                    $url = BASE_URL . 'despesas?' . http_build_query($qs);
            ?>
                <li class="page-item <?php echo $p === $pa ? 'active' : ''; ?>">
                    <a class="page-link" href="<?php echo $url; ?>"><?php echo $p; ?></a>
                </li>
            <?php endfor; ?>
        </ul>
    </nav>
</div>
