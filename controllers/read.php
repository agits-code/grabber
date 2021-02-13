<?php
require "GrabberTool.php";
foreach ($app['database']->selectAll('myfiles') as $item) {

    if ($item->decompressed) {


        if (!$item->isread) {
            GrabberTool::csvReader($item->filename,$app['database']);

            $step = "file ".$item->filename." letto";
            break;
        }

    }

}




//require "views/read.view.php";