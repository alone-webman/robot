<?php

use Webman\Route;
use AloneWebMan\RoBot\BotRoute;

// 开启程序路由
BotRoute::start('%plugin%');

// 关闭默认路由
// Route::disableDefaultRoute();