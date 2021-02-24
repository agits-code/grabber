<?php
$read_file = $db->query_all("SELECT * FROM myfiles WHERE decompressed=true AND isread=false ORDER BY FILEDATE ASC LIMIT 1;");
if($read_file)
{
    GrabberTool::csvReader($read_file[0], $db);
}else
{
    echo "nessun file da leggere";
}

$step = ($read_file) ? ($read_file[0]->ID." : ".$read_file[0]->filename) : ("&#10005");



//require "views/read.view.php";