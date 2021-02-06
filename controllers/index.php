<?php
//$file = $app['database']->insert();
$files = $app['database']->selectAll('myfiles');

require 'views/index.view.php';
//
require "GrabberTool.php";
//echo GrabberTool::fetchContent('https://assoc-datafeeds-eu.amazon.com/datafeed/listFeeds');
$elenco = GrabberTool::getItems('https://assoc-datafeeds-eu.amazon.com/datafeed/listFeeds');
//echo GrabberTool::addLink('https://assoc-datafeeds-eu.amazon.com/datafeed/listFeeds');
//GrabberTool::downloadFile('https://assoc-datafeeds-eu.amazon.com/datafeed/getFeed?filename=it_standardized_camera_mp_20210118_1.delta.csv.gz');
//GrabberTool::decompressGz('getFeed?filename=it_standardized_camera_mp_20210118_1.delta.csv.gz');
//GrabberTool::csvReader('getFeed?filename=it_standardized_camera_mp_20210118_1.delta.csv');