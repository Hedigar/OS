<?php
// Inclui o layout principal
require_once __DIR__ . '/../layout/main.php';
?>

<div class="container mt-5">
    <h2><?= $title ?></h2>
    <a href="<?= BASE_URL ?>configuracoes/index" class="btn btn-primary mb-3">Configurações</a>


    <?php if (empty($configuracoes)): ?>
        <div class="alert alert-info">Nenhuma configuração cadastrada.</div>

    
    <?php else: ?>
        <!-- Add your configuration display code here -->
    <?php endif; ?>  

</div>