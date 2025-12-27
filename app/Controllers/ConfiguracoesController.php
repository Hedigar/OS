<?php

namespace App\Controllers;

class ConfiguracoesController extends BaseController
{
    public function index()
    {
        $this->render('configuracoes/index', [
            'title' => 'Configurações',
            'current_page' => 'configuracoes'
        ]);
    }
}
