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

    public function selectOne($table)
    {
        $statement = $this->pdo->prepare("select * from {$table}");

        $statement->execute();

        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    public function insert($filename,$filedate,$filesize,$md5,$link)
    {

        try {
          $statment = $this->pdo->prepare("INSERT INTO myfiles (filename , filedate , filesize , md5 , link )
        VALUES ('$filename','$filedate','$filesize','$md5','$link')");


        } catch (Exception $e)
        {
           //var_dump($e);
        }
        $statment->execute();
    }

    public function todo()
    {
     //$sql = "UPDATE MyGuests SET lastname='Doe' WHERE id=2";WHERE Username LIKE '$query'

        try {
            $statment = $this->pdo->prepare("UPDATE myfiles SET todo=false WHERE filename LIKE '%.xml.%'");


        } catch (Exception $e)
        {
            //var_dump($e);
        }
        $statment->execute();
    }
}