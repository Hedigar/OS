<?php

namespace App\Controllers;

use App\Models\ConfiguracaoGeral;

class ConfiguracoesController extends BaseController
{
    private $configModel;

    public function __construct()
    {
        parent::__construct();
        $this->configModel = new ConfiguracaoGeral();
    }

    public function index()
    {
        $this->render('configuracoes/index', [
            'title' => 'Configurações',
            'current_page' => 'configuracoes'
        ]);
    }

    public function os()
    {
        $textoPadrao = "Será cobrado um valor R$ 100,00 mão de obra (HORA TÉCNICA), caso o cliente não autorize a realização do serviço (ORÇAMENTO).\n" .
                       "*Não nos responsabilizamos pela origem e software dos equipamentos depositados para orçamentos.\n" .
                       "*O equipamento somente será entregue com a apresentação da ordem de serviço ou documento com foto somente para o proprietário.\n" .
                       "*Equipamentos não retirados no prazo de 30 dias após a da data de conclusão do serviço, serão considerado abandonados e será cobrado uma taxa diária de R$ 2,00(dois reais) para fins de armazenamento, contar a partir da data de conclusão do serviço até a data de retirada do equipamento. Caso esse prazo de armazenamentoseja superior a 90 dias, autorizo desde já a doação do equipamento à Myranda Informatica para que essa possa cobrir todos os custos de armazenagem, bem comodoar, vender, reciclar ou mesmo descartar de forma correta o equipamento.";

        $this->render('configuracoes/os', [
            'title' => 'Configurações de OS',
            'current_page' => 'configuracoes_os',
            'fonte_tamanho' => $this->configModel->getValor('impressao_fonte_tamanho') ?? '12',
            'exibir_observacoes' => $this->configModel->getValor('impressao_exibir_observacoes') ?? '1',
            'texto_observacoes' => $this->configModel->getValor('impressao_texto_observacoes') ?? $textoPadrao
        ]);
    }

    public function salvarImpressao()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $tamanho = filter_input(INPUT_POST, 'fonte_tamanho', FILTER_SANITIZE_SPECIAL_CHARS);
            $exibirObs = filter_input(INPUT_POST, 'exibir_observacoes', FILTER_SANITIZE_SPECIAL_CHARS) ?: '0';
            $textoObs = filter_input(INPUT_POST, 'texto_observacoes', FILTER_SANITIZE_SPECIAL_CHARS);

            $this->configModel->setValor('impressao_fonte_tamanho', $tamanho, 'Tamanho da fonte para impressão de OS');
            $this->configModel->setValor('impressao_exibir_observacoes', $exibirObs, 'Exibir observações na impressão de OS');
            $this->configModel->setValor('impressao_texto_observacoes', $textoObs, 'Texto de observações/termos na impressão de OS');

            $_SESSION['success'] = "Configurações de impressão atualizadas!";
            $this->redirect('configuracoes/os');
        }
    }
}
