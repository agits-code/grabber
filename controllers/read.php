<?php
require "GrabberTool.php";
foreach ($app['database']->selectAll('myfiles') as $item) {
    //download->decompress->read
    if ($item->todo) {


        if (!$item->isread) {
            GrabberTool::csvReader($item->filename,$app['database']);

            $step = "file ".$item->filename." letto";
            break;
        }

    }

}




//require "views/read.view.php";