<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RoverController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Render the HTML form
     */
    public function index() {
        return view('index');
    }

    /**
     * 
     */
    public function initiate(Request $request) {
        var_dump(explode(" ", $request->get('masterCommand')));
    }
}
