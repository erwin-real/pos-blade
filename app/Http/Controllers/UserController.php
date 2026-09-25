<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    function index() {
        return "This is index";
    }

    function welcome() {
        return view('welcome');
    }
}
