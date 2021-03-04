<?php
namespace App\Controllers;

use App\Core\App;

class FilesController
{
    public function index()
    {
        $db = App::get('database');
        $files = $db->query_all("select * from myfiles where ((downloaded=false AND filecursor >0) OR (isread=false AND pointer > 0));");
        $now = time();
        return view('files',[
            'files' => $files,
            'now' => $now
        ]);


    }
}