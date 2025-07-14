<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OnlineColumbariumController extends Controller
{
    /**
     * Display the online columbarium page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('columbarium.index');
    }
}