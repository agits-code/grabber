<?php
require "GrabberTool.php";
if (!$list = $app['database']->downloadedFiles()) exit("nessun file da scompattare");
foreach ($list as $item) {


        if (!$item->decompressed) {
            GrabberTool::decompressGz($item);
            $app['database']->setDecompressed($item->filename);
            $step = "file " . $item->filename . " scompattato";
            break;

        }


}


require "views/decompress.view.php";