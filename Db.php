<?php

class Db
{
    protected $connection;

    public function __construct()
    {
        //подключаемся к бд при каждом создании объекта
        $this->connection = new PDO ("mysql:host=localhost; dbname=gallery; charset=utf8", 'root', '', array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
    }

    //Метод добавления картинку в бд
    public function addData($sql, $data)
    {
        $query = $this->connection->prepare($sql);
        $query->execute($data);
    }

    //получить все
    public function queryAll($sql)
    {
        $query = $this->connection->prepare($sql);
        $query->execute();
        return $query->fetchAll();
    }

    //Получить с доп параметром
    public function getOneImage($sql, $data)
    {
        $query = $this->connection->prepare($sql);
        $query->execute($data);
        return $query->fetchAll();
    }


}