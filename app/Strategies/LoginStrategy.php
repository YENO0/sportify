<?php

namespace App\Strategies;

use Illuminate\Http\Request;

interface LoginStrategy
{
    public function login(Request $request);
}
