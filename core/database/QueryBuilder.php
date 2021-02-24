<?php


class QueryBuilder
{
    protected $pdo;


    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }


    public function query($sql)
    {

        try {
            $statement = $this->pdo->prepare($sql);
            $statement->execute();
            return $statement;
        } catch (Exception $e) {
            echo $e->getMessage();
            die('query non funziona ....');
        }

    }

    public function query_all($sql)
    {
        return $this->query($sql)->fetchAll(PDO::FETCH_OBJ);
    }

    public function query_first($sql)
    {
        return ($this->query_all($sql)) ? $this->query_all($sql)[0] : 0;
    }

}