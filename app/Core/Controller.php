<?php

namespace App\Core;

use App\Console\Nixs;

class Controller
{
    public function view($view, $data = [])
    {
        Nixs::render($view, $data);
    }
}
