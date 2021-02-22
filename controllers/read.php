<?php
require "GrabberTool.php";
if (!( $app['database']->getPdo()->query("select * from myfiles where decompressed=true AND isread=false;")->fetchAll(PDO::FETCH_CLASS)))
{
   $step = "nessun file da leggere";
} else {


   foreach (($app['database']->getPdo()->query("select * from myfiles where decompressed=true;")->fetchAll(PDO::FETCH_CLASS)) as $item) {


       if (!$item->isread) {
          GrabberTool::csvReader($item, $app['database']->getPdo());
           $app['database']->getPdo()->query("UPDATE myfiles SET isread=true WHERE ID='$item->ID';");
         $step = "file " . $item->filename . " letto";
        break;
       }

}

}




require "views/read.view.php";