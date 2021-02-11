<?php
require "GrabberTool.php";
// leggo la pagina
$elenco = GrabberTool::getItems('https://assoc-datafeeds-eu.amazon.com/datafeed/listFeeds');

// scrivo elementi nuovi nel DB
foreach ($elenco as $item)
   {
       $file = $app['database']->insert($item['name'], $item['date'], $item['size'], $item['code'], $item['link']);
       $app['database']->skipFiles();
   }

//scorro DB


$app['database']->clearOld();


    foreach ($app['database']->selectAll('myfiles') as $item) {
        //download->decompress->read
        if ($item->todo) {

            if (!$item->downloaded) {
                GrabberTool::downloadFile($item->link);
                $app['database']->setDownloaded($item->filename);

            }
            if (!$item->decompressed) {
                GrabberTool::decompressGz("getFeed?filename=".$item->filename);
                $app['database']->setDecompressed($item->filename);


            }
            if (!$item->isread) {
                GrabberTool::csvReader($item->filename,$app['database']);


                break;
            }

        }

    }


////////$files = $app['database']->selectAll('myfiles');


//////////require 'views/index.view.php';
//

//echo GrabberTool::fetchContent('https://assoc-datafeeds-eu.amazon.com/datafeed/listFeeds');

//var_dump($elenco[0]);
//echo GrabberTool::addLink('https://assoc-datafeeds-eu.amazon.com/datafeed/listFeeds');
//GrabberTool::downloadFile('https://assoc-datafeeds-eu.amazon.com/datafeed/getFeed?filename=it_standardized_camera_mp_20210118_1.delta.csv.gz');
//GrabberTool::decompressGz('getFeed?filename=it_standardized_camera_mp_20210118_1.delta.csv.gz');
//GrabberTool::csvReader('getFeed?filename=it_standardized_camera_mp_20210118_1.delta.csv');