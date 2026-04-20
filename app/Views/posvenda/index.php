<?php
$current_page = 'pos_venda';
require_once __DIR__ . '/../layout/main.php';
?>
<div class="container">
    <div class="d-flex justify-between align-center mb-4">
        <h1>Pós-Venda</h1>
        <div class="d-flex gap-2">
            <a href="<?php echo BASE_URL; ?>crm" class="btn btn-primary btn-sm">🚀 Ir para CRM Avançado</a>
            <a href="<?php echo BASE_URL; ?>dashboard" class="btn btn-secondary btn-sm">← Dashboard</a>
        </div>
    </div>

    <div class="card">
        <h3 class="card-title">Entregas para acompanhar</h3>
        <?php if (empty($itens)): ?>
            <p class="text-muted">Nenhuma OS elegível para pós-venda no momento.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>OS</th>
                            <th>Cliente</th>
                            <th>Dias desde entrega</th>
                            <th>Última atualização</th>
                            <th>Ações</th>
                            <th>Registrar resposta</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($itens as $a): ?>
                            <?php 
                                $telefone = preg_replace('/\D+/', '', $a['cliente_telefone'] ?? '');
                                $msg = "Olá {$a['cliente_nome']}, tudo bem? Sobre a OS #{$a['os_id']}, gostaríamos de saber como está o equipamento e sua experiência. Seu feedback é importante.";
                                $wa = $telefone ? "https://wa.me/55{$telefone}?text=" . urlencode($msg) : '#';
                            ?>
                            <tr>
                                <td>#<?php echo (int)$a['os_id']; ?></td>
                                <td><?php echo htmlspecialchars($a['cliente_nome'] ?? ''); ?></td>
                                <td><?php echo (int)($a['dias'] ?? 0); ?></td>
                                <td><?php echo isset($a['ultima_atualizacao']) ? date('d/m/Y H:i', strtotime($a['ultima_atualizacao'])) : '--'; ?></td>
                                <td>
                                    <a href="<?php echo BASE_URL; ?>ordens/view?id=<?php echo (int)$a['os_id']; ?>" class="btn btn-outline-primary btn-sm">Ver OS</a>
                                    <?php if ($telefone): ?>
                                        <a href="<?php echo $wa; ?>" target="_blank" class="btn btn-success btn-sm">WhatsApp</a>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <form action="<?php echo BASE_URL; ?>pos-venda/registrar-resposta" method="POST" class="d-flex flex-column gap-2">
                                        <input type="hidden" name="os_id" value="<?php echo (int)$a['os_id']; ?>">
                                        
                                        <div class="d-flex gap-2 align-items-center">
                                            <label class="small mb-0">Nota:</label>
                                            <select name="nota" class="form-control form-control-sm" style="width: 70px;" required>
                                                <option value="">--</option>
                                                <option value="5">5 ⭐</option>
                                                <option value="4">4 ⭐</option>
                                                <option value="3">3 ⭐</option>
                                                <option value="2">2 ⭐</option>
                                                <option value="1">1 ⭐</option>
                                            </select>
                                        </div>

                                        <div class="d-flex gap-2">
                                            <input type="text" name="resumo" class="form-control form-control-sm" placeholder="Resumo do feedback" required>
                                            <button type="submit" class="btn btn-primary btn-sm">Salvar</button>
                                        </div>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
