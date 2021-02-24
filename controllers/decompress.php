<?php

    $decompr_file = $db->query_all("SELECT * FROM myfiles WHERE downloaded=true AND decompressed=false ORDER BY FILEDATE ASC LIMIT 1;");
    if ($decompr_file)
    {
        GrabberTool::decompressGz($decompr_file[0],$db);
    } else
        {
           echo "nessun file da scompattare.";
        }

    $step = ($decompr_file) ? ($decompr_file[0]->ID." : ".$decompr_file[0]->filename) : ("&#10005");



require "views/decompress.view.php";