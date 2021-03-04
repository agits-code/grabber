<?php
use App\Core\App;
App::bind('config',require 'config.php');
App::bind('database',new QueryBuilder(
    Connection::make(App::get('config')['database'])
));

function view($name, $data=[])
{
    extract($data,0);
    return require "app/views/{$name}.view.php";
}

function redirect($path)
{
    header("Location: /{$path}");
}

//$db = App::get('database');

