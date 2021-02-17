<?php


class QueryBuilder
{
    protected $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * @return mixed
     */
    public function getPdo()
    {
        return $this->pdo;
    }

    public function selectAll($table)
    {
        $statement = $this->pdo->prepare("select * from {$table}");

        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_CLASS);
    }

    public function selectProcessing() //da cancellare
    {
        //$statement = $this->pdo->prepare("select * from myfiles where ((downloaded=false AND filecursor >0) OR (isread=false AND pointer > 0))");
        $statement = $this->pdo->prepare("select * from myfiles where ( isread=false AND pointer > 0)");

        $statement->execute();

        $item = $statement->fetchAll(PDO::FETCH_CLASS);
        return $item;
    }

    public function downloadedFiles()
    {
        $statement = $this->pdo->prepare("select * from myfiles where downloaded=true");
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_CLASS);
    }

    public function decompressedFiles()
    {
        $statement = $this->pdo->prepare("select * from myfiles where decompressed=true");
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_CLASS);
    }

    public function getCursor($id)
    {

        $statement = $this->pdo->prepare("select * from myfiles where ID='$id';");

        $statement->execute();

        $item = $statement->fetchAll(PDO::FETCH_CLASS);
        return $item[0]->filecursor;
    }


    public function getPointer($table,$filename)
    {

        $statement = $this->pdo->prepare("select * from {$table} where filename = '$filename'");

        $statement->execute();

        $item = $statement->fetchAll(PDO::FETCH_CLASS);
        return $item[0]->pointer;
    }

    public function setPointer($filename,$cursor)
    {
        try {
            $statment = $this->pdo->prepare("UPDATE myfiles SET pointer='$cursor' WHERE filename='$filename'");

        } catch (Exception $e)
        {
            //var_dump($e);
        }
        $statment->execute();
    }

    public function setCursor($cursor,$id)
    {
        try {
            $statment = $this->pdo->prepare("UPDATE myfiles SET filecursor='$cursor' WHERE  ID='$id';");

        } catch (Exception $e)
        {
            //var_dump($e);
        }
        $statment->execute();
    }

    public function selectFile($table,$filename,$filedate)
    {
        $statement = $this->pdo->prepare("select * from {$table} where (filename = '$filename' and filedate = '$filedate')");
         $statement->execute();

        return $statement->fetchAll(PDO::FETCH_CLASS);
    }



    public function insert($filename,$filedate,$filesize,$md5,$link)
    {
       if(count($this->selectFile('myfiles',$filename,$filedate)) === 0) {
           try {
               $statment = $this->pdo->prepare("INSERT INTO myfiles (filename , filedate , filesize , md5 , link )
        VALUES ('$filename','$filedate','$filesize','$md5','$link')");


           } catch (Exception $e) {
               var_dump($e);
           }
           $statment->execute();
       }


    }





    public function setDecompressed($filename)
    {
        try {
            $statment = $this->pdo->prepare("UPDATE myfiles SET decompressed=true WHERE filename='$filename'");

        } catch (Exception $e)
        {
            //var_dump($e);
        }
        $statment->execute();
    }

    public function setRead($filename)
    {
        try {
            $statment = $this->pdo->prepare("UPDATE myfiles SET isread=true WHERE filename='$filename'");

        } catch (Exception $e)
        {
            //var_dump($e);
        }
        $statment->execute();
    }

}