<?php
require __DIR__ . '/Data.php';
//Проверка нажатия удалить
if($_POST['status'] == 'delete') {
    //Получаем из бд удаляемую картинку
    $deleteImage = Data::getDelImage($_POST['id']);
    $pathDelImage = __DIR__ . "/uploads/" . $deleteImage[0]['id'] . $deleteImage[0]['name'] . '.' . $deleteImage[0]['extension'];
    //удаляем из бд
    Data::delImages($_POST['id']);
    //удаляем файл с диска
    if (file_exists($pathDelImage)) {
        unlink($pathDelImage);
        header('Location: index.php', true, 301);
    } else {
        echo "<div class=\"alert alert-danger\" role=\"alert\"> Что-то пошло не так </div>";
    }
    unset($_POST);
}
if (isset($_POST['submit'])) {
    //Проходим по массиву с файлами
    foreach ($_FILES as $file) {
        //Если пусто, то пропускаем
        if ($file['name'] == '') {
            continue;
        }
        //записываем полученное
        $fileName = $file['name'];
        $fileName = $words = preg_replace('/[0-9]/', '', $fileName);
        $fileType = $file['type'];
        $fileTmpName = $file['tmp_name'];
        $fileError = $file['error'];
        $fileSize = $file['size'];
        //получаем расширение
        $fileExtension = strtolower(end(explode('.', $fileName)));
        $fileName = explode('.', $fileName);
        //Удлаляем расширение из названия и затем присоединяем, чтобы не удалилась точка
        array_pop($fileName);
        $fileName = implode('.', $fileName);
        $allowedExtension = ['jpg', 'jpeg', 'png'];
        //проверка на расширение, размер файла и ошибки
        if (in_array($fileExtension, $allowedExtension)) {
            if ($fileSize < 5000000) {
                if ($fileError === 0) {
                    //Добавляем в бд, получаем ид и добавляем к имени файла, записываем на диск
                    Data::addImage($fileName, $fileExtension);
                    $lastId = Data::getLastId();
                    $lastId = $lastId[0][0];
                    $fileNameNew = $lastId . $fileName . '.' . $fileExtension;
                    $fileDestination = __DIR__ . '/uploads/' . $fileNameNew;
                    move_uploaded_file($fileTmpName, $fileDestination);
                    echo "<div class=\"alert alert-success .lert-dismissible\" role=\"alert\"> Файл успешно загружены </div>";
                } else {
                    echo "<div class=\"alert alert-danger\" role=\"alert\"> Что-то пошло не так </div>";
                }
            } else {
                echo "<div class=\"alert alert-danger\" role=\"alert\"> Превышен максимальный размер файла, пожалуйста, загрузите файл меньше 5 мб </div>";
            }
        } else {
            echo "<div class=\"alert alert-danger\" role=\"alert\"> Неверный тип файла </div>";
        }
    }
    unset($_POST);
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Lesson 12 </title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css"
          integrity="sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS" crossorigin="anonymous">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css" integrity="sha384-UHRtZLI+pbxtHCWp1t77Bi1L4ZtiqrqD80Kn4Z8NTSRyMA2Fd33n5dQ8lWUE00s/" crossorigin="anonymous">
    <!-- fancyBox3 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.6/dist/jquery.fancybox.min.css"/>
    <style>
        .thumb {
            position: relative;
        }
        .thumb img {
            -webkit-filter: grayscale(0);
            max-height: 300px;
            max-width: 300px;
            filter: none;
            border-radius: 5px;
            background-color: #fff;
            border: 1px solid #ddd;
            padding: 5px;
            position: relative;
        }

        .thumb img:hover {
            -webkit-filter: grayscale(1);
            filter: grayscale(1);
            padding: 0;
        }
        .form-button {
            top: 10px;
            display: none;
            position: absolute;
            border: none;
        }

        .btn-del:hover {
            font-size: larger;
            color: crimson;
        }

        .btn-del {
            color: crimson;
        }

        button,
        button:active,
        button:focus {
            outline: 0;
        }

        *{
            outline: none !important
        }

        .wrapp:hover + .form-button, .form-button:hover {
            display: inline-block;
        }
        .thumb {
            padding: 5px;
        }

        .form {
            padding: 10px 20px 5px 20px;
        }

        .form-wrapper {
            margin-bottom: 50px;
        }
    </style>
</head>
<body>
<div class="container">
    <h1 class="h3 text-center my-4">Gallery</h1>
    <div class="dropdown form-wrapper">
        <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenu" data-toggle="dropdown"
                aria-haspopup="true" aria-expanded="false">
            Загрузить картинки в галерею
        </button>
        <div class="dropdown-menu" aria-labelledby="dropdownMenu">
            <form action="" method="post" class="form form-group" enctype="multipart/form-data">
                <input name="photo-1" class="" type="file">
                <input name="photo-2" class="" type="file">
                <input name="photo-3" class="" type="file">
                <button class="btn  btn-info" name="submit" value="submit" type="submit">Отправить</button>
            </form>
        </div>
    </div>
    <div class="row justify-content-around">
        <?php foreach (Data::getImages() as $image):
            $thisImage = '/uploads/'. $image['id'] . $image['name'] . '.' . $image['extension'];
        ?>
            <div class="col-lg-3 col-md-4 col-6 thumb row">
                <a class="wrapp" data-fancybox="gallery" href="<?= $thisImage ?>">
                    <img class="img-fluid img-responsive col align-self-center" src="<?= $thisImage ?>" alt="...">
                </a>
                <form action="" class="form-button" method="post">
                    <input type="hidden" value="<?= $image['id'] ?>" name="id">
                    <input type="hidden" name="status" value="delete">
                    <button name="delete" class="btn btn-del btn-outline-*"><i class="fa fa-trash" aria-hidden="true"></i></button>
                </form>

            </div>

    <?php endforeach;?>
    </div>
</div>
<!-- jQuery -->
<script src="//code.jquery.com/jquery-3.3.1.min.js"></script>
<!-- Popper -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js"
        integrity="sha384-wHAiFfRlMFy6i5SRaxvfOCifBUQy1xHdJ/yoi7FRNXMRBu5WHdZYu1hA6ZOblgut"
        crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js"
        integrity="sha384-B0UglyR+jN6CkvvICOB2joaf5I4l3gm9GU6Hc1og6Ls7i6U/mkkaduKaBhlAXv9k"
        crossorigin="anonymous"></script>
<!-- fancyBox -->
<script src="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.6/dist/jquery.fancybox.min.js"></script>
</body>
</html>