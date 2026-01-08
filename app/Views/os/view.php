<?php
$current_page = 'ordens';
require_once __DIR__ . '/../layout/main.php';

$ordem = $ordem ?? [];
$itens = $itens ?? [];
$historico = $historico ?? [];
$statuses = $statuses ?? [];

// Função auxiliar para formatar moeda
if (!function_exists('formatCurrency')) {
    function formatCurrency($value) {
        return 'R$ ' . number_format($value, 2, ',', '.');
    }
}

// Helpers seguros para evitar warnings/deprecations ao acessar chaves
if (!function_exists('safe_text')) {
    function safe_text(array $arr, string $key, string $default = ''): string
    {
        $val = $arr[$key] ?? $default;
        return htmlspecialchars((string)$val, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('safe_val')) {
    function safe_val(array $arr, string $key, $default = null)
    {
        return $arr[$key] ?? $default;
    }
}
?>

<div class="container">
    <div class="d-flex justify-between align-center mb-4 flex-wrap">
        <h1>📋 Ordem de Serviço #<?php echo safe_text($ordem, 'id', 'N/A'); ?></h1>
        <div class="d-flex gap-2 flex-wrap">
            <a href="<?php echo BASE_URL; ?>ordens/form?id=<?php echo safe_text($ordem, 'id', ''); ?>" class="btn btn-info">
                ✏️ Editar OS
            </a>
            <a href="<?php echo BASE_URL; ?>ordens/print-receipt?id=<?php echo safe_text($ordem, 'id', ''); ?>" target="_blank" class="btn btn-primary">
                🖨️ Imprimir OS
            </a>
            <a href="<?php echo BASE_URL; ?>ordens/print-estimate?id=<?php echo safe_text($ordem, 'id', ''); ?>" target="_blank" class="btn btn-success">
                💲 Imprimir Orçamento
            </a>
            <a href="<?php echo BASE_URL; ?>ordens" class="btn btn-secondary">
                ← Voltar
            </a>
        </div>
    </div>

    <!-- CARD DE DETALHES GERAIS -->
    <div class="card mb-4">
        <h2 class="card-title">Detalhes Gerais</h2>
        <div class="form-grid">
            <!-- Cliente -->
            <div>
                <h3 class="mb-2">👥 Cliente</h3>
                <p class="m-0"><?php echo safe_text($ordem, 'cliente_nome', 'N/A'); ?></p>
            </div>

            <!-- Status -->
            <div>
                <h3 class="mb-2">✅ Status Atual</h3>
                <?php $status_cor = safe_text($ordem, 'status_cor', '#777'); ?>
                <span class="badge" style="background-color: <?php echo $status_cor; ?>; color: #fff;">
                    <?php echo safe_text($ordem, 'status_nome', '—'); ?>
                </span>
            </div>

            <!-- Status Pagamento -->
            <div>
                <h3 class="mb-2">💰 Pagamento</h3>
                <?php 
                    $status_pag = safe_text($ordem, 'status_pagamento', 'pendente');
                    $pag_cor = ($status_pag === 'pago') ? '#2ecc71' : (($status_pag === 'parcial') ? '#f1c40f' : '#e74c3c');
                    $pag_label = ($status_pag === 'pago') ? 'Pago' : (($status_pag === 'parcial') ? 'Parcial' : 'Pendente');
                ?>
                <span class="badge" style="background-color: <?php echo $pag_cor; ?>; color: #fff;">
                    <?php echo $pag_label; ?>
                </span>
            </div>

            <!-- Status Entrega -->
            <div>
                <h3 class="mb-2">📦 Entrega</h3>
                <?php 
                    $status_ent = safe_text($ordem, 'status_entrega', 'nao_entregue');
                    $ent_cor = ($status_ent === 'entregue') ? '#2ecc71' : '#e74c3c';
                    $ent_label = ($status_ent === 'entregue') ? 'Entregue' : 'Não Entregue';
                ?>
                <span class="badge" style="background-color: <?php echo $ent_cor; ?>; color: #fff;">
                    <?php echo $ent_label; ?>
                </span>
            </div>

            <!-- Data de Abertura -->
            <div>
                <h3 class="mb-2">📅 Abertura</h3>
                <?php
                    $dataAbert = safe_val($ordem, 'data_abertura', null) ?: safe_val($ordem, 'created_at', null);
                    if (!empty($dataAbert) && strtotime((string)$dataAbert) !== false) {
                        echo '<p class="m-0">' . date('d/m/Y H:i', strtotime((string)$dataAbert)) . '</p>';
                    } else {
                        echo '<p class="m-0">—</p>';
                    }
                ?>
            </div>
        </div>
    </div>

    <!-- CARD DE EQUIPAMENTO -->
    <div class="card mb-4">
        <h2 class="card-title">💻 Detalhes do Equipamento</h2>
        <div class="form-grid">
            <div>
                <h3 class="mb-2">Equipamento</h3>
                <?php
                    $equipDisplay = trim((safe_text($ordem, 'equipamento_tipo', '') . ' ' . safe_text($ordem, 'equipamento_marca', '') . ' ' . safe_text($ordem, 'equipamento_modelo', '')));
                    if ($equipDisplay === '') $equipDisplay = 'N/A';
                ?>
                <p class="m-0"><?php echo $equipDisplay; ?></p>
            </div>
            <div>
                <h3 class="mb-2">Tipo / Marca / Modelo</h3>
                <p class="m-0"><?php echo safe_text($ordem, 'equipamento_tipo', 'N/A'); ?> / <?php echo safe_text($ordem, 'equipamento_marca', 'N/A'); ?> / <?php echo safe_text($ordem, 'equipamento_modelo', 'N/A'); ?></p>
            </div>
            <div>
                <h3 class="mb-2">Serial / Senha</h3>
                <p class="m-0"><?php echo safe_text($ordem, 'equipamento_serial', 'N/A'); ?> / <?php echo safe_text($ordem, 'equipamento_senha', 'N/A'); ?></p>
            </div>
            <div>
                <h3 class="mb-2">Fonte / SN Fonte / Acessórios</h3>
                <?php $fonte = safe_val($ordem, 'equipamento_fonte', null); ?>
                <p class="m-0">
                    <?php echo ($fonte === 1 || $fonte === '1' || $fonte === 'sim') ? 'Deixou' : 'Não Deixou'; ?> 
                    <?php echo !empty($ordem['equipamento_sn_fonte']) ? ' (SN: ' . safe_text($ordem, 'equipamento_sn_fonte', '') . ')' : ''; ?>
                    / <?php echo safe_text($ordem, 'equipamento_acessorios', 'Nenhum'); ?>
                </p>
            </div>
        </div>
    </div>

    <!-- CARD DE PROBLEMA E LAUDO -->
    <div class="card mb-4">
        <h2 class="card-title">Laudo e Status</h2>
        <form action="<?php echo BASE_URL; ?>ordens/atualizar" method="POST">
            <input type="hidden" name="id" value="<?php echo $ordem['id']; ?>">
            
            <div class="form-grid">
                <div class="form-group">
                    <h3 class="mb-2">Defeito Relatado (Recepção)</h3>
                    <?php 
                        $isAdmin = (isset($_SESSION['usuario_nivel']) && $_SESSION['usuario_nivel'] === 'admin');
                    ?>
                    <textarea name="defeito" class="form-control" style="min-height: 120px;" <?php echo (!$isAdmin) ? 'readonly' : ''; ?>><?php echo safe_text($ordem, 'defeito_relatado', safe_text($ordem, 'defeito', '')); ?></textarea>
                </div>
                <div class="form-group">
                    <h3 class="mb-2">Laudo Técnico / Solução</h3>
                    <textarea name="laudo_tecnico" class="form-control" style="min-height: 120px;" placeholder="Digite aqui o laudo técnico..."><?php echo safe_text($ordem, 'laudo_tecnico', ''); ?></textarea>
                </div>
            </div>

            <div class="form-grid mt-4">
                <div class="form-group">
                    <h3 class="mb-2">Alterar Status</h3>
                    <select name="status_id" class="form-control">
                        <?php foreach ($statuses as $st): ?>
                            <option value="<?php echo $st['id']; ?>" <?php echo ($st['id'] == $ordem['status_atual_id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($st['nome']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <h3 class="mb-2">Status de Pagamento</h3>
                    <select name="status_pagamento" class="form-control">
                        <option value="pendente" <?php echo (safe_text($ordem, 'status_pagamento') === 'pendente') ? 'selected' : ''; ?>>Pendente</option>
                        <option value="parcial" <?php echo (safe_text($ordem, 'status_pagamento') === 'parcial') ? 'selected' : ''; ?>>Parcial</option>
                        <option value="pago" <?php echo (safe_text($ordem, 'status_pagamento') === 'pago') ? 'selected' : ''; ?>>Pago</option>
                    </select>
                </div>
                <div class="form-group">
                    <h3 class="mb-2">Status de Entrega</h3>
                    <select name="status_entrega" class="form-control">
                        <option value="nao_entregue" <?php echo (safe_text($ordem, 'status_entrega') === 'nao_entregue') ? 'selected' : ''; ?>>Não Entregue</option>
                        <option value="entregue" <?php echo (safe_text($ordem, 'status_entrega') === 'entregue') ? 'selected' : ''; ?>>Entregue</option>
                    </select>
                </div>
                <div class="form-group">
                    <h3 class="mb-2">Observação do Status (Histórico)</h3>
                    <input type="text" name="status_observacao" class="form-control" placeholder="Ex: Peça encomendada no Mercado Livre">
                </div>
            </div>

            <div class="mt-4 text-end">
                <button type="submit" class="btn btn-primary">💾 Salvar Alterações</button>
            </div>
        </form>
    </div>

    <!-- CARD DE HISTÓRICO DE STATUS -->
    <div class="card mb-4">
        <h2 class="card-title">📜 Histórico de Status</h2>
        <?php if (empty($historico)): ?>
            <div class="alert alert-info m-0" style="background-color: var(--bg-tertiary); color: var(--text-primary); border: 1px solid var(--border-color);">Nenhum histórico registrado.</div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Data/Hora</th>
                            <th>Status</th>
                            <th>Usuário</th>
                            <th>Observação</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($historico as $h): ?>
                            <tr>
                                <td><?php echo date('d/m/Y H:i', strtotime($h['created_at'])); ?></td>
                                <td>
                                    <span class="badge" style="background-color: <?php echo $h['status_cor']; ?>; color: #fff;">
                                        <?php echo htmlspecialchars($h['status_nome']); ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars($h['usuario_nome'] ?? 'Sistema'); ?></td>
                                <td><?php echo htmlspecialchars($h['observacao'] ?? '—'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
