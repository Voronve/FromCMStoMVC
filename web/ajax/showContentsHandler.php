<?php
use application\models\CMSArticle;

$CMSArticle = new CMSArticle();

if (isset($_GET['articleId'])) {
    $article = $CMSArticle->getById((int)$_GET['articleId']);
    echo json_encode($article);
}else if (isset($_POST['articleId'])) {
    $article = $CMSArticle->getById( $_POST['articleId'] );
    echo json_encode($article);
//        die("Привет)");
//    $conn = new PDO( DB_DSN, DB_USERNAME, DB_PASSWORD );
//    
//        if (isset($conn)) {
//            die("Соединенте установлено");
//        }
//        else {
//            die("Соединение не установлено");
//        }
//    $article = "WHERE Id=". (int)$_POST[articleId];
//    echo $article;
//    $sql = "SELECT content FROM articles". $article;
//    $contentFromDb = $conn->prepare( $sql );
//    $contentFromDb->execute();
//    $result = $contentFromDb->fetch();
//    $conn = null;
//    echo json_encode($result);
}

