<?php
require "GrabberTool.php";
$list =  $app['database']->query_all("select * from myfiles where downloaded=true AND decompressed=false;");

if (!($list))
   {
      $step = ( "nessun file da scompattare");

    } else {
    foreach ($list as $item) {

        if (!$item->decompressed) {
            GrabberTool::decompressGz($item);

            $app['database']->query("UPDATE myfiles SET decompressed=true WHERE filename='$item->filename'  AND ID='$item->ID';");
            $step = "file " . $item->filename . " scompattato";
            break;

        }
     }

   }


require "views/decompress.view.php";