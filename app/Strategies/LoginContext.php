<?php

namespace App\Strategies;

use Illuminate\Http\Request;

class LoginContext
{
    private $strategy;

    public function __construct(LoginStrategy $strategy)
    {
        $this->strategy = $strategy;
    }

    public function login(Request $request)
    {
        return $this->strategy->login($request);
    }
}
