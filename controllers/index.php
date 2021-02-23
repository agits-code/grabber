<?php
require "GrabberTool.php";
$db = $app['database'];


// leggo la pagina
$elenco = GrabberTool::getItems('https://assoc-datafeeds-eu.amazon.com/datafeed/listFeeds');

// scrivo elementi nuovi nel DB
foreach ($elenco as $item)
   {
       $exists = $db->query_all("SELECT * From myfiles WHERE filename='{$item['name']}' AND filedate='{$item['date']}';");
       if(!$exists) {

           $db->query("INSERT INTO myfiles (filename , filedate , filesize , md5 , link )
        VALUES ('{$item['name']}','{$item['date']}','{$item['size']}','{$item['code']}','{$item['link']}');");
       }

       // WHERE (filename regexp '^([a-z_.-]+)\.([a-z.]{2,6})$' or filename LIKE '%.xml.%')
       $db->query("UPDATE myfiles SET todo=false WHERE (filename LIKE '%.xml.%');");
   }



// cancello file processati più vecchi di 30 gg
echo date('y/m/d h:i:sa', 1614019273-(3600*24*30));;


$db->query("DELETE FROM myfiles WHERE (isread=true AND (filedate < (NOW()-(3600*24*30))));");
/*$tempo =(strtotime(date('Y-m-d H:i:s T', time())));
foreach ($app['database']->getPdo()->query("SELECT * FROM myfiles;")->fetchAll(PDO::FETCH_OBJ) as $value)
{
    if(($tempo-strtotime($value->filedate." UTC")) > (3600*24*30))
    {
        $app['database']->getPdo()->query("DELETE FROM myfiles WHERE isread=true AND ID='$value->ID';");
    }
}
*/
$files = $db->query_all("select * from myfiles where ((downloaded=false AND filecursor >0) OR (isread=false AND pointer > 0));");


require 'views/index.view.php';
