<?php
require 'vendor/autoload.php';
require 'core/bootstrap.php';
require 'core/Request.php';

 Router::load('routes.php')
    ->direct(Request::uri(),Request::method());
