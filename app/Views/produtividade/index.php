<div class="container">
    <div class="d-flex justify-between align-center mb-4 flex-wrap">
        <h1>Produtividade Pessoal</h1>
        <div class="d-flex gap-2">
            <a href="<?php echo BASE_URL; ?>produtividade/form" class="btn btn-primary">Registrar</a>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-body">
                    <h2 class="card-title">Resumo Diário</h2>
                    <canvas id="pieDia"></canvas>
                    <p class="mt-3">Total de horas: <?php echo round(($dia['total'] ?? 0) / 60, 2); ?></p>
                    <ul class="mt-2">
                        <?php foreach (($dia['atividades'] ?? []) as $a): ?>
                            <li><?php echo date('H:i', strtotime($a['data_hora'])); ?> • <?php echo $a['tipo']; ?> • <?php echo (int)$a['tempo_minutos']; ?> min</li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-8 mb-4">
            <div class="card">
                <div class="card-body">
                    <h2 class="card-title">Relatório Semanal</h2>
                    <canvas id="barSemana"></canvas>
                    <div class="mt-3">
                        <?php foreach (($semana['alertas'] ?? []) as $al): ?>
                            <div class="alert <?php echo str_starts_with($al, '✅') ? 'alert-success' : 'alert-warning'; ?>"><?php echo $al; ?></div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <h2 class="card-title">Relatório Mensal</h2>
                    <canvas id="lineMes"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const sumDia = <?php echo json_encode($dia['somas'] ?? ['operacional'=>0,'supervisao'=>0,'estrategico'=>0]); ?>;
const pieDia = new Chart(document.getElementById('pieDia'), {
    type: 'pie',
    data: {
        labels: ['Operacional','Supervisão','Estratégico'],
        datasets: [{
            data: [sumDia.operacional, sumDia.supervisao, sumDia.estrategico],
            backgroundColor: ['#ff6384','#ffcd56','#4bc0c0']
        }]
    }
});
const semanaDias = <?php echo json_encode(array_keys($semana['dias'] ?? [])); ?>;
const semanaData = <?php echo json_encode(array_values($semana['dias'] ?? [])); ?>;
const op = semanaData.map(d => d.operacional);
const sup = semanaData.map(d => d.supervisao);
const est = semanaData.map(d => d.estrategico);
const barSemana = new Chart(document.getElementById('barSemana'), {
    type: 'bar',
    data: {
        labels: semanaDias,
        datasets: [
            {label:'Operacional', data: op, backgroundColor:'#ff6384', stack:'tempo'},
            {label:'Supervisão', data: sup, backgroundColor:'#ffcd56', stack:'tempo'},
            {label:'Estratégico', data: est, backgroundColor:'#4bc0c0', stack:'tempo'}
        ]
    },
    options: {
        responsive: true,
        scales: {x: {stacked: true}, y: {stacked: true}}
    }
});
const mesDias = <?php echo json_encode(array_keys($mes['dias'] ?? [])); ?>;
const mesData = <?php echo json_encode(array_values($mes['dias'] ?? [])); ?>;
const mesOp = mesData.map(d => d.operacional);
const mesSup = mesData.map(d => d.supervisao);
const mesEst = mesData.map(d => d.estrategico);
const lineMes = new Chart(document.getElementById('lineMes'), {
    type: 'line',
    data: {
        labels: mesDias,
        datasets: [
            {label:'Operacional', data: mesOp, borderColor:'#ff6384', backgroundColor:'rgba(255,99,132,0.2)'},
            {label:'Supervisão', data: mesSup, borderColor:'#ffcd56', backgroundColor:'rgba(255,205,86,0.2)'},
            {label:'Estratégico', data: mesEst, borderColor:'#4bc0c0', backgroundColor:'rgba(75,192,192,0.2)'}
        ]
    }
});
</script>
