<?php

namespace App\Controllers;

use App\Core\Auth;

class DashboardController extends BaseController
{
    public function index()
    {
        $user = Auth::user();
        $this->render('dashboard/index', [
            'title' => 'Dashboard',
            'user' => $user
        ]);
    }
}
