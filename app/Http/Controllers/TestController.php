<?php
namespace App\Http\Controllers;

/**
 * Created by PhpStorm.
 * User: moulik
 * Date: 28/11/17
 * Time: 1:04 PM
 */

class TestController extends Controller{
    public function hi(){
        return view('Charts.chart');
    }
    public function xrp(){
        return view('ind');
    }
}