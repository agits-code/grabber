<?php
require "GrabberTool.php";
if (!( $app['database']->getPdo()->query("select * from myfiles where decompressed=true AND isread=false;")->fetchAll(PDO::FETCH_CLASS)))
{
   $step = "nessun file da leggere";
} else {


   foreach (($app['database']->getPdo()->query("select * from myfiles where decompressed=true;")->fetchAll(PDO::FETCH_CLASS)) as $item) {


       if (!$item->isread) {
          GrabberTool::csvReader($item, $app['database']);
          // $app['database']->getPdo()->query("UPDATE myfiles SET isread=true WHERE ID='$item->ID';");

        break;
       }
       $step = "file " . $item->filename . " letto";
}

}




//require "views/read.view.php";