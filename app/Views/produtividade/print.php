<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($title ?? 'Relatório'); ?></title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        h1 { font-size: 18px; margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ccc; padding: 6px; text-align: left; }
        .muted { color: #666; }
    </style>
    </head>
<body>
    <h1>Produtividade - Diário</h1>
    <p class="muted">Data: <?php echo date('d/m/Y'); ?> • Usuário: <?php echo htmlspecialchars($user['nome'] ?? ''); ?></p>
    <table>
        <thead>
            <tr>
                <th>Horário</th>
                <th>Tipo</th>
                <th>Descrição</th>
                <th>Categoria</th>
                <th>Tempo (min)</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach (($dia['atividades'] ?? []) as $a): ?>
            <tr>
                <td><?php echo date('H:i', strtotime($a['data_hora'])); ?></td>
                <td><?php echo htmlspecialchars($a['tipo']); ?></td>
                <td><?php echo htmlspecialchars($a['descricao'] ?? ''); ?></td>
                <td><?php echo ucfirst($a['categoria']); ?></td>
                <td><?php echo (int)$a['tempo_minutos']; ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="4">Total</th>
                <th><?php echo (int)($dia['total'] ?? 0); ?></th>
            </tr>
        </tfoot>
    </table>
</body>
</html>
