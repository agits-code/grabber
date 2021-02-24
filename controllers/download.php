<?php

    $downl_file = $db->query_first("SELECT * FROM myfiles WHERE downloaded=false ORDER BY FILEDATE ASC LIMIT 1;");
    if($downl_file)
    {
        GrabberTool::downloadFile($downl_file->ID,$downl_file->filesize,$downl_file->link, $db); //ok passo passo
    } else
        {
            echo "nessun file da scaricare";
        }


$step = ($downl_file) ? ($downl_file->ID." : ".$downl_file->filename) : ("&#10005");

require "views/download.view.php";
