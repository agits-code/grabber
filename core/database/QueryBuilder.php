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

    public function insert($filename,$filedate,$filesize,$md5,$link)
    {
        //

        try {
          $statment = $this->pdo->prepare("INSERT INTO myfiles (filename , filedate , filesize , md5 , link )
        VALUES ($filename,
         $filedate,
         $filesize,
         $md5,
         $link)");
          $statment->execute();
        } catch (Exception $e)
        {
           die($e);
        }
    }
}