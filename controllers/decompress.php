<?php

    $decompr_file = $db->query_first("SELECT * FROM myfiles WHERE downloaded=true AND decompressed=false ORDER BY FILEDATE ASC LIMIT 1;");

    if ($decompr_file)
    {
        GrabberTool::decompressGz($decompr_file->ID,$decompr_file->link,$db);
        $now = time();
        $db->query("UPDATE myfiles SET updated= '$now' WHERE ID='$decompr_file->ID';");

    } else
        {
           echo "nessun file da scompattare.";
            $now = time();

        }

    $step = ($decompr_file) ? ($decompr_file->ID." : ".$decompr_file->filename) : ("&#10005");



require "views/decompress.view.php";