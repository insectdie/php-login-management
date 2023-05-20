<?php

namespace insectdie\PHP\MVC\Controller;

use insectdie\PHP\MVC\App\View;

class HomeController
{
    function index() : void 
    {
        View::render('Home/index',[
            "title" => "PHP Login Management"
        ]);
    }
}