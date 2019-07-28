<?php

namespace App\Http\Controllers;

use Tymon\JWTAuth\JWTAuth;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', []);
    }

    public function getAuthUser()
    {
        return response()->json(auth()->user(), 200);
    }
}
