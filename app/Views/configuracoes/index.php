<?php
// Inclui o layout principal
require_once __DIR__ . '/../layout/main.php';
?>

<div class="container mt-5">
    <h2><?= $title ?></h2>
    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body text-center">
                    <div class="display-4 mb-3 text-primary">
                        <i class="fas fa-boxes"></i>
                    </div>
                    <h5 class="card-title">Ajustes de Produtos e Serviços</h5>
                    <p class="card-text text-muted">Gerencie peças, mão de obra e margens de lucro.</p>
                    <a href="<?= BASE_URL ?>configuracoes/produtos-servicos" class="btn btn-outline-primary">Configurar Itens</a>
                </div>
            </div>
        </div>
        
        <!-- Outros cards de configuração podem ser adicionados aqui -->
    </div>

</div>