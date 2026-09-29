<?php
// MIGRAÇÃO COMPRADO: limpa fluxo_caixa e repopula só itens marcados como comprado
$_ENV['DB_HOST'] = '127.0.0.1';
$_ENV['DB_USERNAME'] = 'root';
$_ENV['DB_PASSWORD'] = 'root';
$_ENV['DB_DATABASE'] = 'os';
require_once __DIR__ . '/../app/Core/Autoload.php';

use App\Models\FluxoCaixa;
$fluxo = new FluxoCaixa();
$db = $fluxo->getConnection();

$statusAprovados = [4,5,8,11,12,14,15];
$placeholders = implode(',', array_fill(0, count($statusAprovados), '?'));

$db->exec("TRUNCATE TABLE fluxo_caixa");

$sqlItens = "
SELECT ios.id, ios.ordem_servico_id, ios.atendimento_externo_id, ios.quantidade,
       COALESCE(NULLIF(ios.valor_custo,0), NULLIF(ios.custo,0)) as vc,
       COALESCE(ios.data_compra, DATE(ios.created_at)) as data_transacao
FROM itens_ordem_servico ios
LEFT JOIN ordens_servico os ON ios.ordem_servico_id = os.id
LEFT JOIN atendimentos_externos ae ON ios.atendimento_externo_id = ae.id
WHERE ios.ativo = 1 AND ios.comprado = 1
  AND (
    (ios.ordem_servico_id IS NOT NULL AND os.status_atual_id IN ($placeholders))
    OR (ios.atendimento_externo_id IS NOT NULL AND ae.status = 'concluido')
  )
";
$stmt = $db->prepare($sqlItens);
$stmt->execute($statusAprovados);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

$cnt = 0;
foreach($items as $it){
    $valor = $it['quantidade']*$it['vc'];
    if($valor<=0) continue;
    if($it['ordem_servico_id']){
        $fluxo->registrarCustoItemOs($it['id'], $it['ordem_servico_id'], $valor, $it['data_transacao']);
    }else{
        $fluxo->registrarCustoItemAtendimento($it['id'], $it['atendimento_externo_id'], $valor, $it['data_transacao']);
    }
    $cnt++;
}
echo "Lançados $cnt itens comprados\n";
