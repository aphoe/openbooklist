<?php

namespace App\Http\Controllers\Api\V1\App\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class LoginController extends Controller
{
    public function __invoke(Request $request): Response
    {
        return response()->noContent();
    }
}
