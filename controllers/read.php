<?php
require "GrabberTool.php";
if (!$list = $app['database']->decompressedFiles()) exit("nessun file da leggere");
foreach ($app['database']->decompressedFiles() as $item) {




        if (!$item->isread) {
            GrabberTool::csvReader($item->filename,$app['database']);

            $step = "file ".$item->filename." letto";
            break;
        }



}




//require "views/read.view.php";