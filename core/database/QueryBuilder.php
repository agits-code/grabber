<?php


class QueryBuilder
{
    protected $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function selectAll($table)
    {
        $statement = $this->pdo->prepare("select * from {$table}");

        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_CLASS);
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

    public function skipFiles()
    {

        try {
            $statment = $this->pdo->prepare("UPDATE myfiles SET todo=false 
             WHERE (filename regexp '^([a-z_.-]+)\.([a-z.]{2,6})$' or filename LIKE '%.xml.%')");

        } catch (Exception $e)
        {
            //var_dump($e);
        }
        $statment->execute();
    }

    public function setDownloaded($filename)
    {
        try {
            $statment = $this->pdo->prepare("UPDATE myfiles SET downloaded=true WHERE filename='$filename'");

        } catch (Exception $e)
        {
            //var_dump($e);
        }
        $statment->execute();
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
    public function clearOld()
    {
        try {
            $statment = $this->pdo->prepare("DELETE FROM myfiles WHERE isread=true AND filedate < (NOW() - 3600*24*30);");

        } catch (Exception $e)
        {
            //var_dump($e);
        }
        $statment->execute();
    }
}