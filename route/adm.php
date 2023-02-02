<?php

use think\facade\Route;

/* ----------------  version/controller/action 变为 version.controller/action  ----------------*/

$pathInfo = request()->pathinfo();

$pathArr = explode('/', $pathInfo);

if (count($pathArr) > 2 && $pathArr[0] == 'adm' && $pathArr[2] != 'login') {
    $newPathInfo = $pathInfo;
    if (count($pathArr) === 3) {
        // 包含两个 /
        $newPathInfo = $pathArr[0] . '.' . $pathArr[1] . '/' . $pathArr[2];
    }
    if (count($pathArr) === 4) {
        // 包含两个 /
        $newPathInfo = $pathArr[0] . '.' . $pathArr[1] . '.' . $pathArr[2] . '/' . $pathArr[3];
    }

    request()->setPathinfo($newPathInfo);
}

/* ----------------  version/controller/action 变为 version.controller/action  ----------------*/

//登陆地址
Route::rule('adm/10soo_login', 'adm.public/login');

Route::get('captcha/[:config]','\\think\\captcha\\CaptchaController@index');
