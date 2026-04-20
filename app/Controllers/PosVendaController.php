<?php

namespace App\Controllers;

use App\Models\OrdemServico;
use App\Models\Log;
use App\Core\Auth;

class PosVendaController extends BaseController
{
    public function index()
    {
        $osModel = new OrdemServico();
        $alertas = $osModel->getAlertasDashboard();
        $posVenda = array_values(array_filter($alertas, function ($a) {
            return ($a['tipo'] ?? '') === 'pos_venda';
        }));

        $this->render('posvenda/index', [
            'title' => 'Pós-Venda',
            'current_page' => 'pos_venda',
            'itens' => $posVenda
        ]);
    }

    public function registrarResposta()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('pos-venda');
        }

        $osId = filter_input(INPUT_POST, 'os_id', FILTER_VALIDATE_INT);
        $resumo = trim((string)filter_input(INPUT_POST, 'resumo', FILTER_SANITIZE_SPECIAL_CHARS));
        $nota = filter_input(INPUT_POST, 'nota', FILTER_VALIDATE_INT);

        if (!$osId || $resumo === '' || !$nota || $nota < 1 || $nota > 5) {
            $this->redirect('pos-venda');
        }

        $osModel = new OrdemServico();
        
        // Atualizar a OS com os dados do pós-venda
        $osModel->update($osId, [
            'pos_venda_status' => 1,
            'pos_venda_nota' => $nota,
            'pos_venda_data' => date('Y-m-d H:i:s')
        ]);

        $os = $osModel->findWithDetails($osId);
        $clienteNome = $os['cliente_nome'] ?? '';

        $log = new Log();
        $log->registrar(Auth::id(), 'Resposta Pós-Venda', "OS #{$osId} - {$clienteNome} - Nota: {$nota}", null, ['resumo' => $resumo]);

        $this->redirect('pos-venda');
    }
}
