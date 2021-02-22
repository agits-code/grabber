<?php
require "GrabberTool.php";


foreach ($app['database']->selectAll('myfiles') as $item) {
    //download->decompress->read
    if ($item->todo) {

        if (!$item->downloaded) {

            GrabberTool::downloadFile($item, $app['database']->getPdo()); //ok passo passo
            break;
        }
    }
}
//require "views/download.view.php";
