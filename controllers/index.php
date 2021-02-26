<?php
//echo date('y-m-d h:m:s T', '1614250962');die();


// leggo la pagina
$elenco = GrabberTool::getItems('https://assoc-datafeeds-eu.amazon.com/datafeed/listFeeds');

// scrivo elementi nuovi nel DB
foreach ($elenco as $item)
   {
       if(!(strpos($item['name'],'.xml.')))
       {
           $exists = $db->query_all("SELECT * From myfiles WHERE filename='{$item['name']}' AND filedate='{$item['date']}';");
           if (!$exists) {

               $db->query("INSERT INTO myfiles (filename , filedate , filesize , md5 , link )
        VALUES ('{$item['name']}','{$item['date']}','{$item['size']}','{$item['code']}','{$item['link']}');");
           }
       }

   }

// cancello file processati più vecchi di 30 gg
$db->query("DELETE FROM myfiles WHERE isread=true AND filedate < UNIX_TIMESTAMP() - 3600*24*30;");

$files = $db->query_all("select * from myfiles where ((downloaded=false AND filecursor >0) OR (isread=false AND pointer > 0));");

$now = time();

require 'views/index.view.php';
