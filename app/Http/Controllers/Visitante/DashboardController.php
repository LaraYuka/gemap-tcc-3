<?php

namespace App\Http\Controllers\Visitante;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        return view('visitante.dashboard');
    }
}
