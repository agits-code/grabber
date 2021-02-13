<?php
require "GrabberTool.php";

foreach ($app['database']->selectAll('myfiles') as $item) {
    //download->decompress->read
    if ($item->downloaded) {


        if (!$item->decompressed) {
            GrabberTool::decompressGz("getFeed?filename=" . $item->filename);
            $app['database']->setDecompressed($item->filename);
            $step = "file ".$item->filename." scompattato";
            break;

        }
    }

}


require "views/decompress.view.php";