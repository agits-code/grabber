<?php
require "GrabberTool.php";

if (
    !($list =  $app['database']->getPdo()->
    query("select * from myfiles where downloaded=true AND decompressed=false;")->fetchAll(PDO::FETCH_CLASS))
   )
   {
      $step = ( "nessun file da scompattare");

    } else {
    foreach ($list as $item) {

        if (!$item->decompressed) {
            GrabberTool::decompressGz($item);

            $app['database']->getPdo()->query("UPDATE myfiles SET decompressed=true WHERE filename='$item->filename'  AND ID='$item->ID';");
           $step = "file " . $item->filename . " scompattato";
            break;

        }
     }

   }


require "views/decompress.view.php";