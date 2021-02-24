<?php

    $downl_file = $db->query_all("SELECT * FROM myfiles WHERE downloaded=false ORDER BY FILEDATE ASC LIMIT 1;");
    if($downl_file)
    {
        GrabberTool::downloadFile($downl_file[0], $db); //ok passo passo
    } else
        {
            echo "nessun file da scaricare";
        }


$step = ($downl_file) ? ($downl_file[0]->ID." : ".$downl_file[0]->filename) : ("&#10005");

require "views/download.view.php";
