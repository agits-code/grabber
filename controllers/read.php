<?php
$read_file = $db->query_first("SELECT * FROM myfiles WHERE decompressed=true AND isread=false ORDER BY FILEDATE ASC LIMIT 1;");
if($read_file)
{
    GrabberTool::csvReader($read_file->ID,$read_file->filename, $db);
}else
{
    echo "nessun file da leggere";
}

$step = ($read_file) ? ($read_file->ID." : ".$read_file->filename) : ("&#10005");



require "views/read.view.php";