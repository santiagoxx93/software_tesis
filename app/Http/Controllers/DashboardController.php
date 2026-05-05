<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;

class DashboardController extends Controller
{
    /**
     * Redirige al dashboard correcto según el rol del usuario autenticado.
     */
    public function index(): RedirectResponse|\Illuminate\View\View
    {
        $user = auth()->user();

        if ($user->esAdmin()) {
            return view('dashboard.admin');
        }

        return view('dashboard.especialista');
    }
}
