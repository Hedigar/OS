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
                        <i class="fas fa-file-invoice"></i>
                    </div>
                    <h5 class="card-title">Configurações de OS</h5>
                    <p class="card-text text-muted">Ajuste fontes, termos de garantia e visibilidade de campos na impressão.</p>
                    <a href="<?= BASE_URL ?>configuracoes/os" class="btn btn-outline-primary">Configurar Impressão</a>
                </div>
            </div>
        </div>

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
        
        <div class="col-md-4 mb-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body text-center">
                    <div class="display-4 mb-3 text-primary">
                        <i class="fas fa-users"></i>
                    </div>
                    <h5 class="card-title">Usuários</h5>
                    <p class="card-text text-muted">Gerencie usuários, permissões e redefinições de senha.</p>
                    <a href="<?= BASE_URL ?>usuarios" class="btn btn-outline-primary">Gerenciar Usuários</a>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body text-center">
                    <div class="display-4 mb-3 text-primary">
                        <i class="fas fa-credit-card"></i>
                    </div>
                    <h5 class="card-title">Pagamentos</h5>
                    <p class="card-text text-muted">Configure máquinas, bandeiras e taxas para registro de pagamentos.</p>
                    <a href="<?= BASE_URL ?>configuracoes/pagamentos" class="btn btn-outline-primary">Configurar Pagamentos</a>
                </div>
            </div>
        </div>

        <!-- Outros cards de configuração podem ser adicionados aqui -->
    </div>

</div>
