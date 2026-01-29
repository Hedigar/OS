<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Models\ConfiguracaoGeral;

class CalculadoraController extends BaseController
{
    private ConfiguracaoGeral $configModel;

    public function __construct()
    {
        parent::__construct();
        $this->configModel = new ConfiguracaoGeral();
    }

    public function index()
    {
        Auth::check();

        // Carregar configurações de pagamentos (taxas)
        $pagamentosJson = $this->configModel->getValor('pagamentos_config') ?: '';
        $pagamentosConfig = [];
        if (!empty($pagamentosJson)) {
            try {
                $pagamentosConfig = json_decode($pagamentosJson, true) ?: [];
            } catch (\Throwable $e) {
                $pagamentosConfig = [];
            }
        }

        // Filtrar máquinas habilitadas
        $maquinasHabilitadas = [];
        if (!empty($pagamentosConfig['maquinas'])) {
            foreach ($pagamentosConfig['maquinas'] as $mq) {
                if (!empty($mq['habilitada'])) {
                    $maquinasHabilitadas[] = $mq;
                }
            }
        }

        // Carregar configurações padrão da calculadora
        $margemPadrao = $this->configModel->getValor('calculadora_margem_padrao');
        $impostoPadrao = $this->configModel->getValor('calculadora_imposto_padrao');

        // Valores default caso não existam no banco
        if ($margemPadrao === null) $margemPadrao = 30; // 30%
        if ($impostoPadrao === null) $impostoPadrao = 3;  // 3%

        $this->render('ferramentas/calculadora', [
            'title' => 'Calculadora de Preços',
            'current_page' => 'calculadora',
            'maquinas' => $maquinasHabilitadas,
            'margem_padrao' => (float)$margemPadrao,
            'imposto_padrao' => (float)$impostoPadrao
        ]);
    }

    public function salvarConfig()
    {
        Auth::check();
        
        if (!\App\Core\Auth::isAdmin()) {
            $this->redirect('ferramentas/calculadora');
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $margem = filter_input(INPUT_POST, 'margem_padrao', FILTER_VALIDATE_FLOAT);
            $imposto = filter_input(INPUT_POST, 'imposto_padrao', FILTER_VALIDATE_FLOAT);

            if ($margem !== false) {
                $this->configModel->setValor('calculadora_margem_padrao', (string)$margem, 'Margem de lucro padrão para calculadora');
            }
            
            if ($imposto !== false) {
                $this->configModel->setValor('calculadora_imposto_padrao', (string)$imposto, 'Imposto padrão para calculadora');
            }
        }

        $this->redirect('ferramentas/calculadora');
    }
}
