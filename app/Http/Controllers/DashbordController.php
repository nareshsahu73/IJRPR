<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;


class DashbordController extends Controller
{
    public function show()
    {
        return view('profile.show');
    }
}