<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SportController extends Controller
{
    // Add this function inside the class
    public function index()
    {
        // For now, let's just return a simple text to test it
        return "Hello! This is the Sport Controller working.";
    }
}