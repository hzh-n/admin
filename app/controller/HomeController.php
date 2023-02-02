<?php

namespace app\controller;

use app\model\News;

class HomeController extends _Controller
{
    public function indexAction()
    {


        return redirect('/html/index.html');
    }
}