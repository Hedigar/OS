<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Services\DashboardService;

class DashboardController extends BaseController
{
    private DashboardService $service;

    public function __construct()
    {
        parent::__construct();
        $this->service = new DashboardService();
    }

    public function index()
    {
        $user = Auth::user();
        $stats = $this->service->getStats();
        $atividades = $this->service->getRecentActivities(10);
        $alertas = $this->service->getAlertas();

        $this->render('dashboard/index', [
            'title' => 'Dashboard',
            'user' => $user,
            'stats' => $stats,
            'atividades' => $atividades,
            'alertas' => $alertas
        ]);
    }
}
