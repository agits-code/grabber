<?php
require "GrabberTool.php";



// leggo la pagina
$elenco = GrabberTool::getItems('https://assoc-datafeeds-eu.amazon.com/datafeed/listFeeds');

// scrivo elementi nuovi nel DB
foreach ($elenco as $item)
   {
       $file = $app['database']->insert($item['name'], $item['date'], $item['size'], $item['code'], $item['link']);
       // WHERE (filename regexp '^([a-z_.-]+)\.([a-z.]{2,6})$' or filename LIKE '%.xml.%')
       $app['database']->getPdo()->query("UPDATE myfiles SET todo=false WHERE (filename LIKE '%.xml.%');");
   }



// cancello file processati più vecchi di 30 gg
$app['database']->getPdo()->query("DELETE FROM myfiles WHERE isread=true AND filedate < (NOW() - 3600*24*30);");

// ->query("select * from myfiles where ((downloaded=false AND filecursor >0) OR (isread=false AND pointer > 0));";
$files = $app['database']->getPdo()->query("select * from myfiles where ((downloaded=false AND filecursor >0) OR (isread=false AND pointer > 0));")->fetchAll(PDO::FETCH_OBJ);


require 'views/index.view.php';
