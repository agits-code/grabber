<?php
namespace App\Controllers;
use App\Core\App;
class PagesController
{
    public function home()
    {

        //echo date('y-m-d h:m:s T', '1614250962');die();
        $db = App::get('database');

        // leggo la pagina
        $elenco = GrabberTool::getItems('https://assoc-datafeeds-eu.amazon.com/datafeed/listFeeds');

        // scrivo elementi nuovi nel DB
        foreach ($elenco as $item)
        {
            if(!(strpos($item['name'],'.xml.')))
            {
                $exists = $db->query_all(
                    "SELECT * From myfiles WHERE filename='{$item['name']}' AND filedate='{$item['date']}';"
                );
                if (!$exists) {

                    $db->query("INSERT INTO myfiles (filename , filedate , filesize , md5 , link )
                 VALUES ('{$item['name']}','{$item['date']}','{$item['size']}','{$item['code']}','{$item['link']}');");
                }
            }

        }

         // cancello file processati più vecchi di 30 gg
         $db->query("DELETE FROM myfiles WHERE isread=true AND filedate < UNIX_TIMESTAMP() - 3600*24*30;");

        // $files = $db->query_all("select * from myfiles where ((downloaded=false AND filecursor >0) OR (isread=false AND pointer > 0));");

         $now = time();

         //require 'views/index.view.php';
        return view('index', [

            'now' => $now
        ]);

    }

    public function download()
    {
        $db = App::get('database');
        $downl_file = $db->query_first(
            "SELECT * FROM myfiles WHERE downloaded=false ORDER BY filedate ASC LIMIT 1;"
        );
        if($downl_file) {
            $endCursor =GrabberTool::downloadFile(
                $downl_file->ID,$downl_file->filesize,$downl_file->link, $downl_file->filecursor
            ); //ok passo passo
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

        //require "views/download.view.php";
        return view('download', [
            'now' => $now,
            'step' => $step
        ]);
    }

    public function decompress()
    {
        $db = App::get('database');
        $decompr_file = $db->query_first(
            "SELECT * FROM myfiles WHERE downloaded=true AND decompressed=false ORDER BY FILEDATE ASC LIMIT 1;"
        );

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


        return view('decompress', [
            'now' => $now,
            'step' => $step
        ]);
    }

    public function read()
    {
        $db = App::get('database');
        $read_file = $db->query_first(
            "SELECT * FROM myfiles WHERE decompressed=true AND isread=false ORDER BY FILEDATE ASC LIMIT 1;"
        );
        if($read_file)
        {
            GrabberTool::csvReader($read_file->ID,$read_file->filename, $db);
            $now = time();
            $db->query("UPDATE myfiles SET updated= '$now' WHERE ID='$read_file->ID';");

        }else
        {
            echo "nessun file da leggere";
            $now = time();

        }


        $step = ($read_file) ? ($read_file->ID." : ".$read_file->filename) : ("&#10005");


        return view('read', [
            'now' => $now,
            'step' => $step
        ]);
    }

    public function about()
    {
        //var_dump(trim($_SERVER['REQUEST_URI'],"/"));

        return view('about');
    }

    public function names()
    {
        //var_dump($_SERVER);
        //var_dump($_REQUEST);
        var_dump("you typed " . $_POST['name']);

    }
}