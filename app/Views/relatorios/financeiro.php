<?php
$current_page = 'relatorios';
require_once __DIR__ . '/../layout/main.php';
?>

<div class="container">
    <h1 class="mb-4">Relatórios Financeiros</h1>
    
    <div class="row">
        <div class="col-md-6 mb-4">
            <a href="<?= BASE_URL ?>relatorios/competencia" class="card text-decoration-none">
                <div class="card-body">
                    <h5 class="card-title text-success">
                        <i class="fas fa-industry"></i> Visão de Competência (Produção)
                    </h5>
                    <p class="card-text text-muted">Relatório de produção finalizada no período</p>
                </div>
            </a>
        </div>

        <div class="col-md-6 mb-4">
            <a href="<?= BASE_URL ?>relatorios/caixa" class="card text-decoration-none">
                <div class="card-body">
                    <h5 class="card-title text-primary">
                        <i class="fas fa-cash-register"></i> Visão de Caixa (Fluxo Financeiro)
                    </h5>
                    <p class="card-text text-muted">Relatório de entradas e saídas reais</p>
                </div>
            </a>
        </div>

        <div class="col-md-6 mb-4">
            <a href="<?= BASE_URL ?>relatorios/analitica" class="card text-decoration-none">
                <div class="card-body">
                    <h5 class="card-title text-info">
                        <i class="fas fa-chart-line"></i> Visão Analítica de OS
                    </h5>
                    <p class="card-text text-muted">Análise de rentabilidade por OS</p>
                </div>
            </a>
        </div>

        <div class="col-md-6 mb-4">
            <a href="<?= BASE_URL ?>relatorios/orfas" class="card text-decoration-none">
                <div class="card-body">
                    <h5 class="card-title text-warning">
                        <i class="fas fa-exclamation-triangle"></i> Visão de Entradas Órfãs
                    </h5>
                    <p class="card-text text-muted">Adiantamentos e entradas não faturadas</p>
                </div>
            </a>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
