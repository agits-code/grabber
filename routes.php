<?php


$router->get( '' , 'PagesController@home');
$router->get('about' , 'PagesController@about');
$router->get( 'download' , 'PagesController@download');
$router->get('decompress' , 'PagesController@decompress');
$router->get( 'read' , 'PagesController@read');
$router->post('names' , 'PagesController@names');

$router->get('files','FilesController@index');