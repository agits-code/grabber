<?php
require "GrabberTool.php";
foreach ($app['database']->selectAll('myfiles') as $item) {
    //download->decompress->read
    if ($item->todo) {

        if (!$item->downloaded) {
            GrabberTool::downloadFile($item); // ok unica soluzione

            $app['database']->getPdo()->query("UPDATE myfiles SET downloaded=true WHERE filename='$item->filename' AND ID='$item->ID';");
            $step = "file " . $item->filename . " scaricato";

            break;

        }
    }
}
require "views/download.view.php";