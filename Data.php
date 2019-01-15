<?php
require __DIR__ . '/Db.php';

class Data
{
    //добавить картинку в бд
    static function addImage($fileName, $fileExtension)
    {
        $addComment = new Db;
        $sqlAdd = "INSERT INTO `image` SET `name`=:fileName, `extension`=:fileExtension";
        $data = ['fileName' => $fileName, 'fileExtension' => $fileExtension];
        $addComment->addData($sqlAdd, $data);
    }
    //Получить последний ид
    static function getLastId()
    {
        $getId = new Db;
        $sql = "SELECT MAX(`id`) FROM `image`";
        return $getId->queryAll($sql);
    }
    //Получить все картинки
    static function getImages()
    {
        $images = new Db;
        $sql = 'SELECT * FROM `image`';
        return $images->queryAll($sql);
    }

    //Удалить картинку
    static function delImages($id)
    {
        $image = new Db;
        $sql = "DELETE FROM `image` WHERE `id`=:id";
        $id = ['id' => $id];
        $image->addData($sql, $id);
    }

    //Получить удаляемую картинку
    static function getDelImage($id)
    {
        $image = new Db;
        $sql = "SELECT * FROM `image` WHERE `id`=:id";
        $id = ['id' => $id];
        return $image->getOneImage($sql, $id);
    }


}
