<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\RoverControl;

class RoverController extends Controller
{
    /**
     * Render the HTML form
     *
     * @return Response
     */
    public function index() {
        return view('index');
    }

    /**
     * Initialize a Rover instance, providing it
     * with the command received.
     *
     * @param Request $request HTTP Post request
     */
    public function navigate(Request $request) {
        try {
            $rover = new RoverControl( str_replace('  ', ' ', trim($request->get('masterCommand'))));
        }
        catch(Exception $e) {
            echo $e;
            die();
        }

        return view('result', [ 'position' => $rover->getFormattedPosition()]);
    }
}
