<?php
require "GrabberTool.php";
// leggo la pagina
$elenco = GrabberTool::getItems('https://assoc-datafeeds-eu.amazon.com/datafeed/listFeeds');

// scrivo elementi nuovi nel DB
foreach ($elenco as $item)
   {
       $file = $app['database']->insert($item['name'], $item['date'], $item['size'], $item['code'], $item['link']);
       $app['database']->skipFiles();
   }




$app['database']->clearOld();


$files = $app['database']->selectAll('myfiles');


require 'views/index.view.php';
