<?php
require "GrabberTool.php";
// leggo la pagina
$api_amazon = GrabberTool::getItems('https://assoc-datafeeds-eu.amazon.com/datafeed/listFeeds');

// scrivo elementi nuovi nel DB
foreach ($api_amazon as $item)
{
    $file = $app['database']->putRow($item['name'], $item['date'], $item['size'], $item['code'], $item['link']);
}

// elimino elementi letti più vecchi di un mese
$app['database']->getPdo()->query("DELETE FROM myfiles WHERE isread=true AND filedate < (NOW() - 3600*24*30);");

// escludo file xml dalla lista file da elaborare
// x escludere anche file base: WHERE (filename regexp '^([a-z_.-]+)\.([a-z.]{2,6})$' or filename LIKE '%.xml.%')");
$app['database']->getPdo()->query("UPDATE myfiles SET todo=false WHERE (filename LIKE '%.xml.%');");


// visualizzo file in fase di elaborazione
$files = $app['database']->getPdo()->
query("select * from myfiles where ((downloaded=false AND filecursor >0) OR (isread=false AND pointer > 0))")->
fetchAll(PDO::FETCH_CLASS);

require 'views/index.view.php';
