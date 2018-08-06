<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ManhourController extends Controller
{
    //

    public function input($id) {
        return view('manhour.input');
    }
}
