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



    public function getPointer($file_id)
    {

        $statement = $this->pdo->prepare("select * from myfiles where ID = '$file_id'");

        $statement->execute();

        $item = $statement->fetchAll(PDO::FETCH_CLASS);

        return $item[0]->pointer;
    }

    public function setPointer($file_id,$cursor)
    {
        try {
            $statment = $this->pdo->prepare("UPDATE myfiles SET pointer='$cursor' WHERE ID='$file_id'");

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



    public function putRow($filename,$filedate,$filesize,$md5,$link)
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


    public function setRead($id)
    {
        try {
            $statment = $this->pdo->prepare("UPDATE myfiles SET isread=true WHERE ID='$id';");

        } catch (Exception $e)
        {
            //var_dump($e);
        }
        $statment->execute();
    }

}