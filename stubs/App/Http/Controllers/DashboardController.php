<?php

namespace App\Http\Controllers\{{Name}};

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index(){
        return view('{{name}}.dashboard');
    }
}
