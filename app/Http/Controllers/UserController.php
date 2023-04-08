<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    public function register() {
        return response('register', 200);
    }

    public function login() {
        return response('login', 200);
    }


}
