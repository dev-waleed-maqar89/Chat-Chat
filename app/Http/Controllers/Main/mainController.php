<?php

namespace App\Http\Controllers\Main;

use App\Events\Main\NewMessageReceived;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class mainController extends Controller
{
    public function __invoke(Request $request)
    {
        return view('layouts.app');
    }
}