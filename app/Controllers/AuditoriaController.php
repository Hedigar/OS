<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Models\Log;
use App\Models\Usuario;

class AuditoriaController extends BaseController
{
    protected Usuario $usuarioModel;

    public function __construct()
    {
        parent::__construct();
        // Apenas super admin pode acessar
        $this->requireSuperAdmin();
        
        // $this->logModel já é instanciado no BaseController como protected
        $this->usuarioModel = new Usuario();
    }

    public function index()
    {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 20;
        $offset = ($page - 1) * $limit;

        $filters = [
            'usuario_id' => !empty($_GET['usuario_id']) ? (int)$_GET['usuario_id'] : null,
            'data_inicio' => !empty($_GET['data_inicio']) ? $_GET['data_inicio'] : null,
            'data_fim' => !empty($_GET['data_fim']) ? $_GET['data_fim'] : null,
            'acao' => !empty($_GET['acao']) ? $_GET['acao'] : null,
        ];

        // Busca logs filtrados
        $logs = $this->logModel->findFiltered(
            $filters['usuario_id'],
            $filters['data_inicio'],
            $filters['data_fim'],
            $filters['acao'],
            $limit,
            $offset
        );

        // Contagem para paginação
        $totalLogs = $this->logModel->countFiltered(
            $filters['usuario_id'],
            $filters['data_inicio'],
            $filters['data_fim'],
            $filters['acao']
        );

        $totalPages = ceil($totalLogs / $limit);

        $usuarios = $this->usuarioModel->all();

        $data = [
            'title' => 'Auditoria de Logs',
            'current_page' => 'auditoria',
            'logs' => $logs,
            'usuarios' => $usuarios,
            'filters' => array_merge($filters, [
                'page' => $page,
                'total_pages' => $totalPages,
                'total' => $totalLogs,
            ]),
            'pagination' => [
                'current_page' => $page,
                'total_pages' => $totalPages,
                'total_records' => $totalLogs
            ]
        ];

        $this->render('auditoria/index', $data);
    }
}
