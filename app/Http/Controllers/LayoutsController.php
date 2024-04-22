<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LayoutsController extends Controller
{
    public function index(){
        return view('layouts.index');
    }

    public function event(){
        return view('layouts.event');
    }

    public function tambah(){
        return view('layouts.tambah');
    }
}
