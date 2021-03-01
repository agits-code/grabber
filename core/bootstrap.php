<?php

App::bind('config',require 'config.php');
App::bind('database',new QueryBuilder(
    Connection::make(App::get('config')['database'])
));

function view($name, $data=[])
{
    extract($data,0);
    return require "views/{$name}.view.php";
}


//$db = App::get('database');

