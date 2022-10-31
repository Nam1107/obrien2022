<?php
header("Access-Control-Allow-Origin: *");
session_start();
require './path.php';
require './configs/routers.php';
require_once './core/Application.php';
require './database/db.php';
require './helper/middleware.php';
require './helper/validateUser.php';

$myApp = new Application();