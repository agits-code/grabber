<?php
require "GrabberTool.php";
$elenco = GrabberTool::getItems('https://assoc-datafeeds-eu.amazon.com/datafeed/listFeeds');
//var_dump($elenco[24]['name']);die();
foreach ($elenco as $item) {
    $file = $app['database']->insert($item['name'], $item['date'], $item['size'], $item['code'], $item['link']);
}
$files = $app['database']->selectAll('myfiles');

require 'views/index.view.php';
//

//echo GrabberTool::fetchContent('https://assoc-datafeeds-eu.amazon.com/datafeed/listFeeds');

//var_dump($elenco[0]);
//echo GrabberTool::addLink('https://assoc-datafeeds-eu.amazon.com/datafeed/listFeeds');
//GrabberTool::downloadFile('https://assoc-datafeeds-eu.amazon.com/datafeed/getFeed?filename=it_standardized_camera_mp_20210118_1.delta.csv.gz');
//GrabberTool::decompressGz('getFeed?filename=it_standardized_camera_mp_20210118_1.delta.csv.gz');
//GrabberTool::csvReader('getFeed?filename=it_standardized_camera_mp_20210118_1.delta.csv');