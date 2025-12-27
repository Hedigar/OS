<?php

namespace App\Controllers;

class DespesasController extends BaseController
{
    public function index()
    {
        $this->render('despesas/index', [
            'title' => 'Despesas',
            'current_page' => 'despesas'
        ]);
    }
}
