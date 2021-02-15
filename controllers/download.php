<?php
require "GrabberTool.php";
foreach ($app['database']->selectAll('myfiles') as $item) {
    //download->decompress->read
    if ($item->todo) {

        if (!$item->downloaded) {
            GrabberTool::downloadFileok($item,$app['database']);

           // GrabberTool::downloadFile1($item->link); // funziona
            $app['database']->setDownloaded($item->filename);
            $step = "file ".$item->filename." scaricato";
            break;
        }
    }
}
require "views/download.view.php";