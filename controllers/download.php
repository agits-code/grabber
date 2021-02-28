<?php

    $downl_file = $db->query_first("SELECT * FROM myfiles WHERE downloaded=false ORDER BY filedate ASC LIMIT 1;");
    if($downl_file) {
        $endCursor =GrabberTool::downloadFile($downl_file->ID,$downl_file->filesize,$downl_file->link, $downl_file->filecursor); //ok passo passo
        $now = time();
        $db->query("UPDATE myfiles SET updated= '$now' WHERE ID='$downl_file->ID';");
        $db->query("UPDATE myfiles SET filecursor='$endCursor' WHERE ID='$downl_file->ID';");
        if ($downl_file->filesize === $endCursor) {
            $db->query("UPDATE myfiles SET downloaded=true WHERE ID='$downl_file->ID';");
        }
    } else {
        echo "nessun file da scaricare";
        $now = time();
        }


$step = ($downl_file) ? ($downl_file->ID." : ".$downl_file->filename) : ("&#10005");

require "views/download.view.php";
