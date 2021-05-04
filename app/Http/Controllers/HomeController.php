<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {

    }
    public function test()
    {

        return ;
    }
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }
}


function print_r2($var)
{
    ob_start();
    print_r($var);
    $str        =   ob_get_contents();
    ob_end_clean();
    $str        =   str_replace(" ", "&nbsp;", $str);
    echo nl2br("<span style='font-family:Tahoma, 굴림; font-size:9pt;'>$str</span>");
}