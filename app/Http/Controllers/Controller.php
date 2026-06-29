<?php

namespace App\Http\Controllers;

use App\Traits\ApiResponse; // <-- Import trait

abstract class Controller
{
    use ApiResponse; // <-- Gunakan trait di sini
}