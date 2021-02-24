<?php
require "GrabberTool.php";


foreach ($app['database']->selectAll('myfiles') as $item) {


        if (!$item->downloaded) {

            GrabberTool::downloadFile($item, $app['database']); //ok passo passo
            break;
        }

}
//require "views/download.view.php";
