<?php
require "GrabberTool.php";
foreach ($app['database']->selectAll('myfiles') as $item) {
    //download->decompress->read
    if ($item->todo) {

        if (!$item->downloaded) {
    /*       GrabberTool::downloadFile1($item); // ok unica soluzione
            $app['database']->setDownloaded($item->filename, $item->ID);
    */
               GrabberTool::downloadFileok($item, $app['database']); //ok passo passo


            if($item->filesize === $app['database']->getCursor($item->filename,$item->ID)) {
                $app['database']->setDownloaded($item->filename, $item->ID);
                $step = "file " . $item->filename . " scaricato";
            }
           $step = "file " . $item->filename .
               " in download : di ".($item->filesize)." byte";
            break; // passa al prossimo file
        }
    }
}
require "views/download.view.php";